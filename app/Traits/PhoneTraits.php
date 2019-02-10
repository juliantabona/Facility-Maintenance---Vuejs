<?php

namespace App\Traits;

trait PhoneTraits
{
    public function initiateCreate()
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $phone = request('phone');
        $modelId = request('modelId');                      //  The id of the associated model
        $modelType = request('modelType');                  //  Associated model e.g) invoice

        /*******************************************************
         *   CHECK IF USER HAS PERMISSION TO CREATE INVOICE    *
         ******************************************************/

        /*********************************************
         *   VALIDATE INVOICE INFORMATION            *
         ********************************************/

        if (!$modelType) {
            //  Model type not specified
            return oq_api_notify_error('Include model type e.g) user, company, e.t.c', null, 404);
        } elseif (!$modelId) {
            //  Model id not specified
            return oq_api_notify_error('Include associated model id e.g) 1, 2, 3 e.t.c', null, 404);
        } elseif (!$phone) {
            //  Phone details not specified
            return oq_api_notify_error('Include phone details', null, 404);
        } else {
            //  Create the dynamic model

            $dynamicModel = $this->generateDynamicModel($modelType);

            //  Check if this is a valid dynamic class
            if (class_exists($dynamicModel)) {
                //  Find the associated record by model id
                try {
                    $dynamicModel = $dynamicModel::find($modelId);
                } catch (\Exception $e) {
                    return oq_api_notify_error('Query Error', $e->getMessage(), 404);
                }

                //  Check if we have a record returned
                if ($dynamicModel) {
                    //  Model record exists

                    $template = [
                        'type' => $phone['type'],
                        'number' => $phone['number'],
                        'calling_code' => $phone['calling_code'],
                        'company_branch_id' => $user->companyBranch->id,
                        'company_id' => $user->companyBranch->company->id,
                        'created_by' => $user->id,
                    ];

                    //  Create the phone number
                    try {
                        $phone = $dynamicModel->phones()->create($template);
                    } catch (\Exception $e) {
                        return oq_api_notify_error('Query Error', $e->getMessage(), 404);
                    }

                    //  Record activity of a phone created
                    $status = 'created';
                    $invoiceCreatedActivity = oq_saveActivity($phone, $user, $status, $template);

                    //  re-retrieve the instance to get all of the fields in the table.
                    $phone = $phone->fresh();

                    //  return phone number
                    return oq_api_notify($phone, 201);
                } else {
                    //  Model record does not exist
                    return oq_api_notify_error('Query Error', $e->getMessage(), 404);
                }
            } else {
                //  Model class does not exist
                return oq_api_notify_error('Invalid model - e.g) must be company/user', null, 404);
            }
        }
    }

    public function addAndReplace($model, $phones)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        foreach ($phones as $key => $phone) {
            $phones[$key] = new \App\Phone([
                'number' => $phone['number'],
                'calling_code' => $phone['calling_code'],
                'type' => $phone['type'],
                'company_branch_id' => $user->company_branch_id,
                'company_id' => $user->company_id,
                'created_by' => $user->id,
                'trackable_id' => $model->id,
                'trackable_type' => $this->getMorphClass(),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        try {
            //  Delete old phones
            $deleted = $model->phones()->delete();

            //  Insert new phones
            $phones = $model->phones()->saveMany($phones);

            //  If the phones were updated successfully
            if ($phones) {
                //  Action was executed successfully
                return ['success' => true, 'response' => $phones];
            }
        } catch (\Exception $e) {
            $response = oq_api_notify_error('Query Error', $e->getMessage(), 404);

            return ['success' => false, 'response' => $response];
        }
    }

    public function generateDynamicModel($modelType)
    {
        //  Create the dynamic model
        $dynamicModel = ('\App\\'.ucfirst($modelType));  //  \App\User

        //  Check if this is a valid dynamic class
        if (class_exists($dynamicModel)) {
            //  Model class does exist

            return $dynamicModel;
        } else {
            return false;
        }
    }
}
