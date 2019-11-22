<?php

namespace App\Traits;

use DB;
use App\Store;
use App\Company;
use App\Document;

//  Notifications
use App\Notifications\ProductCreated;
use App\Notifications\ProductUpdated;

//  Resources
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\Products as ProductsResource;

trait ProductTraits
{
    private $product;
    
    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($products = null)
    {

        try {

            if( $products ){
                
                //  Transform the products
                return new ProductsResource($products);

            }else{
                
                //  Transform the product
                return new ProductResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateUpdate() method =>
     *
     *  This method is used to update an existing
     *  product. The $productInfo variable represents 
     *  the product dataset provided
     */
    public function initiateUpdate( $productInfo = null )
    {
        /*
         *  The $productInfo variable represents accepted structure of the  
         *  product data required to update the current resource.
         */


        /*
         *  The $template variable represents structure of the product.
         *  If no template is provided, we create one using the
         *  request data.
         */
        $template = [
            /*  Basic Info  */
            'name' => $productInfo['name'] ?? null,
            'description' => $productInfo['description'] ?? null,
            'type' => $productInfo['type'] ?? null,
            'cost_per_item' => $productInfo['cost_per_item'] ?? null,
            'unit_regular_price' => $productInfo['unit_regular_price'] ?? null,
            'unit_sale_price' => $productInfo['unit_sale_price'] ?? null,
            'sku' => $productInfo['sku'] ?? null,
            'barcode' => $productInfo['barcode'] ?? null,
            'stock_quantity' => $productInfo['stock_quantity'] ?? null,
            'allow_stock_management' => $productInfo['allow_stock_management'] ?? null,
            'auto_manage_stock' => $productInfo['auto_manage_stock'] ?? null,
            'variant_attributes' => $productInfo['variant_attributes'] ?? null,
            'allow_variants' => $productInfo['allow_variants'] ?? null,
            'allow_downloads' => $productInfo['allow_downloads'] ?? null,
            'show_on_store' => $productInfo['show_on_store'] ?? null,
            'is_new' => $productInfo['is_new'] ?? null,
            'is_featured' => $productInfo['is_featured'] ?? null,
        ];

        try {
            
            /*
             *  Update the current product instance
             */
            $updated = $this->update($template);

            /*  If the product was updated successfully  */
            if ($updated) {

                /*  Return a fresh instance of the product  */
                return $this->fresh();

            }
        } catch (\Exception $e) {
            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }

    /*  initiateUpdate() method =>
     *
     *  This method is used to create new product variations
     *  for the current product. The $variantAttributeInfo   
     *  variable represents the variable attribute names
     *  and options from the dataset provided
     */
    public function initiateCreateVariations( $variantAttributeInfo = null )
    {
        try {
        
            if( $variantAttributeInfo ){

                /*  Get the product variations:
                 *  
                 * [
                 *   ["Small", "Blue", "Cotton"]
                 *   ["Small", "Blue", "Nylon"]
                 *   ["Small", "Red", "Cotton"]
                 *   ["Small", "Red", "Nylon"],
                 *   ...
                 * ]
                 * 
                 */
                $generated_variations = $this->generateVariations($variantAttributeInfo) ?? [];

                //  If we have any generated variations
                if( count($generated_variations) ){
    
                    $templates = [];

                    foreach($generated_variations as $key => $variation_options){
                        /*
                         *  If the main product is called "Summer Dress" and the
                         *  $variation_options=["Small", "Blue", "Cotton"]
                         * 
                         *  Then the variation product is named using both the parent
                         *  product name and the variation options. For example:
                         * 
                         *  "Summer Dress (Small, Blue, Cotton)"
                         * 
                         *  If the parent product id is 65 then this variation poduct
                         *  will have an sku in the format
                         * 
                         *  "65_small_blue_cotton"
                         */

                        $name = $this->name .' ('. ucwords(trim( is_array($variation_options) ? implode(', ', $variation_options) : $variation_options )).')';
    
                        //  Create an sku value
                        $sku = $this->id.'_'.strtolower(trim( is_array($variation_options) ? implode('_', $variation_options) : $variation_options ) );
    
                        /*
                         *  The $template variable represents structure of the product.
                         */
                        $template = [
    
                            /*  Basic Info  */
                            'name' => $name,
                            'description' => $this->description ?? null,
                            'type' => $this->type ?? null,
                            'sku' => $sku ?? null,
                            'parent_product_id' =>$this->id,
                            'owner_id' =>$this->owner_id,
                            'owner_type' =>$this->owner_type
    
                        ];
    
                        array_push($templates, $template);
    
                    }

                    /*
                     *  Delete all previous variations
                     */
                    $deletedProductVariations = $this->variations()->forceDelete();

                    /*
                     *  Create the new variations
                     */
                    $createdProductVariations = $this->insert($templates);

                    /*
                     *  Set the allow variants to true
                     */
                    $updatedProduct = $this->update([
                        'variant_attributes' => $variantAttributeInfo,
                        'allow_variants' => true
                    ]);

                    //  Get the created variations
                    $saved_variations = $this->variations()->get();

                    /*  We need an $variants array to store all the variants for each
                     *  product variation we just created 
                     */
                    $variants = [];

                    //  Foearch variation saved as a product
                    foreach($saved_variations as $x => $saved_variation){

                        //  Get the saved product id
                        $product_id = $saved_variation['id'];

                        /*  Get the generated variations. Sometimes each generated 
                         *  variation can be a single element or an array. We need 
                         *  to check for either case.
                         * 
                         *  A single element example:
                         * 
                         *   ~ Small
                         * 
                         *  An array example:
                         *  
                         *   ~ ["Small", "Blue", "Cotton"]
                         */
                        if(is_array($generated_variations[$x])){

                            /*  $generated_variations[$x] is array e.g:
                             *   ~ ["Small", "Blue", "Cotton"] or
                             *   ~ ["Small", "Blue", "Nylon"] or
                             *   ~ ["Small", "Red", "Cotton"] or
                             *   e.t.c ...
                             * 
                             *  This means we had multiple variant attributes e.g 
                             *   ~ "Size" with options "Small", "Medium", "Large",
                             *   ~ "Color" with options "Blue", "Red",
                             *   ~ "Size" with options "Cotton", "Nylon",
                             *   e.t.c
                             */
                            foreach($generated_variations[$x] as $y => $generated_variation_option){
                            
                                /*  We can get the name of the variable attribute that each
                                 *  $generated_variation belongs to e.g:
                                 *  name = Size, name = Color, e.t.c 
                                 */
                                $variable_attribute_name = $variantAttributeInfo[$y]['name'];

                                /*  Final result make be variants with details showing the variant
                                 *  attribute name, the option value and product it e.g =>
                                 *
                                 *  [ name => "Sizes", value => "Small", product_id => 82 ]
                                 *  [ name => "Color", value => "Blue", product_id => 82 ]
                                 *  [ name => "Material", value => "Cotton", product_id => 82 ]
                                 *  [ name => "Sizes", value => "Small", product_id => 83 ]
                                 *  [ name => "Color", value => "Blue", product_id => 83 ]
                                 *  [ name => "Material", value => "Nylon", product_id => 83 ]
                                 *  e.t.c
                                 * 
                                 *  We push each one into the $variants array for storage
                                 */
                                array_push($variants, [
                                    'name' => $variable_attribute_name,             //  E.g Size / Color / Material
                                    'value' => $generated_variation_option,         //  E.g Small / Blue / Cotton
                                    'product_id' => $saved_variations[$x]['id'],    //  E.g 10
                                ]);

                            }

                        }else{

                            /*  $generated_variations[$x] is a single element e.g:
                             *   ~ "Small" or
                             *   ~ "Medium" or
                             *   ~ "Large" or
                             *   e.t.c ...
                             * 
                             *  This means we only had one variant attribute e.g "Size"
                             *  with options e.g "Small", "Medium" or "Large"
                             */

                            /*  We can get the name of the variable attribute that each
                             *  $generated_variation belongs to e.g:
                             *  name = Size, name = Color, e.t.c 
                             */
                            $variable_attribute_name = $variantAttributeInfo[0]['name'];

                            array_push($variants, [
                                'name' => $variable_attribute_name,             //  E.g Size / Color / Material
                                'value' => $generated_variations[$x],         //  E.g Small / Blue / Cotton
                                'product_id' => $saved_variations[$x]['id'],    //  E.g 10
                            ]);
                        }
                        
                    }

                    if( count($variants) ){

                        //  Create the variants of the variations
                        $createdProductVariats = DB::table('variables')->insert($variants);

                    }
    
                }
    
            }
        } catch (\Exception $e) {
            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }

    public function generateVariations($variantAttributes, $level = 0){

        /*  If we have the following variant attributes
                ~ Size => [XS, S]
                ~ Color => [blue, red]
                ~ Material => [cotton, nylon]

            This function return the variant attribute option name in the 
            following format:
                ~ [XS, blue, cotton], [XS, blue, nylon], [XS, red, cotton], [XS, red, nylon]
                ~ [S, blue, cotton], [S, blue, nylon], [S, red, cotton], [S, red, nylon]
        */

        //  Get the valid variant attributes
        $validVariants = $this->validVariants($variantAttributes) ?? [];

        // Check if we have valid variants e.g sizes, colors, materials respectively
        if( count($validVariants) ){
            
            $variations = [];

            //  Foreach valid variant attribute e.g size, color, material
            for($x = $level; $x < count($validVariants); $x++){

                if( ($x == $level) ){

                    //  Get the variant attribute options e.g)
                    //  level = 0 : XS, SM, M, L, XL
                    //  level = 1 : Blue, Red
                    //  level = 2 : Cottom, Nylon
                    $variantOptions = $validVariants[$x]['values'] ?? [];

                    //  Foreach variant attribute option e.g "XS, SM, M, L, XL" or "Blue, Red" or "Cottom, Nylon"
                    for($y=0; $y < count($variantOptions); $y++){
                        //console.log('--------------------------------------------------------------');
                        //console.log('level == '+ level);
                        //console.log('Focus: ' + this.validVariants[x].name);
                        //console.log(level == 0 ? variantOptions[y] : ' ---' + variantOptions[y]);

                        //  Get the variant option name .g) XS_blue_cotton or XS_blue_nylon e.t.c
                        //  level = 0 : return XS_Blue_Cotton
                        //  level = 1 : return Blue
                        //  level = 2 : return Cottom

                        $variation_option = $variantOptions[$y];
                        
                        //  if we have more variations we can attach to the existing 
                        if( $level != (count($validVariants ?? []) - 1) ){

                            //  Foreach child variation we got e.g) blue, red or cotton, nylon
                            $childVariations = $this->generateVariations($variantAttributes, $level + 1);

                            //console.log('childVariations');
                            //console.log(childVariations);

                            for( $z = 0; $z < count($childVariations ?? []); $z++){

                                //  To avoid xs_cotton_cotton or xs_nylon_nylon
                                if($variation_option != $childVariations[$z]){

                                    //  To avoid xs_cotton_nylon or xs_nylon_cotton
                                    if($variation_option != $childVariations[$z]){
                                        
                                        /*  If we have:
                                         *  ~ ["Small", ["Blue", "Cotton"]]
                                         * 
                                         *  Then the method array_flatten will produce:
                                         * ~ ["Small", "Blue", "Cotton"]
                                         */
                                        array_push($variations, array_flatten([$variation_option, $childVariations[$z]]) );

                                    }
                                }
                            }
                        }else{

                            //  e.g to produce [blue, red] or [cotton, nylon]
                            array_push($variations, $variation_option);

                        }

                    }
                }

            }

            return $variations;
        }
    }

    public function validVariants($variantAttributes = []){
        
        $validVariants = [];

        //  If we have any variant attributes
        if( count($variantAttributes) ){

            //  Foreach variant attribute
            foreach($variantAttributes as $variantAttribute){

                //  If this variant attribute has a name and options
                if( $variantAttribute['name'] && count($variantAttribute['values'] ?? []) ){

                    //  Get the variant attribute
                    array_push($validVariants , $variantAttribute);
                }

            }
        }

        //  Return the valid variant attributes 
        return $validVariants;
    }















































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
         *  $storeId = 1, 2, 3, e.t.c
        /*
         *  The $storeId variable only get data specifically related to
         *  the specified store id. It is useful for scenerios where we
         *  want only comments of that store only
         */
        $storeId = request('storeId');

        /*
         *  $companyBranchId = 1, 2, 3, e.t.c
        /*
         *  The $companyBranchId variable only get data specifically related to
         *  the specified company branch id. It is useful for scenerios where we
         *  want only comments of that branch only
         */
        $companyBranchId = request('companyBranchId');

        /*
         *  $companyId = 1, 2, 3, e.t.c
        /*
         *  The $companyId variable only get data specifically related to
         *  the specified company id. It is useful for scenerios where we
         *  want only comments of that company only
         */
        $companyId = request('companyId');

        if (isset($storeId) && !empty($storeId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED STORE COMMENTS  *
            /********************************************************************/

            $model = Store::find($storeId);
        }elseif (isset($companyBranchId) && !empty($companyBranchId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED BRANCH COMMENTS *
            /********************************************************************/

            $model = CompanyBranch::find($companyBranchId);
        } elseif (isset($companyId) && !empty($companyId)) {
            /**********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY COMMENTS  *
            /**********************************************************************/

            $model = Company::find($companyId);
        } else {
            //  Apply filter by allocation
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL COMMENTS         *
                /**********************************************************/

                //  Get the current comment instance
                $model = $this;
            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH COMMENTS    *
                /*************************************************************/

                // Only get comments associated to the company branch
                $model = $auth_user->companyBranch;
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY COMMENTS    *
                /**************************************************************/

                //  Only get comments associated to the company
                $model = $auth_user->company;
            }
        }

        /*  If user indicated to only return specific types of products  */
        if( isset($productTypes) && !empty( $productTypes ) ){

            //  If the $productType is a list e.g) virtual,physical, ... e.t.c
            $productTypes = explode(',', $productType );

            if (count($productTypes)) {
                //  Get products listed
                $products = $model->productAndServices()->whereIn('type', $productTypes);
            }

        } else {
            //  Otherwise get all products
            $products = $model->productAndServices();
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
            'unit_regular_price' => request('unit_regular_price') ?? 0,
            'unit_sale_price' => request('unit_sale_price') ?? 0,

            //  Inventory & Tracking details
            'sku' => request('sku') ?? null,
            'barcode' => request('barcode') ?? null,
            'stock_quantity' => request('stock_quantity') ?? null,
            'allow_stock_management' => request('allow_stock_management'),
            'auto_manage_stock' => request('auto_manage_stock'),
            
            //  Variant details
            'variants' => request('variants') ?? null,
            'variant_attributes' => request('variant_attributes') ?? null,
            'allow_variants' => request('allow_variants'),
            
            //  Download Details
            'allow_downloads' => request('allow_downloads'),

            //  Store Details
            'show_on_store' => request('show_on_store'),

            //  Other
            'is_new' => request('is_new'),
            'is_featured' => request('is_featured'),

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
         *  the appropriate type (customer/supplier)
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
         *  the appropriate type (customer/supplier)
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

    /*  initiateUpdate2() method:
     *
     *  This is used to update an existing product. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the product.
     *
     */
    public function initiateUpdate2($product_id)
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
            'unit_regular_price' => request('unit_regular_price') ?? 0,
            'unit_sale_price' => request('unit_sale_price') ?? 0,

            //  Inventory & Tracking details
            'sku' => request('sku') ?? null,
            'barcode' => request('barcode') ?? null,
            'stock_quantity' => request('stock_quantity') ?? null,
            'allow_stock_management' => request('allow_stock_management'),
            'auto_manage_stock' => request('auto_manage_stock'),
            
            //  Variant details
            'variants' => request('variants') ?? null,
            'variant_attributes' => request('variant_attributes') ?? null,
            'allow_variants' => request('allow_variants'),
            
            //  Download Details
            'allow_downloads' => request('allow_downloads'),

            //  Store Details
            'show_on_store' => request('show_on_store'),

            //  Other
            'is_new' => request('is_new'),
            'is_featured' => request('is_featured'),
            
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
