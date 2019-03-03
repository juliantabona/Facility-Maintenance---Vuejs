<?php

namespace App\Http\Controllers\Api;

use App\Phone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhoneController extends Controller
{
    public function index(Request $request)
    {
        //  Phone Instance
        $data = ( new Phone() )->initiateGet();
        $success = $data['success'];
        $response = $data['response'];

        //  If the phones were found successfully
        if ($success) {
            //  If this is a success then we have the list of phones
            $phones = $response;

            //  Action was executed successfully
            return oq_api_notify($phones, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function store(Request $request)
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
            $dynamicModel = ('\App\\'.ucfirst($modelType));  //  \App\User

            //  Check if this is a valid dynamic class
            if (class_exists($dynamicModel)) {
                //  Model class does exist

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
                return oq_api_notify_error('Include supplier id', null, 404);
            }
        }
    }

    public function update(Request $request, $phone_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        //  Query data
        $phone = request('phone');

        /*************************************************************
         *   CHECK IF USER HAS PERMISSION TO UPDATE PHONE DETAILS    *
         ************************************************************/

        /**********************************
         *   VALIDATE PHONE DETAILS       *
         **********************************/

        if (!$phone) {
            //  Phone details not specified
            return oq_api_notify_error('Include phone details', null, 404);
        } else {
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
                $phone = Phone::find($phone_id)->update($template);
            } catch (\Exception $e) {
                return oq_api_notify_error('Query Error', $e->getMessage(), 404);
            }

            if ($phone) {
                $phone = Phone::find($phone_id);

                //  Record activity of a phone created
                $status = 'updated';
                $invoiceCreatedActivity = oq_saveActivity($phone, $user, $status, $template);

                //  return phone number
                return oq_api_notify($phone, 200);
            }
        }
    }

    public function delete(Request $request, $phone_id)
    {
        //  Current authenticated user
        $user = auth('api')->user();

        /*************************************************************
         *   CHECK IF USER HAS PERMISSION TO DELETE PHONE DETAILS    *
         ************************************************************/
        //  Delete the phone number
        try {
            //  Get the phone
            $phone = Phone::find($phone_id);

            //  Record activity of a phone deleted
            $status = 'deleted';

            $invoiceCreatedActivity = oq_saveActivity($phone, $user, $status);

            //  Delete the phone
            $phone = $phone->delete();

            //  return response
            return oq_api_notify($phone, 204);
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }
}
