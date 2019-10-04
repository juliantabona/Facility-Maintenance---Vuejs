<?php

namespace App\Http\Controllers\Api;

use App\Phone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhoneController extends Controller
{

    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getPhones()
    {
        //  Check if the user is authourized to view all phones
        if ($this->user->can('viewAll', Phone::class)) {
        
            //  Get the phones
            $phones = Phone::paginate();

            //  Check if the phones exist
            if ($phones) {

                //  Return an API Readable Format of the Phone Instance
                return ( new \App\Phone )->convertToApiFormat($phones);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getPhone( $phone_id )
    {
        //  Get the phone
        $phone = Phone::where('id', $phone_id)->first() ?? null;

        //  Check if the phone exists
        if ($phone) {

            //  Check if the user is authourized to view the phone
            if ($this->user->can('view', $phone)) {

                //  Return an API Readable Format of the Phone Instance
                return $phone->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  OWNER RELATED RESOURCES      *
    *********************************/

    public function getPhoneOwner( $phone_id )
    {
        //  Get the phone
        $phone = Phone::findOrFail($phone_id);

        //  Get the phone owner
        $owner = $phone->owner ?? null;

        //  Check if the owner exists
        if ($owner) {

            //  Check if the user is authourized to view the phone owner
            if ($this->user->can('view', $phone)) {

                //  Return an API Readable Format of the Model Instance
                return $owner->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  WALLET RELATED RESOURCES     *
    *********************************/

    public function getPhoneWallet( $phone_id )
    {
        //  Get the phone
        $phone = Phone::findOrFail($phone_id);

        //  Get the phone wallet
        $wallet = $phone->wallet ?? null;

        //  Check if the wallet exists
        if ($wallet) {

            //  Check if the user is authourized to view the phone wallet
            if ($this->user->can('view', $phone)) {

                //  Return an API Readable Format of the Model Instance
                return $wallet->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }



    /*
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
    */
}
