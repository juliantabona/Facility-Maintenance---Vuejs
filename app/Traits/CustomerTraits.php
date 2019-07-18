<?php

namespace App\Traits;

use App\Document;

//  Notifications
use App\Company;

trait CustomerTraits
{

    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of customer results.
     *
     */
    public function initiateGetAll($options = array())
    {
        //  Default settings
        $defaults = array(
            'paginate' => true,
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
         *  $customerType = physical, virtual
        /*
         *  The $customerType variable is used to determine which type of customer to pull.
         *  The user may request data of type.
         *  1) company: A customer that is listed as a company
         *  2) contact: A customer that is listed as a user
         */
        $customerType = strtolower(request('customerType'));

        /*
         *  $companyId = 1, 2, 3, e.t.c
        /*
         *  The $companyId variable only get customers specifically related to
         *  the specified company id. It is useful for scenerios where we
         *  want only customers of that company
         */
        $companyId = request('companyId');

        if( isset($companyId) && !empty($companyId) ){

            //  Only get specific company data only if specified
            if ($companyId) {
                /************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY CUSTOMERS   *
                /***********************************************************************/

                $model = Company::find($companyId);
            }

        }else{

            //  Apply filter by allocation
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL CUSTOMERS         *
                /**********************************************************/

                //  Get the company instance
                $model = new Company();

            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH CUSTOMERS    *
                /*************************************************************/

                // Only get customers associated to the company branch
                $model = $auth_user->companyBranch;
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY CUSTOMERS    *
                /**************************************************************/

                //  Only get customers associated to the company
                $model = $auth_user->company;
            }

        }

        /*  If user indicated to only return specific types of customers
        */
        if ($customerType == 'company') {
            $customerArray = ['companies' => $model->companyClients()];

        /*  If user indicated to only return virtual customers
        */
        } elseif ($customerType == 'user') {
            $customerArray = ['users' => $model->userClients()];

        /*  If user did not indicate any specific group
        */
        } else {
            //  Otherwise get all customers
            $customerArray = [
                'users' => $model->userClients(),
                'companies' => $model->companyClients(),
            ];
        }

        //  Only get specific company data only if specified
        if ($companyId) {
            /************************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY CUSTOMERS    *
            /***********************************************************************/
            foreach($customerArray as $key => $customers){
                $customerArray[$key] = $customers->where('company_id', $companyId);
            }
            
        }

        try {

            foreach($customerArray as $key => $customers){

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
                if($key == 'users'){
                    $order_join = 'user_directory';
                }elseif($key == 'companies'){
                    $order_join = 'company_directory';
                }
                
                //  Get all and trashed
                if (request('withtrashed') == 1) {
                    //  Run query
                    $customerArray[$key] = $customers->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
                //  Get only trashed
                } elseif (request('onlytrashed') == 1) {
                    //  Run query
                    $customerArray[$key] = $customers->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
                //  Get all except trashed
                } else {
                    //  Run query
                    $customerArray[$key] = $customers->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
                }

                //  If we only want to know how many were returned
                if( request('count') == 1 ){
                    //  If the customers are paginated
                    if($config['paginate']){
                        $customerArray[$key] = $customers->total() ?? 0;
                    //  If the customers are not paginated
                    }else{
                        $customerArray[$key] = $customers->count() ?? 0;
                    }
                }else{
                    //  If we are not paginating then
                    if (!$config['paginate']) {
                        //  Get the collection
                        $customerArray[$key] = $customers->get();
                    }

                    //  If we have any customers so far
                    if ($customerArray[$key]) {
                        //  Eager load other relationships wanted if specified
                        if (strtolower(request('connections'))) {
                            $customerArray[$key]->load(oq_url_to_array(strtolower(request('connections'))));
                        }
                    }
                }

            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $customerArray];

        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

}
