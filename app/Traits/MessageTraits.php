<?php

namespace App\Traits;

use DB;
use App\Document;
use App\CompanyBranch;
use App\Company;
use App\Product;
use App\Store;
use App\Order;

//  Notifications
use App\Notifications\MessageCreated;
use App\Notifications\MessageUpdated;

//  Resources
use App\Http\Resources\Message as MessageResource;
use App\Http\Resources\Messages as MessagesResource;

trait MessageTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($Messages = null)
    {

        try {

            if( $Messages ){
                
                //  Transform the Messages
                return new MessagesResource($Messages);

            }else{
                
                //  Transform the Message
                return new MessageResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }



    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of Message results.
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
         *  $MessageType =
        /*
         *  The $MessageType variable is used to determine which types of Messages to pull.
         *  The user may request Messages with a type of:
         *  1) review: Message received but are saved as reviews
         */
        $MessageType = strtolower(request('MessageType'));

        /*
         *  $storeId = 1, 2, 3, e.t.c
        /*
         *  The $storeId variable only get data specifically related to
         *  the specified store id. It is useful for scenerios where we
         *  want only Messages of that store only
         */
        $storeId = request('storeId');

        /*
         *  $productId = 1, 2, 3, e.t.c
        /*
         *  The $productId variable only get data specifically related to
         *  the specified product id. It is useful for scenerios where we
         *  want only Messages of that product only
         */
        $productId = request('productId');

        /*
         *  $orderId = 1, 2, 3, e.t.c
        /*
         *  The $orderId variable only get data specifically related to
         *  the specified product id. It is useful for scenerios where we
         *  want only Messages of that product only
         */
        $orderId = request('orderId');

        /*
         *  $companyBranchId = 1, 2, 3, e.t.c
        /*
         *  The $companyBranchId variable only get data specifically related to
         *  the specified company branch id. It is useful for scenerios where we
         *  want only Messages of that branch only
         */
        $companyBranchId = request('companyBranchId');

        /*
         *  $companyId = 1, 2, 3, e.t.c
        /*
         *  The $companyId variable only get data specifically related to
         *  the specified company id. It is useful for scenerios where we
         *  want only Messages of that company only
         */
        $companyId = request('companyId');

        if (isset($storeId) && !empty($storeId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED STORE MessageS  *
            /********************************************************************/

            $model = Store::find($storeId);
        }else if (isset($productId) && !empty($productId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED PRODUCT Message *
            /********************************************************************/

            $model = Product::find($productId);
        }else if (isset($orderId) && !empty($orderId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED PRODUCT Message *
            /********************************************************************/

            $model = Order::find($orderId);
        }elseif (isset($companyBranchId) && !empty($companyBranchId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED BRANCH MessageS *
            /********************************************************************/

            $model = CompanyBranch::find($companyBranchId);
        } elseif (isset($companyId) && !empty($companyId)) {
            /**********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY MessageS  *
            /**********************************************************************/

            $model = Company::find($companyId);
        } else {
            //  Apply filter by allocation
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL MessageS         *
                /**********************************************************/

                //  Get the current Message instance
                $model = $this;
            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH MessageS    *
                /*************************************************************/

                // Only get Messages associated to the company branch
                $model = $auth_user->companyBranch;
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY MessageS    *
                /**************************************************************/

                //  Only get Messages associated to the company
                $model = $auth_user->company;
            }
        }

        if (isset($MessageType) && !empty($MessageType)) {
            //  If the $MessageType is a list e.g) pending,processing,on-hold ... e.t.c
            $MessageType = explode(',', $MessageType);

            //  If we have atleast one type
            if (count($MessageType)) {
                //  Get Messages only with the specified type
                $Messages = $model->Messages()->whereIn('type', $MessageType);
            }
        } else {
            //  Otherwise get all Messages
            $Messages = $model->Messages();
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

        $order_join = 'Messages';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $Messages = $Messages->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $Messages = $Messages->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $Messages = $Messages->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we only want to know how many were returned
            if( request('count') == 1 ){
                //  If the Messages are paginated
                if($config['paginate']){
                    $Messages = $Messages->total() ?? 0;
                //  If the Messages are not paginated
                }else{
                    $Messages = $Messages->count() ?? 0;
                }
            }else{
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $Messages = $Messages->get();
                }

                //  If we have any Messages so far
                if ($Messages) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $Messages->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $Messages];

        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }


    /*  initiateShow() method:
     *
     *  This is used to return only one specific Message.
     *
     */
    public function initiateShow($Message_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        try {
            //  Get the trashed Message
            if (request('withtrashed') == 1) {
                //  Run query
                $Message = $this->withTrashed()->where('id', $Message_id)->first();

            //  Get the non-trashed Message
            } else {
                //  Run query
                $Message = $this->where('id', $Message_id)->first();
            }

            //  If we have any Message so far
            if ($Message) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $Message->load(oq_url_to_array(request('connections')));
                }

                //  Action was executed successfully
                return ['success' => true, 'response' => $Message];
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
     *  This is used to create a new Message. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the Message.
     *
     */
    public function initiateCreate($template = null)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE Message    *
         ******************************************************/

        /*********************************************
         *   VALIDATE Message INFORMATION            *
         ********************************************/
        
        //  Create a template to hold the Message details
        $template = $template ?? [

            //  Message text
            'text' => request('text') ?? '',
            
            //  Message type e.g) review
            'type' => request('MessageType') ?? '',

            //  User id
            'user_id' => request('user_id') ?? $auth_user->id,

            //  From Staff true/false
            'from_staff' => request('from_staff') ?? false

        ];

        try {
            //  Create the Message
            $Message = $this->create($template)->fresh();
            
            //  If the Message was created successfully
            if ($Message) {

                //  Check and create rating
                $MessageRating = $this->checkAndCreateMessageRating($Message);

                $MessageAllocation = $this->checkAndAllocateMessage($Message);

                //  refetch the updated Message
                $Message = $Message->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new MessageCreated($Message));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of Message created
                $status = 'created';
                $MessageCreatedActivity = oq_saveActivity($Message, $auth_user, $status, ['Message' => $Message->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $Message];
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

    public function checkAndCreateMessageRating($Message){

        $auth_user = auth('api')->user();

        /*  rating:
         *  This is a variable used to determine if the current Message has a rating
         *  Sometimes when creating a new Message, we may want to add rating to that
         *  Message. We can do this if the relationship variable has been set with
         *  the appropriate rating e.g 1, 2, 3, 4, 5
         */
        $rating = request('rating') ?? null;

        if (isset($rating) && !empty($rating)) {

            //  Add to rating to Message
            $rating = DB::table('ratings')->insert([
                        'value' => $rating, 
                        'user_id' => $auth_user->id,
                        'trackable_id' => $Message->id,                       
                        'trackable_type' => 'Message',                       
                        'created_at' => DB::raw('now()'),                       
                        'updated_at' => DB::raw('now()')
                    ]);

            return $rating;

        }

        return false;
    }

    public function checkAndAllocateMessage($Message){

        $auth_user = auth('api')->user();

        $order = Order::find( request('orderId') ) ?? null;
        $product = Product::find( request('productId') ) ?? null;
        $store = Store::find( request('storeId') ) ?? null;

        if (isset($order) && !empty($order)) {
            
            //  Allocate the Message to this order
            $this->insertAllocation($Message, $order->id, 'order');
            //  Then allocate the Message to this store as well
            $this->insertAllocation($Message, $order->store->id, 'store');

        }else if (isset($product) && !empty($product)) {
            
            //  Allocate the Message to this order
            $this->insertAllocation($Message, $product->id, 'product');
            //  Then allocate the Message to this store as well
            $this->insertAllocation($Message, $product->store->id, 'store');

        }else if (isset($store) && !empty($store)) {
            //  Allocate the Message to this store
            $this->insertAllocation($Message, $store->id, 'store');

        }

        return false;
    }

    public function insertAllocation($Message, $trackable_id, $trackable_type){

        //  Allocate to Message
        $insert = DB::table('Message_allocations')->insert([
                'Message_id' => $Message->id,
                'trackable_id' => $trackable_id,                       
                'trackable_type' => $trackable_type,                       
                'created_at' => DB::raw('now()'),                       
                'updated_at' => DB::raw('now()')
            ]);

        return $insert;

    }

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing Message. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the Message.
     *
     */
    public function initiateUpdate($Message_id)
    {

        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE Message    *
         ******************************************************/

        /*********************************************
         *   VALIDATE Message INFORMATION            *
         ********************************************/

        //  Create a template to hold the Message details
        $template = $template ?? [

            //  Message text
            'text' => request('text'),
            
            //  User id
            'user_id' => request('user_id') ?? $auth_user->id
            
        ];

        try {
            //  Update the Message
            $Message = $this->where('id', $Message_id)->first()->update($template);

            //  If the Message was updated successfully
            if ($Message) {

                //  re-retrieve the instance to get all of the fields in the table.
                $Message = $this->where('id', $Message_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new MessageUpdated($Message));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of Message updated
                $status = 'updated';
                $MessageUpdatedActivity = oq_saveActivity($Message, $auth_user, $status, ['Message' => $Message->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $Message];
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
