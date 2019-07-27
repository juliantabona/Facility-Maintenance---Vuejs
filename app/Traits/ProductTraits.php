<?php

namespace App\Traits;

use DB;
use App\Document;

//  Notifications
use App\Company;
use App\Notifications\ProductCreated;
use App\Notifications\ProductUpdated;

trait ProductTraits
{

    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of product results.
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
         *  $productType = physical, virtual
        /*
         *  The $productType variable is used to determine which type of product to pull.
         *  The user may request data of type.
         *  1) physical: A product that is listed as a physical entity
         *  2) virtual: A product that is listed as a virtual entity
         */
        $productType = strtolower(request('productType'));

        /*
         *  $companyId = 1, 2, 3, e.t.c
        /*
         *  The $companyId variable only get data specifically related to
         *  the specified company id. It is useful for scenerios where we
         *  want only products of that company only
         */
        $companyId = request('companyId');

        if( isset($companyId) && !empty($companyId) ){

            //  Only get specific company data only if specified
            if ($companyId) {
                /************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY PRODUCTS    *
                /***********************************************************************/

                $model = Company::find($companyId);

            }

        }else{

            //  Apply filter by allocation
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL PRODUCTS         *
                /**********************************************************/

                //  Get the current product instance
                $model = $this;

            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH PRODUCTS    *
                /*************************************************************/

                // Only get products associated to the company branch
                $model = $auth_user->companyBranch;
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY PRODUCTS    *
                /**************************************************************/

                //  Only get products associated to the company
                $model = $auth_user->company;
            }

        }

        /*  If user indicated to only return specific types of products
        */
        if ($productType == 'physical') {
            $products = $model->onlyProducts();

        /*  If user indicated to only return virtual products
        */
        } elseif ($productType == 'virtual') {
            $products = $model->onlyServices();

        /*  If user did not indicate any specific group
        */
        } else {

            if( isset($productTypes) && !empty( $productTypes ) ){

                //  If the $productType is a list e.g) virtual,pysical, ... e.t.c
                $productTypes = explode(',', $productType );

                if (count($productTypes)) {
                    //  Get products listed
                    $products = $model->productAndServices()->whereIn('type', $productTypes);
                }

            } else {
                //  Otherwise get all products
                $products = $model->productAndServices();
            }
        }


        //  Only get specific company data only if specified
        if ($companyId) {
            /************************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY PRODUCTS    *
            /***********************************************************************/

            $products = $products->where('company_id', $companyId);
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
                $products = $products->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $products = $products->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $products = $products->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we only want to know how many were returned
            if( request('count') == 1 ){
                //  If the products are paginated
                if($config['paginate']){
                    $products = $products->total() ?? 0;
                //  If the products are not paginated
                }else{
                    $products = $products->count() ?? 0;
                }
            }else{
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $products = $products->get();
                }

                //  If we have any products so far
                if ($products) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $products->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $products];

        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }


    /*  initiateShow() method:
     *
     *  This is used to return only one specific product.
     *
     */
    public function initiateShow($product_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        try {
            //  Get the trashed product
            if (request('withtrashed') == 1) {
                //  Run query
                $product = $this->withTrashed()->where('id', $product_id)->first();

            //  Get the non-trashed product
            } else {
                //  Run query
                $product = $this->where('id', $product_id)->first();
            }

            //  If we have any product so far
            if ($product) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $product->load(oq_url_to_array(request('connections')));
                }

                //  Action was executed successfully
                return ['success' => true, 'response' => $product];
            } else {
                //  No resource found
                return ['success' => false, 'response' => oq_api_notify_no_resource()];
            }
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateCreate() method:
     *
     *  This is used to create a new product. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the product.
     *
     */
    public function initiateCreate($template = null)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE PRODUCT    *
         ******************************************************/

        /*********************************************
         *   VALIDATE PRODUCT INFORMATION            *
         ********************************************/
        
        //  Create a template to hold the product details
        $template = $template ?? [
            //  General details
            'name' => request('name'),
            'description' => request('description') ?? null,
            'type' => request('type') ?? null,
            
            //  Pricing details
            'cost_per_item' => request('cost_per_item') ?? 0,
            'unit_price' => request('unit_price') ?? 0,
            'unit_sale_price' => request('unit_sale_price') ?? 0,

            //  Inventory & Tracking details
            'sku' => request('sku') ?? null,
            'barcode' => request('barcode') ?? null,
            'stock_quantity' => request('stock_quantity') ?? null,
            'allow_inventory' => request('allow_inventory'),
            'auto_track_inventory' => request('auto_track_inventory'),
            
            //  Variant details
            'variants' => request('variants') ?? null,
            'variant_attributes' => request('variant_attributes') ?? null,
            'allow_variants' => request('allow_variants'),
            
            //  Download Details
            'allow_downloads' => request('allow_downloads'),

            //  Store Details
            'show_on_store' => request('show_on_store'),

            //  Ownership Details
            'company_branch_id' => $auth_user->company_branch_id ?? null,
            'company_id' => $auth_user->company_id ?? null,
        ];

        try {
            //  Create the product
            $product = $this->create($template)->fresh();

            //  If the product was created successfully
            if ($product) {

                //  Check whether or not the product has any image to upload
                $this->checkAndUploadImage($product);

                //  Check whether or not the product has any categories to add
                $this->checkAndCreateCategories($product);

                //  Check whether or not the product has any tags to add
                $this->checkAndCreateTags($product);

                //  refetch the updated product
                $product = $product->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new ProductCreated($product));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of product created
                $status = 'created';
                $productCreatedActivity = oq_saveActivity($product, $auth_user, $status, ['product' => $product->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $product];
            } else {
                //  No resource found
                return ['success' => false, 'response' => oq_api_notify_no_resource()];
            }
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    public function checkAndCreateCategories($product)
    {
        $auth_user = auth('api')->user();

        /*  categories:
         *  This is a variable used to determine if the current product being created has 
         *  categories. Sometimes when creating a new product, we may want to add categories
         *  to that product. We can do this if the relationship variable has been set with
         *  the appropriate type (client/supplier)
         */
        $categories = request('categories') ?? null;

        if (isset($categories) && !empty($categories)) {

            //  $categories = [1,2,3, ...] ids of new categories 
            $categories = json_decode($categories) ?? [];

            //  Delete any previously assigned categories
            DB::table('category_allocations')
                ->where('trackable_id', $product->id)
                ->where('trackable_type', 'product')
                ->delete();
                                    
            //  Add new categories
            foreach($categories as $category){

                //  Add to category allocations
               DB::table('category_allocations')->insert([
                    'category_id' => $category, 
                    'trackable_id' => $product->id,                       
                    'trackable_type' => 'product',                       
                    'created_at' => DB::raw('now()'),                       
                    'updated_at' => DB::raw('now()')
                ]);

            }
        }
    }

    public function checkAndCreateTags($product)
    {
        $auth_user = auth('api')->user();

        /*  tags:
         *  This is a variable used to determine if the current product being created has 
         *  tags. Sometimes when creating a new product, we may want to add tags
         *  to that product. We can do this if the relationship variable has been set with
         *  the appropriate type (client/supplier)
         */
        $tags = request('tags') ?? null;

        if (isset($tags) && !empty($tags)) {

            //  $tags = [1,2,3, ...] ids of new tags 
            $tags = json_decode($tags) ?? [];

            //  Delete any previously assigned tags
            DB::table('tag_allocations')
                ->where('trackable_id', $product->id)
                ->where('trackable_type', 'product')
                ->delete();
                                    
            //  Add new tags
            foreach($tags as $tag){

                //  Add to tag allocations
               DB::table('tag_allocations')->insert([
                    'tag_id' => $tag, 
                    'trackable_id' => $product->id,                       
                    'trackable_type' => 'product',                       
                    'created_at' => DB::raw('now()'),                       
                    'updated_at' => DB::raw('now()')
                ]);

            }
        }
    }

    public function checkAndUploadImage($product)
    {
        /*  primary_image:
         *  This is a variable used to determine if the current product being created has
         *  an image file to upload. Sometimes when creating a new product, we may want to 
         *  also upload the primary image (featured image) at the same time. We can do this 
         *  if the primary_image variable has been set with the image file (type=binary)
         */
        $File = request('primary_image');

        if (isset($File) && !empty($File) && request()->hasFile('primary_image')) {

            //  Start upload process of files
            $data = ( new Document() )->saveDocument( request(), $product->id, 'product', $File, 'products', 'primary', true );

        }
    }

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing product. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the product.
     *
     */
    public function initiateUpdate($product_id)
    {

        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE PRODUCT    *
         ******************************************************/

        /*********************************************
         *   VALIDATE PRODUCT INFORMATION            *
         ********************************************/

        //  Create a template to hold the product details
        $template = $template ?? [
            //  General details
            'name' => request('name'),
            'description' => request('description') ?? null,
            'type' => request('type') ?? null,
            
            //  Pricing details
            'cost_per_item' => request('cost_per_item') ?? 0,
            'unit_price' => request('unit_price') ?? 0,
            'unit_sale_price' => request('unit_sale_price') ?? 0,

            //  Inventory & Tracking details
            'sku' => request('sku') ?? null,
            'barcode' => request('barcode') ?? null,
            'stock_quantity' => request('stock_quantity') ?? null,
            'allow_inventory' => request('allow_inventory'),
            'auto_track_inventory' => request('auto_track_inventory'),
            
            //  Variant details
            'variants' => request('variants') ?? null,
            'variant_attributes' => request('variant_attributes') ?? null,
            'allow_variants' => request('allow_variants'),
            
            //  Download Details
            'allow_downloads' => request('allow_downloads'),

            //  Store Details
            'show_on_store' => request('show_on_store'),

            //  Ownership Details
            'company_branch_id' => $auth_user->company_branch_id ?? null,
            'company_id' => $auth_user->company_id ?? null,
        ];

        try {
            //  Update the product
            $product = $this->where('id', $product_id)->first()->update($template);

            //  If the product was updated successfully
            if ($product) {

                //  re-retrieve the instance to get all of the fields in the table.
                $product = $this->where('id', $product_id)->first();

                //  Check whether or not the product has any image to upload
                $this->checkAndUploadImage($product);

                //  Check whether or not the product has any categories to add
                $this->checkAndCreateCategories($product);

                //  Check whether or not the product has any tags to add
                $this->checkAndCreateTags($product);

                //  refetch the updated product
                $product = $product->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new ProductUpdated($product));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of product updated
                $status = 'updated';
                $productUpdatedActivity = oq_saveActivity($product, $auth_user, $status, ['product' => $product->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $product];
            } else {
                //  No resource found
                return ['success' => false, 'response' => oq_api_notify_no_resource()];
            }
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  summarize() method:
     *
     *  This is used to limit the information of the resource to very specific
     *  columns that can then be used for storage. We may only want to summarize
     *  the data to very important information, rather than storing everything along
     *  with useless information. In this instance we specify table columns
     *  that we want (we access the fillable columns of the model), while also
     *  removing any custom attributes we do not want to store
     *  (we access the appends columns of the model),
     */
    public function summarize()
    {
        //  Collect and select table columns
        return collect($this->fillable)
                //  Remove all custom attributes since the are all based on recent activities
                ->forget($this->appends);
    }
}
