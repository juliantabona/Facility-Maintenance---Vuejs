<?php

namespace App\Traits;

use App\Http\Resources\Phone as PhoneResource;
use App\Http\Resources\Phones as PhonesResource;

trait PhoneTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($phones = null)
    {

        try {

            if( $phones ){

                //  Transform the phones
                return new PhonesResource($phones);

            }else{

                //  Transform the phone
                return new PhoneResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new phone.
     */
    public function initiateCreate( $template = null )
    {
        /*
         *  The $phone variable represents the phone dataset
         *  provided through the request received.
         */
        $phone = request('phone');

        /*
         *  The $template variable represents structure of the phone.
         *  If no template is provided, we create one using the 
         *  request data.
         */
        $template = $template ?? [
            'type' => $phone['type'] ?? null,
            'calling_code' => $phone['calling_code'] ?? null,
            'number' => $phone['number'] ?? null,
            'provider' => $phone['provider'] ?? null
        ];

        try {

            /*
             *  Create a new phone, then retrieve a fresh instance
             */
            $phone = $this->create($template)->fresh();

            /*  If the phone was created successfully  */
            if( $phone ){   

                /*  Return a fresh instance of the phone  */
                return $phone->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }






    public function initiateGet()
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $modelId = request('modelId');                      //  The id of the associated model
        $modelType = request('modelType');                  //  Associated model e.g) user, company

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO GET PHONES        *
         ******************************************************/

        if (!$modelType) {
            //  Model type not specified - Log the error
            $response = oq_api_notify_error('Include model type e.g) user, company, e.t.c', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        } elseif (!$modelId) {
            //  Model id not specified - Log the error
            $response = oq_api_notify_error('Include associated model id e.g) 1, 2, 3 e.t.c', null, 404);

            //  Return the error response
            return ['success' => false, 'response' => $response];
        } else {
            //  Create the dynamic model

            $dynamicModel = oq_generateDynamicModel($modelType);

            //  Check if this is a valid dynamic class
            if (class_exists($dynamicModel)) {
                //  Find the associated record by model id
                try {
                    $dynamicModel = $dynamicModel::find($modelId);

                    //  Get associated phones
                    $phones = $dynamicModel->phones()->get();

                    //  Action was executed successfully
                    return ['success' => true, 'response' => $phones];
                } catch (\Exception $e) {
                    //  Log the error
                    $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

                    //  Return the error response
                    return ['success' => false, 'response' => $response];
                }
            } else {
                //  Model class does not exist - Log the error
                $response = oq_api_notify_error('Invalid model - e.g) must be company/user', null, 404);

                //  Return the error response
                return ['success' => false, 'response' => $response];
            }
        }
    }

    /*  initiateUpdate() method:
     *
     *  This is used to update an existing phone. It also works
     *  to store the update activity and broadcasting of
     *  notifications to users concerning the update of
     *  the phone.
     *
     */
    public function initiateUpdate($phone_id)
    {
        //  Current authenticated user
        $auth_user = auth('api')->user();

        /*
         *  The $phone is a collection of the phone to be stored.
         */
        $phone = request('phone');

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE PHONE      *
         ******************************************************/

        /*******************************************
         *   VALIDATE PHONE INFORMATION            *
         *******************************************/

        //  Create a template to hold the phone details
        $template = [
            'type' => $phone['type'] ?? null,
            'provider' => $phone['provider'] ?? null,
            'number' => $phone['number'] ?? null,
            'calling_code' => $phone['calling_code'] ?? null,
            'company_branch_id' => $user->companyBranch->id ?? null,
            'company_id' => $user->companyBranch->company->id ?? null,
            'created_by' => $user->id ?? null,
        ];

        try {
            //  Update the phone
            $phone = $this->where('id', $phone_id)->first()->update($template);

            //  If the phone was updated successfully
            if ($phone) {
                //  re-retrieve the instance to get all of the fields in the table.
                $phone = $this->where('id', $phone_id)->first();

                /*****************************
                 *   SEND NOTIFICATIONS      *
                 *****************************/

                // $auth_user->notify(new PhoneUpdated($phone));

                /*****************************
                 *   RECORD ACTIVITY         *
                 *****************************/

                //  Record activity of phone updated
                $status = 'updated';
                $phoneUpdatedActivity = oq_saveActivity($phone, $auth_user, $status, ['phone' => $phone->summarize()]);

                //  Action was executed successfully
                return ['success' => true, 'response' => $phone];
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
