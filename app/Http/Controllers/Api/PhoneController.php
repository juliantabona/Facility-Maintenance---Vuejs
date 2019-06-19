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
        //  Phone Instance
        $data = ( new Phone() )->initiateCreate();
        $success = $data['success'];
        $response = $data['response'];

        //  If the phones were created successfully
        if ($success) {
            //  If this is a success then we have the phones
            $phones = $response;

            //  Action was executed successfully
            return oq_api_notify($phones, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function update($phone_id)
    {
        //  Phone Instance
        $data = ( new Phone() )->initiateUpdate($phone_id);
        $success = $data['success'];
        $response = $data['response'];

        //  If the phone was updated successfully
        if ($success) {
            //  If this is a success then we have the phone
            $phone = $response;

            //  Action was executed successfully
            return oq_api_notify($phone, 200);
        }

        //  If the data was not a success then return the response
        return $response;
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

            $phoneDelectedActivity = oq_saveActivity($phone, $user, $status);

            //  Delete the phone
            $phone = $phone->delete();

            //  return response
            return oq_api_notify($phone, 204);
        } catch (\Exception $e) {
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);
        }
    }
}
