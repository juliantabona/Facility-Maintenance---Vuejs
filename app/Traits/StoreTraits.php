<?php

namespace App\Traits;

use DB;
use App\Document;

//  Notifications
use App\Company;
use App\Notifications\StoreCreated;
use App\Notifications\StoreUpdated;
//  Other
use PDF;
use Carbon\Carbon;
use App\Http\Resources\Store as StoreResource;
use App\Http\Resources\Stores as StoresResource;

trait StoreTraits
{
    private $store;

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($stores = null)
    {

        try {

            if( $stores ){

                //  Transform the stores
                return new StoresResource($stores);

            }else{

                //  Transform the store
                return new StoreResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }


    /*  initiateCreate() method:
     *
     *  This method is used to create a new store.
     */
    public function initiateCreate( $storeInfo = null )
    {
        //  Get the authenticated user
        $user = auth('api')->user();

        /*
         *  The $template variable represents accepted structure of the store 
         *  data required to create a new resource.
         */
        $template = [
            'name' => $storeInfo['name'] ?? null,
            'owner_id' => $user->default_account->id,
            'owner_type' => $user->default_account->resource_type
        ];

        try {

            /*
             *  Create a new store, then retrieve a fresh instance
             */
            $this->store = $this->create($template)->fresh();

            /*  If the store was created successfully  */
            if( $this->store ){

                //  If the store does not have a currency
                if( !$this->store->currency ){

                    /*  Add the default current if non was specified  */
                    $this->store->addDefaultCurrency();

                }

                /*  Add the store settings  */
                $this->store->addSettings();

                /*  Create a new Ussd Interface  */
                $this->store->createUssdInterface();

                /*  Add the user to the store as an admin  */
                $this->store->addAdmin( $user = auth('api')->user() );

                /*  If we have only one address  */
                if( isset($storeInfo['address']) ){

                    /*  Create a new address  */
                    $this->store->createAddress( $storeInfo['address'] );

                /*  If we have many addresses  */
                }elseif( isset($storeInfo['addresses']) ){

                    /*  Foreach address  */
                    foreach( $storeInfo['addresses'] as $address){

                        /*  Create a new address  */
                        $this->store->createAddress( $address );

                    }

                }      
                
                /*  If we have only one phone  */
                if( isset($storeInfo['phone']) ){
                    
                    /*  Create a new phone  */
                    $this->store->createPhone( $storeInfo['phone'], $requires_verification = true );

                /*  If we have many phones  */
                }elseif( isset($storeInfo['phones']) ){
                    
                    /*  Foreach phone  */
                    foreach( $storeInfo['phones'] as $phone){

                        /*  Create a new phone  */
                        $this->store->createPhone( $phone, $requires_verification = true );

                    }

                }   

                /*  If we have only one email  */
                if( isset($storeInfo['email']) ){

                    /*  Create a new email  */
                    $this->store->createEmail( $storeInfo['email'] );

                /*  If we have many emails  */
                }elseif( isset($storeInfo['emails']) ){

                    /*  Foreach email  */
                    foreach( $storeInfo['emails'] as $email){

                        /*  Create a new email  */
                        $this->store->createEmail( $email );

                    }

                }   

                /*  Return a fresh instance of the store  */
                return $this->store->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

    public function addAdmin($user=null)
    {   
        /*  If we have a user provided */
        if($user){
            
            $isAdmin = $this->isAdmin($user);

            /*  If the user is not an admin to the store */
            if( !$isAdmin ){

                /*  Add the user to the store as admin  */
                $this->users()->save( $user, ['type' => 'admin']);
            
            }
        }

    }

    /*  addDefaultCurrency() method:
     *
     *  This method is used to add a default currency incase no currency was
     *  specified
     */
    public function addDefaultCurrency()
    {
        //  Get the default currency code e.g "EUR", "BWP", e.t.c
        $default_currency_code = 'BWP'; // $this->default_currency ?? 'BWP';

        //  Get the currency symbol using the currency code e.g "BWP" would produce "P"
        $default_currency_symbol = 'P'; // getCurrencySymbol( $default_currency_code );

        //  Update the store with the default currency
        $this->update([
            'currency' => [
                'code' => $default_currency_code,
                'symbol' => $default_currency_symbol
            ]
        ]);
    }

    /*  addSettings() method:
     *
     *  This method is used to add settings to the current store
     */
    public function addSettings()
    {
        //  Delete any existing settings of this store
        $this->settings()->delete();

        /*  Create new Settings using the initiateCreate() method from the Setting Model  */
        $settings = ( new \App\Setting() )->initiateCreate( $settingsInfo = [

            //  The setting details
            'details' => [

                //  Get the quotation template settings
                'quotationTemplate' => (new \App\Quotation )->getTemplateSettings(),
                
                //  Get the invoice template settings
                'invoiceTemplate' => (new \App\Invoice )->getTemplateSettings(),

                //  Get the order template settings
                'orderTemplate' => (new \App\Order )->getTemplateSettings(),
            ]

        ]);

       /*  If the settings were created successfully  */
       if( $settings ){

            /*  Assign the new settings to the store  */
            $settings->update([
                'owner_id' => $this->id, 
                'owner_type' => $this->resource_type
            ]);

        }

    }

    /*  createUssdInterface() method:
     *
     *  This method is used to create a new USSD Interface
     */
    public function createUssdInterface()
    {
        /*  Create a new Ussd Interface using the initiateCreate() method from the UssdInterfaceTraits  */
        $ussdInterface = ( new \App\UssdInterface() )->initiateCreate( $ussdInterfaceInfo = $this);

       /*  If the USSD Interface was created successfully  */
       if( $ussdInterface ){

            /*  Assign the new USSD Interface to the store  */
            $ussdInterface->update([
                'owner_id' => $this->id, 
                'owner_type' => $this->resource_type
            ]);

        }

    }

    /*  createAddress() method:
     *
     *  This method is used to create a new address 
     *  and assign it to the store.
     */
    public function createAddress($address)
    {
        /*  Create a new address using the initiateCreate() method from the AddressTraits  */
        $newAddress = ( new \App\Address() )->initiateCreate($addressInfo = $address);
        
        /*  If the address was created successfully  */
        if( $newAddress ){

            /*  Assign the new address to the store  */
            $newAddress->update([
                'owner_id' => $this->id, 
                'owner_type' => $this->resource_type,

                /*  Make this the default address if its the first address */
                'default' => ($this->addresses()->count() == 1) ? 1 : 0
            ]);

        }

    }

    /*  createPhone() method:
     *
     *  This method is used to create a new phone 
     *  and assign it to the store.
     */
    public function createPhone($phone, $requires_verification = false)
    {
        /*  If this phone number requires verification to ensure that the user own the number */
        if( $requires_verification ){

            /*  Indicate this by setting the property "verify_number" */ 
            $phone['verify_number'] = true;

        }

        /*  Create a new phone using the initiateCreate() method from the PhoneTraits  */
        $newPhone = ( new \App\Phone() )->initiateCreate($phoneInfo = $phone);
        
        /*  If the phone was created successfully  */
        if( $newPhone ){

            /*  Assign the new phone to the store  */
            $newPhone->update([
                'owner_id' => $this->id, 
                'owner_type' => $this->resource_type,

                /*  Make this the default phone if its the first mobile phone */
                'default' => ($this->mobiles()->count() == 1) ? 1 : 0
            ]);

        }
    }

    /*  createEmail() method:
     *
     *  This method is used to create a new email 
     *  and assign it to the store.
     */
    public function createEmail($email, $requires_verification = false)
    {
        /*  If this phone number requires verification to ensure that the user own the number */
        if( $requires_verification ){

            /*  Indicate this by setting the property "verify_number" */ 
            $phoneInfo['verify_number'] = true;

        }

        /*  Create a new email using the initiateCreate() method from the EmailTraits  */
        $newEmail = ( new \App\Email() )->initiateCreate($emailInfo = $email);
        
        /*  If the email was created successfully  */
        if( $newEmail ){

            /*  Assign the new email to the store  */
            $newEmail->update([
                'owner_id' => $this->id, 
                'owner_type' => $this->resource_type,

                /*  Make this the default email if its the first email */
                'default' => ($this->emails()->count() == 1) ? 1 : 0
            ]);
            
        }

    }


    /*  createContact() method:
     *
     *  This method is used to create and assign a new contact to 
     *  the company. If the contact already exists, then we do not 
     *
     */
    public function createContact( $contact = null )
    {
        if( $contact ){

            /*  Check if a contact using the same mobile number exists for the store  */
            $existingStoreContact = $this->contactsWithMobilePhone( $contact['phone'] )->first();

            /*  If no contact was found for the store  */
            if( !$existingStoreContact ){

                /*  Check if a contact using the same mobile number exists for the store's owning account */
                $existingAccountContact = $this->owner->contactsWithMobilePhone( $contact['phone'] )->first();

                /*  If no contact was found for the store's owning account  */
                if( !$existingAccountContact ){
                    
                    /*  Create a new contact using the initiateCreate() method from the ContactTraits  */
                    $newContact = ( new \App\Contact() )->initiateCreate($contact);

                    /*  Assign the new contact to the store's owning account  */
                    $this->owner->contacts()->save($newContact);
                    
                    /*  Allocate the new contact to the current store  */
                    $this->contacts()->save($newContact);

                    /*  Return the contact  */
                    return $newContact;

                /*  If a contact was found for the account  */
                }else{
                    
                    /*  Allocate the existing account contact to the current store  */
                    $newContact = $this->contacts()->save($existingAccountContact);

                    /*  Return the contact  */
                    return $newContact;

                }

            /*  If a contact was found for the store  */
            }else{

                /*  Return the contact  */
                return $existingStoreContact;

            }

        }
    }

    /*  findContactById()
     *
     *  This method returns the first store contact that
     *  mataches the id specified
     */
    public function findContactById($id)
    {
        /*  Retrieve the store contact */
        return $this->contacts()->where('contacts.id', $id )->first();
    }

    /*  getBasicInfo() method:
     *
     *  This method is used to get the general information about the store
     *  This general information is used when drafting orders, invoices,
     *  quotations and other resources that require the merchants 
     *  information.
     */
    public function getBasicInfo()
    {
        /*  Return the general information from the store information  */
        return collect($this->toArray())->only(['logo', 'name', 'email', 'phone', 'address']);
    }


























    /*  getStores() method:
     *
     *  This is used to return stores
     *
     */
    public function getStores( $options = [] )
    {
        /************************************
        *  CHECK IF THE USER IS AUTHORIZED  *
        /************************************/

        try {

            //  If we have provided the users id
            if( isset($options['user_id']) && !empty(isset($options['user_id'])) ){
                
                //  Get the specified user
                $user = User::find( $options['user_id'] );
            
                //  Filter the stores by the user type
                $userTypes = request('userTypes');

                if( isset($userTypes) && !empty($userTypes) ){
                    
                    if($userTypes == 'admin'){
                        //  Get stores where this user is an admin
                        $stores = $user->storesWhereUserIsAdmin;
                    }elseif($userTypes == 'staff'){
                        //  Get stores where this user is an staff member
                        $stores = $user->storesWhereUserIsStaff;
                    }elseif($userTypes == 'customer'){
                        //  Get stores where this user is an customer
                        $stores = $user->storesWhereUserIsClient;
                    }elseif($userTypes == 'vendor'){
                        //  Get stores where this user is an vendor
                        $stores = $user->storesWhereUserIsVendor;
                    }else{
                        /*  Incase $userTypes is a list e.g) admin,staff ... e.t.c
                         *  This means we want stores where the user plays anyone
                         *  of those roles.
                         */
                        $userTypes = explode(',', $userTypes );

                        //  If we have atleast one userType
                        if (count($userTypes)) {
                            //  Get stores related to the listed user types
                            $stores = $user->stores()->whereHas('users', function ($query) use($userTypes) {
                                            $query->whereIn('user_allocations.type', $userTypes);
                                        })->get();
                        }
                    }

                }else{

                    //  Get the specified stores for this user
                    $stores = $user->stores()->get();
                }

            }else{

                //  Get all the stores
                $stores = $this->all();

            }

            if( $stores ){

                //  Transform the stores
                return new StoresResource($stores);

            }else{

                //  Otherwise we don't have a resource to return
                return oq_api_notify_no_resource();
            
            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  getStore() method:
     *
     *  This is used to return the specified store
     *
     */
    public function getStore( $store_id = null, $options = [] )
    {
        /************************************
        *  CHECK IF THE USER IS AUTHORIZED  *
        /************************************/

        try {

            //  If we have provided the users id
            if( isset($options['user_id']) && !empty(isset($options['user_id'])) ){
                
                //  Get the specified store for this user
                $store = User::find( $options['user_id'] )->stores()->where('stores.id', $store_id)->first();
            
            }else{

                //  Get the specified store
                $store = $this->where('id', $store_id)->first();

            }

            if( $store ){

                //  Transform the store
                return new StoreResource($store);

            }else{

                //  Otherwise we don't have a resource to return
                return oq_api_notify_no_resource();
            
            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }





















    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of store results.
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
         *  $storeInterests = 
        /*
         *  The $storeInterests variable is used to determine which type of store to pull.
         *  It represents stores interested in a particular service of the ecommerce
         *  platform. The user may request stores with an interest in:
         *  1) physical: A store that has interest in listing physical products
         *  2) service: A store that has interest in listing virtual products
         *  3) Event: A store that has interest in listing events
         *  4) Ticket: A store that has interest in listing tickets
         *  5) Donation: A store that has interest in listing donations
         *  6) Membership: A store that has interest in listing membership signup
         */
        $storeInterests = strtolower(request('storeInterests'));

        /*
         *  $companyId = 1, 2, 3, e.t.c
        /*
         *  The $companyId variable only get data specifically related to
         *  the specified company id. It is useful for scenerios where we
         *  want only stores of that company only
         */
        $companyId = request('companyId');

        if( isset($companyId) && !empty($companyId) ){

            //  Only get specific company data only if specified
            if ($companyId) {
                /************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY STORES    *
                /***********************************************************************/

                $model = Company::find($companyId);
            }

        }else{

            //  Apply filter by allocation
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL STORES         *
                /**********************************************************/

                //  Get the current store instance
                $model = $this;

            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH STORES    *
                /*************************************************************/

                // Only get stores associated to the company branch
                $model = $auth_user->companyBranch;
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY STORES    *
                /**************************************************************/

                //  Only get stores associated to the company
                $model = $auth_user->company;
            }

        }

        if(isset($storeInterests) && !empty( $storeInterests )){

            //  If the $storeInterests is a list e.g) physical,service,event ... e.t.c
            $storeInterests = explode(',', $storeInterests );

            //  If we have multiple interests
            if (count($storeInterests)) {
                //  Get stores only with the specified interests
                $stores = $model->stores()->whereHas('interests', function($query) use($storeInterests){
                    $query->whereIn('type', $storeInterests);
                });  
            }

        } else {
            //  Otherwise get all stores
            $stores = $model->stores();
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

        $order_join = 'stores';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $stores = $stores->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $stores = $stores->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $stores = $stores->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we only want to know how many were returned
            if( request('count') == 1 ){
                //  If the stores are paginated
                if($config['paginate']){
                    $stores = $stores->total() ?? 0;
                //  If the stores are not paginated
                }else{
                    $stores = $stores->count() ?? 0;
                }
            }else{
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $stores = $stores->get();
                }

                //  If we have any stores so far
                if ($stores) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $stores->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $stores];

        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }


    /*  initiateShow() method:
     *
     *  This is used to return only one specific store.
     *
     */
    public function initiateShow($store_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        try {
            //  Get the trashed store
            if (request('withtrashed') == 1) {
                //  Run query
                $store = $this->withTrashed()->where('id', $store_id)->first();

            //  Get the non-trashed store
            } else {
                //  Run query
                $store = $this->where('id', $store_id)->first();
            }

            //  If we have any store so far
            if ($store) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $store->load(oq_url_to_array(request('connections')));
                }

                //  Action was executed successfully
                return ['success' => true, 'response' => $store];
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
     *  This is used to create a new store. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the store.
     *
     */
    /*
    public function initiateCreate($template = null)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        $template = $template ?? [
            //  General details
            'title' => request('title'),
            'description' => request('description') ?? null,
            'type' => request('type') ?? null,
            
            //  Pricing details
            'cost_per_item' => request('cost_per_item') ?? 0,
            'unit_regular_price' => request('unit_regular_price') ?? 0,
            'unit_sale_price' => request('unit_sale_price') ?? 0,

            //  Inventory & Tracking details
            'sku' => request('sku') ?? null,
            'barcode' => request('barcode') ?? null,
            'quantity' => request('quantity') ?? null,
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

            //  Ownership Details
            'company_branch_id' => $auth_user->company_branch_id ?? null,
            'company_id' => $auth_user->company_id ?? null,
        ];

        try {
            //  Create the store
            $store = $this->create($template)->fresh();

            //  If the store was created successfully
            if ($store) {

                //  Check whether or not the store has any image to upload
                $this->checkAndUploadImage($store);

                //  Check whether or not the store has any categories to add
                $this->checkAndCreateCategories($store);

                //  Check whether or not the store has any tags to add
                $this->checkAndCreateTags($store);

                //  refetch the updated store
                $store = $store->fresh();

                // $auth_user->notify(new StoreCreated($store));

                //  Record activity of store created
                $status = 'created';
                $storeCreatedActivity = oq_saveActivity($store, $auth_user, $status, ['store' => $store->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $store];
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
    */

    public function checkAndCreateCategories($store)
    {
        $auth_user = auth('api')->user();

        /*  categories:
         *  This is a variable used to determine if the current store being created has 
         *  categories. Sometimes when creating a new store, we may want to add categories
         *  to that store. We can do this if the relationship variable has been set with
         *  the appropriate type (customer/supplier)
         */
        $categories = request('categories') ?? null;

        if (isset($categories) && !empty($categories)) {

            //  $categories = [1,2,3, ...] ids of new categories 
            $categories = json_decode($categories) ?? [];

            //  Delete any previously assigned categories
            DB::table('category_allocations')
                ->where('trackable_id', $store->id)
                ->where('trackable_type', 'store')
                ->delete();
                                    
            //  Add new categories
            foreach($categories as $category){

                //  Add to category allocations
               DB::table('category_allocations')->insert([
                    'category_id' => $category, 
                    'trackable_id' => $store->id,                       
                    'trackable_type' => 'store',                       
                    'created_at' => DB::raw('now()'),                       
                    'updated_at' => DB::raw('now()')
                ]);

            }
        }
    }

    public function checkAndCreateTags($store)
    {
        $auth_user = auth('api')->user();

        /*  tags:
         *  This is a variable used to determine if the current store being created has 
         *  tags. Sometimes when creating a new store, we may want to add tags
         *  to that store. We can do this if the relationship variable has been set with
         *  the appropriate type (customer/supplier)
         */
        $tags = request('tags') ?? null;

        if (isset($tags) && !empty($tags)) {

            //  $tags = [1,2,3, ...] ids of new tags 
            $tags = json_decode($tags) ?? [];

            //  Delete any previously assigned tags
            DB::table('tag_allocations')
                ->where('trackable_id', $store->id)
                ->where('trackable_type', 'store')
                ->delete();
                                    
            //  Add new tags
            foreach($tags as $tag){

                //  Add to tag allocations
               DB::table('tag_allocations')->insert([
                    'tag_id' => $tag, 
                    'trackable_id' => $store->id,                       
                    'trackable_type' => 'store',                       
                    'created_at' => DB::raw('now()'),                       
                    'updated_at' => DB::raw('now()')
                ]);

            }
        }
    }

    public function checkAndUploadImage($store)
    {
        /*  primary_image:
         *  This is a variable used to determine if the current store being created has
         *  an image file to upload. Sometimes when creating a new store, we may want to 
         *  also upload the primary image (featured image) at the same time. We can do this 
         *  if the primary_image variable has been set with the image file (type=binary)
         */
        $File = request('primary_image');

        if (isset($File) && !empty($File) && request()->hasFile('primary_image')) {

            //  Start upload process of files
            $data = ( new Document() )->saveDocument( request(), $store->id, 'store', $File, 'stores', 'primary', true );

        }
    }

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing store. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the store.
     *
     */
    public function initiateUpdate($store_id)
    {

        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE STORE    *
         ******************************************************/

        /*********************************************
         *   VALIDATE STORE INFORMATION            *
         ********************************************/

        //  Create a template to hold the store details
        $template = $template ?? [
            //  General details
            'title' => request('title'),
            'description' => request('description') ?? null,
            'type' => request('type') ?? null,
            
            //  Pricing details
            'cost_per_item' => request('cost_per_item') ?? 0,
            'unit_regular_price' => request('unit_regular_price') ?? 0,
            'unit_sale_price' => request('unit_sale_price') ?? 0,

            //  Inventory & Tracking details
            'sku' => request('sku') ?? null,
            'barcode' => request('barcode') ?? null,
            'quantity' => request('quantity') ?? null,
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

            //  Ownership Details
            'company_branch_id' => $auth_user->company_branch_id ?? null,
            'company_id' => $auth_user->company_id ?? null,
        ];

        try {
            //  Update the store
            $store = $this->where('id', $store_id)->first()->update($template);

            //  If the store was updated successfully
            if ($store) {

                //  re-retrieve the instance to get all of the fields in the table.
                $store = $this->where('id', $store_id)->first();

                //  Check whether or not the store has any image to upload
                $this->checkAndUploadImage($store);

                //  Check whether or not the store has any categories to add
                $this->checkAndCreateCategories($store);

                //  Check whether or not the store has any tags to add
                $this->checkAndCreateTags($store);

                //  refetch the updated store
                $store = $store->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new StoreUpdated($store));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of store updated
                $status = 'updated';
                $storeUpdatedActivity = oq_saveActivity($store, $auth_user, $status, ['store' => $store->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $store];
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

    public function checkAndCreateSettings($store)
    {
        //  Check if the company has any settings
        $settings = $store->settings()->count();

        if (!$settings) {
            //  Create new settings for the company
            $settings = $store->settings()->create([
                'details' => $this->settingsTemplate($store),
            ]);
        }
    }

    public function settingsTemplate($store)
    {
        return [
            //  Get the general settings
            'general' => [
                //  Get the currency mathing the company country
                'currency_type' => $this->predictCurrency($store)
            ],

            //  Get settings for creating quotations
            'quotationTemplate' => (new Quotation())->sampleTemplate(),

            //  Get settings for creating invoices
            'invoiceTemplate' => (new Invoice())->sampleTemplate(),
            
        ];
    }

    public function getStoreBankAccountDetailsAsPDF($store){

        //  Get the store settings
        $bankAccountDetails = $store->settings['details']['bankAccountTemplate'] ?? null;

        return PDF::loadView('pdf.bank_account_details', array('bankAccountDetails' => $bankAccountDetails));

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
