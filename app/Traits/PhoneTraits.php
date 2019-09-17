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

    public function initiateCreate($modelId=null, $modelType=null, $phones=null, $replace=false){
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $phones = $phones ? $phones : request('phones');
        $modelId = $modelId ? $modelId : request('modelId');                      //  The id of the associated model
        $modelType = $modelType ? $modelType : request('modelType');              //  Associated model e.g) user, company
        $replace = $replace ? $replace : request('replace');                      //  Whether to delete the old phones

        /******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE PHONES    *
         ******************************************************/

        /*********************************************
         *   VALIDATE PHONE INFORMATION            *
         ********************************************/

        if (!$modelType) {
            //  Model type not specified
            return ['success' => false, 'response' => oq_api_notify_error('Include model type e.g) user, company, e.t.c', null, 404)];
        } elseif (!$modelId) {
            //  Model id not specified
            return ['success' => false, 'response' => oq_api_notify_error('Include associated model id e.g) 1, 2, 3 e.t.c', null, 404)];
        } elseif (!$phones) {
            //  Phone details not specified
            return ['success' => false, 'response' => oq_api_notify_error('Include phone details. Must be an array of phones', null, 404)];
        } else {
            //  Create the dynamic model
            $dynamicModel = ('\App\\'.ucfirst($modelType));  //  \App\User

            //  Check if this is a valid dynamic class (If the model class exists)
            if (class_exists($dynamicModel)) {

                //  Find the associated record by model id
                try {
                    $dynamicModel = $dynamicModel::find($modelId);
                } catch (\Exception $e) {
                    return ['success' => false, 'response' => oq_api_notify_error('Query Error', $e->getMessage(), 404)];
                }

                //  Check if we have a record returned
                if ($dynamicModel) {

                    //  Array to hold all the phones saved into the database
                    $savedPhones = [];

                    //  Create the phone number
                    try {

                        //  Delete the old phones if we are replacing
                        if($replace){
                            //  Delete old phones
                            $deleted = $dynamicModel->phones()->delete();
                        }
                        
                        foreach($phones as $phone){

                            //  Model record exists
                            $template = [
                                'type' => $phone['type'] ?? null,
                                'provider' => $phone['provider'] ?? null,
                                'number' => $phone['number'] ?? null,
                                'calling_code' => $phone['calling_code'] ?? null,
                                'company_branch_id' => $user->companyBranch->id ?? null,
                                'company_id' => $user->companyBranch->company->id ?? null,
                                'created_by' => $user->id ?? null,
                            ];

                            //  Create new phone
                            $phone = $dynamicModel->phones()->create($template);

                            //  Record activity of a phone created
                            $status = 'created';
                            $phoneCreatedActivity = oq_saveActivity($phone, $user, $status, $template);

                            //  re-retrieve the instance to get all of the fields in the table.
                            $phone = $phone->fresh();

                            array_push($savedPhones, $phone);
                        }

                    } catch (\Exception $e) {
                        return ['success' => false, 'response' => oq_api_notify_error('Query Error', $e->getMessage(), 404)];
                    }

                    //  return phone number
                    return ['success' => true, 'response' => $savedPhones];

                } else {
                    //  Model record does not exist
                    return ['success' => false, 'response' => oq_api_notify_no_resource()];
                }
            } else {
                //  Model class does not exist
                return ['success' => false, 'response' => oq_api_notify_error('Class "'.$modelType.'" does not exist.', null, 404)];
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
