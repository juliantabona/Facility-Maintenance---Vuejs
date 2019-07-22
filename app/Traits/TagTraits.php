<?php

namespace App\Traits;

use App\Product;
use App\Company;

trait TagTraits
{
    /*  initiateGetAll() method:
     *
     *  This is used to return a pagination of tag results.
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
         *     authenticated user. This means we can access all possible records
         *     available. This is usually useful for users acting as superadmins.
         */
        $allocation = request('allocation');

        /*
         *  $tagType = product, appointment, jobcard, e.t.c
         *
         *  The $tagType variable is used to determine which type of category to pull.
         *  The user may request data of type.
         *  1) product: A tags related to only products
         *  2) appointment: A tags related to only appointments
         *  and so on...
         */
        $tagType = strtolower(request('tagType'));

        /*
         *  $companyId = 1, 2, 3, e.t.c
         *
         *  The $companyId variable only get data specifically related to
         *  the specified company id. It is useful for scenerios where we
         *  want only tags of that company only
         */
        $companyId = request('companyId');

        /*
         *  $productId = 1, 2, 3, e.t.c
         *
         *  The $productId variable only get data specifically related to
         *  the specified product id. It is useful for scenerios where we
         *  want only tags of that product only
         */
        $productId = request('productId');

        if( isset($companyId) && !empty($companyId) ){

            //  Only get specific company data only if specified
            if ($companyId) {
                /************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED COMPANY PRODUCTS    *
                /***********************************************************************/

                $tags = Company::find($companyId)->tags();
            }

        }else if( isset($productId) && !empty($productId) ){

            //  Only get specific product data only if specified
            if ($productId) {
                /************************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET SPECIFIED PRODUCT tags  *
                /***********************************************************************/

                $tags = Product::find($productId)->tags();
            }

        }else{

            //  Apply filter by allocation
            if ($allocation == 'all') {
                /***********************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO ALL PRODUCTS         *
                /**********************************************************/

                //  Get the current product instance
                $tags = $this;

            } elseif ($allocation == 'branch') {
                /*************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET BRANCH PRODUCTS    *
                /*************************************************************/

                // Only get products associated to the company branch
                $tags = $auth_user->companyBranch->tags();
            } else {
                /**************************************************************
                *  CHECK IF THE USER IS AUTHORIZED TO GET COMPANY PRODUCTS    *
                /**************************************************************/

                //  Only get products associated to the company
                $tags = $auth_user->company->tags();
            }

        }
        
        //  Filter to the $tagType type
        if ($tagType) {
            //  If the $tagType is a list e.g) product,appointment
            $type = explode(',', $tagType);

            if (count($type)) {
                $tags = $tags->whereIn('type', $type);
            } else {
                $tags = $tags->where('type', $tagType);
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

        $order_join = 'tags';

        try {
            //  Get all and trashed
            if (request('withtrashed') == 1) {
                //  Run query
                $tags = $tags->withTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get only trashed
            } elseif (request('onlytrashed') == 1) {
                //  Run query
                $tags = $tags->onlyTrashed()->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            //  Get all except trashed
            } else {
                //  Run query
                $tags = $tags->advancedFilter(['order_join' => $order_join, 'paginate' => $config['paginate']]);
            }

            //  If we only want to know how many were returned
            if( request('count') == 1 ){
                //  If the tags are paginated
                if($config['paginate']){
                    $tags = $tags->total() ?? 0;
                //  If the tags are not paginated
                }else{
                    $tags = $tags->count() ?? 0;
                }
            }else{
                //  If we are not paginating then
                if (!$config['paginate']) {
                    //  Get the collection
                    $tags = $tags->get();
                }

                //  If we have any tags so far
                if ($tags) {
                    //  Eager load other relationships wanted if specified
                    if (strtolower(request('connections'))) {
                        $tags->load(oq_url_to_array(strtolower(request('connections'))));
                    }
                }
            }

            //  Action was executed successfully
            return ['success' => true, 'response' => $tags];
            
        } catch (\Exception $e) {
            //  Log the error
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        }
    }

    /*  initiateCreate() method:
     *
     *  This is used to create a new tag. It also works
     *  to store the creation activity and broadcasting of
     *  notifications to users concerning the creation of
     *  the tag.
     *
     */
    public function initiateCreate($template = null)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE TAG        *
         ******************************************************/

        /*********************************************
         *   VALIDATE TAG INFORMATION            *
         ********************************************/
        
        //  Create a template to hold the tag details
        $template = $template ?? [
            //  General details
            'name' => trim( request('name') ),
            'type' => request('type') ?? null,
        
            //  Ownership Details
            'company_id' => $auth_user->company_id ?? null,
        ];

        try {
            //  Create the tag
            $tag = $this->create($template);

            //  If the tag was created successfully
            if ($tag) {

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new TagCreated($tag));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of tag created
                $status = 'created';
                $tagCreatedActivity = oq_saveActivity($tag, $auth_user, $status, ['tag' => $tag->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $tag];
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
