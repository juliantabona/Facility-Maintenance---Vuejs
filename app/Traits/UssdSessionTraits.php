<?php

namespace App\Traits;

use App\Http\Resources\UssdSession as UssdSessionResource;
use App\Http\Resources\UssdSessions as UssdSessionsResource;

trait UssdSessionTraits
{
    private $ussd_session_instance;

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($ussd_sessions = null)
    {

        try {

            if( $ussd_sessions ){

                //  Transform the ussd sessions
                return new UssdSessionsResource($ussd_sessions);

            }else{

                //  Transform the ussd session
                return new UssdSessionResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }
}
