<?php

namespace App\Http\Controllers\Api;

use App\UssdInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UssdInterfaceController extends Controller
{

    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getUssdInterface( $ussdInterface_id )
    {
        //  Get the ussdInterface
        $ussdInterface = UssdInterface::where('id', $ussdInterface_id)->first() ?? null;

        //  Check if the ussdInterface exists
        if ($ussdInterface) {

            //  Check if the user is authourized to view the ussdInterface
            if ($this->user->can('view', $ussdInterface)) {

                //  Return an API Readable Format of the UssdInterface Instance
                return $ussdInterface->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function updateUssdInterface( Request $request, $ussdInterface_id )
    {

        //  Get the ussdInterface
        $ussdInterface = UssdInterface::where('id', $ussdInterface_id)->first() ?? null;

        //  Check if the ussdInterface exists
        if ($ussdInterface) {

            //  Check if the user is authourized to update the ussdInterface
            if ($this->user->can('update', $ussdInterface)) {

                //  Update the Ussd Interface
                $updated = $ussdInterface->update( $request->all() );

                //  If the update was successful
                if( $updated ){

                    //  Return an API Readable Format of the UssdInterface Instance
                    return $ussdInterface->fresh()->convertToApiFormat();

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
