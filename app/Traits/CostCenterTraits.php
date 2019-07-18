<?php

namespace App\Traits;

trait CostCenterTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of costcenter results.
     *
     */
    public function initiateGetAll($options = array())
    {
        //  Default settings
        $defaults = array(
            'paginate' => false,
        );

        //  Replace defaults with any provided options
        $config = array_merge($defaults, $options);

        //  If we overide using the request
        $requestPagination = request('paginate');
        if (isset($requestPagination) && ($requestPagination == 0 || $requestPagination == 1)) {
            $config['paginate'] = $requestPagination == 1 ? true : false;
        }

        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  $allocation = all, company, branch
        /*
         *  The $allocation variable is used to determine where the data is being
         *  pulled from. The user may request data from three possible sources.
         *  1) Data may come from the associated authenticated user branch
         *  2) Data may come from the associated authenticated user company
         *  3) Data may come from the whole bucket meaning outside the scope of the
         *     authenticated user. This means we can access all possible records
         *     available. This is usually useful for users acting as superadmins.
         */
        $allocation = request('allocation');

        /*
         *  $modelType = jobcard, asset, e.t.c
        /*
         *  The $modelType variable only get data specifically related to
         *  the specified model. It is useful for scenerios where we
         *  want only costcenters of that model only.
         */
        $modelType = request('modelType');

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL COSTCENTERS         *
            /**********************************************************/

            //  Get the current costcenter instance
            $costcenters = $this;
        } elseif ($allocation == 'branch') {
            /*************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH COSTCENTERS    *
            /*************************************************************/

            // Only get costcenters associated to the company branch
            $costcenters = $auth_user->companyBranch->costcenters();
        } else {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY COSTCENTERS    *
            /**************************************************************/

            //  Only get costcenters associated to the company
            $costcenters = $auth_user->company->costcenters();
        }

        //  Flter to the model type
        if ($modelType) {
            $costcenters = $costcenters->where('type', $modelType);
        }

        /*  To avoid sql order_by error for ambigious fields e.g) created_at
         *  we must specify the order_join.
         *
         *  Order joins help us when using the "advancedFilter()" method. Usually
         *  we need to specify the joining table so that the system is not confused
         *  by similar column names that exist on joining tables. E.g) the column
         *  "created_at" can exist in multiple table and the system might not know
         *  whether the "order_by" is for table_1 created_at or table 2 created_at.
         *  By specifying this we end up with "table_1.created_at"
         *
         *  If we don't have any special order_joins, lets default it to nothing
         */

        $order_join = 'cost_centers';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $costcenters = $costcenters->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $costcenters = $costcenters->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get all except trashed
            } else {
                //  Run query
                $costcenters = $costcenters->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            }

            //  If we are not paginating then
            if (!$config['paginate']) {
                //  Get the collection
                $costcenters = $costcenters->get();
            } else {
                $costcenters = $costcenters->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  Eager load other relationships wanted if specified
            if (request('connections')) {
                $costcenters->load(oq_url_to_array(request('connections')));
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $costcenters];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }
}
