<?php

namespace App\Http\Controllers\Api;

use DB;
use App\UssdService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UssdServiceController extends Controller
{

    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getUssdService( $ussd_service_id )
    {
        //  Get the ussd service
        $ussdService = UssdService::where('id', $ussd_service_id)->first() ?? null;

        //  Check if the ussd service exists
        if ($ussdService) {

            //  Check if the user is authourized to view the ussd service
            if ($this->user->can('view', $ussdService)) {

                //  Return an API Readable Format of the UssdService Instance
                return $ussdService->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function updateUssdService( Request $request, $ussd_service_id )
    {
        //  Get the ussd service
        $ussdService = UssdService::where('id', $ussd_service_id)->first() ?? null;

        //  Check if the ussd service exists
        if ($ussdService) {

            //  Check if the user is authourized to update the ussd service
            if ($this->user->can('update', $ussdService)) {

                //  Update the ussd service
                $updated = $ussdService->update( $request->all() );

                //  If the update was successful
                if( $updated ){

                    //  Return an API Readable Format of the UssdService Instance
                    return $ussdService->fresh()->convertToApiFormat();

                }

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }

        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

}
