<?php

namespace App\Traits;

use App\Document;

//  Notifications
use App\Notifications\ProductCreated;
use App\Notifications\ProductUpdated;

trait ProductTraits
{


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
            'name' => request('name'),
            'description' => request('description') ?? null,
            'type' => request('type') ?? null,
            'purchase_price' => request('purchase_price') ?? null,
            'selling_price' => request('selling_price') ?? null,
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
            'name' => request('name'),
            'description' => request('description') ?? null,
            'type' => request('type') ?? null,
            'purchase_price' => request('purchase_price') ?? null,
            'selling_price' => request('selling_price') ?? null,
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

                //  Refresh product
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
