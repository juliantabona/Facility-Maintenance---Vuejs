<?php

namespace App\Traits;

trait PriorityTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of priority results.
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
        if (request('paginate') == 0 || request('paginate') == 1) {
            $config['paginate'] = request('paginate') == 1 ? true : false;
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
         *     authenticated user. This means we can access all possible models
         *     available. This is usually useful for users acting as superadmins.
         */
        $allocation = request('allocation');

        /*
         *  $modelType = jobcard, asset, e.t.c
        /*
         *  The $modelType variable only get data specificaclly related to
         *  the specified model. It is useful for scenerios where we
         *  want only priorities of that model only.
         */
        $modelType = request('modelType');

        //  Apply filter by allocation
        if ($allocation == 'all') {
            /***********************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO ALL PRIORITIES         *
            /**********************************************************/

            //  Get the current priority instance
            $priorities = $this;
        } elseif ($allocation == 'branch') {
            /*************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH PRIORITIES    *
            /*************************************************************/

            // Only get priorities associated to the company branch
            $priorities = $auth_user->companyBranch->priorities();
        } else {
            /**************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY PRIORITIES    *
            /**************************************************************/

            //  Only get priorities associated to the company
            $priorities = $auth_user->company->priorities();
        }

        //  Flter to the model type
        if ($modelType) {
            $priorities = $priorities->where('type', $modelType);
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

        $order_join = 'priorities';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $priorities = $priorities->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $priorities = $priorities->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            //  Get all except trashed
            } else {
                //  Run query
                $priorities = $priorities->advancedFilter(['order_join' => $order_join, 'paginate' => false]);
            }

            //  If we are not paginating then
            if (!$config['paginate']) {
                //  Get the collection
                $priorities = $priorities->get();
            } else {
                $priorities = $priorities->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  Eager load other relationships wanted if specified
            if (request('connections')) {
                $priorities->load(oq_url_to_array(request('connections')));
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $priorities];
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }
}
