<?php

namespace App\Traits;

use App\Product;
use App\Company;

trait CategoryTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of category results.
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
         *
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
         *  $categoryType = product, appointment, jobcard, e.t.c
         *
         *  The $categoryType variable is used to determine which type of category to pull.
         *  The user may request data of type.
         *  1) product: A categories related to only products
         *  2) appointment: A categories related to only appointments
         *  and so on...
         */
        $categoryType = strtolower(request('categoryType'));

        /*
         *  $companyId = 1, 2, 3, e.t.c
         *
         *  The $companyId variable only get data specificaclly related to
         *  the specified company id. It is useful for scenerios where we
         *  want only categories of that company only
         */
        $companyId = request('companyId');

        /*
         *  $productId = 1, 2, 3, e.t.c
         *
         *  The $productId variable only get data specificaclly related to
         *  the specified product id. It is useful for scenerios where we
         *  want only categories of that product only
         */
        $productId = request('productId');

        if( isset($companyId) && !empty($companyId) ){

            //  Only get specific company data only if specified
            if ($companyId) {
                /************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY PRODUCTS    *
                /***********************************************************************/

                $categories = Company::find($companyId)->categories();
            }

        }else if( isset($productId) && !empty($productId) ){

            //  Only get specific product data only if specified
            if ($productId) {
                /************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED PRODUCT CATEGORIES  *
                /***********************************************************************/

                $categories = Product::find($productId)->categories();
            }

        }else{

            //  Apply filter by allocation
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL PRODUCTS         *
                /**********************************************************/

                //  Get the current product instance
                $categories = $this;

            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH PRODUCTS    *
                /*************************************************************/

                // Only get products associated to the company branch
                $categories = $auth_user->companyBranch->categories();
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY PRODUCTS    *
                /**************************************************************/

                //  Only get products associated to the company
                $categories = $auth_user->company->categories();
            }

        }
        
        //  Flter to the $categoryType type
        if ($categoryType) {
            //  If the $categoryType is a list e.g) product,appointment
            $type = explode(',', $categoryType);

            if (count($type)) {
                $categories = $categories->whereIn('type', $type);
            } else {
                $categories = $categories->where('type', $categoryType);
            }
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

        $order_join = 'categories';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $categories = $categories->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $categories = $categories->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $categories = $categories->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we only want to know how many were returned
            if( request('count') == 1 ){
                //  If the categories are paginated
                if($config['paginate']){
                    $categories = $categories->total ?? 0;
                //  If the categories are not paginated
                }else{
                    $categories = $categories->count() ?? 0;
                }
            }else{
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $categories = $categories->get();
                }

                //  If we have any categories so far
                if ($categories) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $categories->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $categories];
            
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }
}
