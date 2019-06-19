<?php

namespace App\Http\Controllers\Api;

use App\Company;
use App\CompanyBranch;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();

        //  We start with no products and services
        $productsAndServices = [];

        //  Query data
        $modelType = request('model', 'company');           //  e.g) company, branch, all
        $productOrServiceType = request('type', 'all');     //  e.g) product, service, all
        $model_id = request('modelId');                     //  The id of the company/branch for getting related products/services

        /*  First thing is first, we need to understand one of 2 scenerios, Either we want:
         *
         *  1) Only products or services for a related COMPANY of the authenticated user
         *  2) Only products or services for a related BRANCH by using a unique identifyer
         *
         *  Once we have those products or services we will determine whether we want any of the following
         *
         *  3) Only products
         *  4) Only services
         *  4) Both products and services
         *
         *  We will then determine whether we want any of the following
         *
         *  1) All products/services aswell as the trashed ones
         *  2) Only products/services that are trashed
         *  3) Only products/services that are not trashed
         *
         *  After this we will perform our filters, e.g) where, orderby, e.t.c
         *
         */

        /*  User Company specific products or services
         *  If the user indicated that they want products or services related to their company,
         *  or any products or services related to a specified company of their choice.
         *  They must indicate using the query "model" set to "company" and they can
         *  set "model_id" to specify an exact company id to target.
         */
        if ($modelType == 'company') {
            //  Check if the user specified the "model_id"
            if (empty($model_id)) {
                /***********************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW SPECIFIED COMPANY PRODUCTS OR SERVICES  *
                /***********************************************************************************/

                $companyId = $user->companyBranch->company->id;
            } else {
                /***********************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW THEIR COMPANY PRODUCTS OR SERVICES      *
                /***********************************************************************************/

                $companyId = $model_id;
            }

            $company = Company::where('id', $companyId)->first();

            if ($company) {
                $products = $company->productAndServices();
            } else {
                return oq_api_notify_error('Specified company was not found', 1, 404);
            }

            /*  User Branch specific products or services
            *  If the user indicated that they want products or services related to their branch,
            *  or any products or services related to a specified branch of their choice.
            *  They must indicate using the query "model" set to "branch" and they can
            *  set "model_id" to specify an exact branch id to target.
            */
        } elseif ($modelType == 'branch') {
            //  Check if the user specified the "model_id"
            if (empty($model_id)) {
                /***********************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW SPECIFIED BRANCH PRODUCTS OR SERVICES   *
                /***********************************************************************************/

                $branchId = $user->company_branch_id;
            } else {
                /***********************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO VIEW THEIR BRANCH PRODUCTS OR SERVICES       *
                /***********************************************************************************/

                $branchId = $model_id;
            }

            $branch = CompanyBranch::where('id', $branchId)->first();

            if ($branch) {
                $products = $branch->productAndServices();
            } else {
                return oq_api_notify_error('Specified branch was not found', null, 404);
            }

            /*  ALL products or services
             *  If the user wants all the products or services in the system. They must indicate
             *  using the query "model" set to "all". This is normaly used by authorized
             *  superadmins to access all products or services resources in the system.
             */
        } elseif ($modelType == 'all') {
            /***********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO VIEW ALL PRODUCTS OR SERVICES    *
            /***********************************************************************/

            /*   Create a new Product() instance that can be used to retrieve all products or services
             */
            $products = new Product();
        } else {
            return oq_api_notify_error('Model query must be "company" or "branch". Provided model is incorrect', null, 404);
        }

        /*  If the user specified to get specific types of products or services,
         *  e.g) Get only products / Get only services
         */
        if ($productOrServiceType == 'product' || $productOrServiceType == 'service') {
            $products = $products->where('type', $productOrServiceType);
        } elseif ($productOrServiceType != 'all') {
            return oq_api_notify_error('Type query must be "product" or "service". Provided type is incorrect', null, 404);
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

        $order_join = 'products_and_services';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $products = $products->withTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $products = $products->onlyTrashed()->advancedFilter(['order_join' => $order_join]);
            //  Get all except trashed
            } else {
                //  Run query
                $products = $products->advancedFilter(['order_join' => $order_join]);
            }

            //  If we have any products or services so far
            if ($products) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $products->load(oq_url_to_array(request('connections')));
                }
            }
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  Action was executed successfully
        return oq_api_notify($products, 200);
    }

    public function show($product_id)
    {
        //  Product Instance
        $data = ( new Product() )->initiateShow($product_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the product was found successfully
        if ($success) {
            //  If this is a success then we have the product
            $product = $response;

            //  Action was executed successfully
            return oq_api_notify($product, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
    {
        //  Start creating the product
        $data = ( new Product() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the product was created successfully
        if ($success) {
            //  If this is a success then we have a product returned
            $product = $response;

            //  Action was executed successfully
            return oq_api_notify($product, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($product_id)
    {
        //  Product Instance
        $data = ( new Product() )->initiateUpdate($product_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the product was updated successfully
        if ($success) {
            //  If this is a success then we have a product returned
            $product = $response;

            //  Action was executed successfully
            return oq_api_notify($product, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function getImage(Request $request, $product_id)
    {
        try {
            //  Get the associated product
            $product = Product::where('id', $product_id)->first();
            $productImage = $product->primaryImage;

            //  Action was executed successfully
            return oq_api_notify($productImage, 200);

        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }

        //  No resource found
        return oq_api_notify_no_resource();
    }

}
