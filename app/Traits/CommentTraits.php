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
use App\Notifications\CommentCreated;
use App\Notifications\CommentUpdated;

trait CommentTraits
{

    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of comment results.
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
         *  $commentType =
        /*
         *  The $commentType variable is used to determine which types of comments to pull.
         *  The user may request comments with a type of:
         *  1) review: Comment received but are saved as reviews
         */
        $commentType = strtolower(request('commentType'));

        /*
         *  $storeId = 1, 2, 3, e.t.c
        /*
         *  The $storeId variable only get data specifically related to
         *  the specified store id. It is useful for scenerios where we
         *  want only comments of that store only
         */
        $storeId = request('storeId');

        /*
         *  $productId = 1, 2, 3, e.t.c
        /*
         *  The $productId variable only get data specifically related to
         *  the specified product id. It is useful for scenerios where we
         *  want only comments of that product only
         */
        $productId = request('productId');

        /*
         *  $orderId = 1, 2, 3, e.t.c
        /*
         *  The $orderId variable only get data specifically related to
         *  the specified product id. It is useful for scenerios where we
         *  want only comments of that product only
         */
        $orderId = request('orderId');

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
        }else if (isset($productId) && !empty($productId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED PRODUCT COMMENT *
            /********************************************************************/

            $model = Product::find($productId);
        }else if (isset($orderId) && !empty($orderId)) {
            /********************************************************************
            *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED PRODUCT COMMENT *
            /********************************************************************/

            $model = Order::find($orderId);
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

        if (isset($commentType) && !empty($commentType)) {
            //  If the $commentType is a list e.g) pending,processing,on-hold ... e.t.c
            $commentType = explode(',', $commentType);

            //  If we have atleast one type
            if (count($commentType)) {
                //  Get comments only with the specified type
                $comments = $model->comments()->whereIn('type', $commentType);
            }
        } else {
            //  Otherwise get all comments
            $comments = $model->comments();
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

        $order_join = 'comments';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $comments = $comments->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $comments = $comments->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $comments = $comments->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we only want to know how many were returned
            if( request('count') == 1 ){
                //  If the comments are paginated
                if($config['paginate']){
                    $comments = $comments->total() ?? 0;
                //  If the comments are not paginated
                }else{
                    $comments = $comments->count() ?? 0;
                }
            }else{
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $comments = $comments->get();
                }

                //  If we have any comments so far
                if ($comments) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $comments->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $comments];

        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }


    /*  initiateShow() method:
     *
     *  This is used to return only one specific comment.
     *
     */
    public function initiateShow($comment_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        try {
            //  Get the trashed comment
            if (request('withtrashed') == 1) {
                //  Run query
                $comment = $this->withTrashed()->where('id', $comment_id)->first();

            //  Get the non-trashed comment
            } else {
                //  Run query
                $comment = $this->where('id', $comment_id)->first();
            }

            //  If we have any comment so far
            if ($comment) {
                //  Eager load other relationships wanted if specified
                if (request('connections')) {
                    $comment->load(oq_url_to_array(request('connections')));
                }

                //  Action was executed successfully
                return ['success' => true, 'response' => $comment];
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
     *  This is used to create a new comment. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the comment.
     *
     */
    public function initiateCreate($template = null)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE COMMENT    *
         ******************************************************/

        /*********************************************
         *   VALIDATE COMMENT INFORMATION            *
         ********************************************/
        
        //  Create a template to hold the comment details
        $template = $template ?? [

            //  Comment text
            'text' => request('text') ?? '',
            
            //  Comment type e.g) review
            'type' => request('commentType') ?? '',

            //  User id
            'user_id' => request('user_id') ?? $auth_user->id,

            //  From Staff true/false
            'from_staff' => request('from_staff') ?? false

        ];

        try {
            //  Create the comment
            $comment = $this->create($template)->fresh();
            
            //  If the comment was created successfully
            if ($comment) {

                //  Check and create rating
                $commentRating = $this->checkAndCreateCommentRating($comment);

                $commentAllocation = $this->checkAndAllocateComment($comment);

                //  refetch the updated comment
                $comment = $comment->fresh();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new CommentCreated($comment));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of comment created
                $status = 'created';
                $commentCreatedActivity = oq_saveActivity($comment, $auth_user, $status, ['comment' => $comment->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $comment];
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

    public function checkAndCreateCommentRating($comment){

        $auth_user = auth('api')->user();

        /*  rating:
         *  This is a variable used to determine if the current comment has a rating
         *  Sometimes when creating a new comment, we may want to add rating to that
         *  comment. We can do this if the relationship variable has been set with
         *  the appropriate rating e.g 1, 2, 3, 4, 5
         */
        $rating = request('rating') ?? null;

        if (isset($rating) && !empty($rating)) {

            //  Add to rating to comment
            $rating = DB::table('ratings')->insert([
                        'value' => $rating, 
                        'user_id' => $auth_user->id,
                        'trackable_id' => $comment->id,                       
                        'trackable_type' => 'comment',                       
                        'created_at' => DB::raw('now()'),                       
                        'updated_at' => DB::raw('now()')
                    ]);

            return $rating;

        }

        return false;
    }

    public function checkAndAllocateComment($comment){

        $auth_user = auth('api')->user();

        $order = Order::find( request('orderId') ) ?? null;
        $product = Product::find( request('productId') ) ?? null;
        $store = Store::find( request('storeId') ) ?? null;

        if (isset($order) && !empty($order)) {
            
            //  Allocate the comment to this order
            $this->insertAllocation($comment, $order->id, 'order');
            //  Then allocate the comment to this store as well
            $this->insertAllocation($comment, $order->store->id, 'store');

        }else if (isset($product) && !empty($product)) {
            
            //  Allocate the comment to this order
            $this->insertAllocation($comment, $product->id, 'product');
            //  Then allocate the comment to this store as well
            $this->insertAllocation($comment, $product->store->id, 'store');

        }else if (isset($store) && !empty($store)) {
            //  Allocate the comment to this store
            $this->insertAllocation($comment, $store->id, 'store');

        }

        return false;
    }

    public function insertAllocation($comment, $trackable_id, $trackable_type){

        //  Allocate to comment
        $insert = DB::table('comment_allocations')->insert([
                'comment_id' => $comment->id,
                'trackable_id' => $trackable_id,                       
                'trackable_type' => $trackable_type,                       
                'created_at' => DB::raw('now()'),                       
                'updated_at' => DB::raw('now()')
            ]);

        return $insert;

    }

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing comment. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the comment.
     *
     */
    public function initiateUpdate($comment_id)
    {

        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE COMMENT    *
         ******************************************************/

        /*********************************************
         *   VALIDATE COMMENT INFORMATION            *
         ********************************************/

        //  Create a template to hold the comment details
        $template = $template ?? [

            //  Comment text
            'text' => request('text'),
            
            //  User id
            'user_id' => request('user_id') ?? $auth_user->id
            
        ];

        try {
            //  Update the comment
            $comment = $this->where('id', $comment_id)->first()->update($template);

            //  If the comment was updated successfully
            if ($comment) {

                //  re-retrieve the instance to get all of the fields in the table.
                $comment = $this->where('id', $comment_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new CommentUpdated($comment));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of comment updated
                $status = 'updated';
                $commentUpdatedActivity = oq_saveActivity($comment, $auth_user, $status, ['comment' => $comment->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $comment];
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
