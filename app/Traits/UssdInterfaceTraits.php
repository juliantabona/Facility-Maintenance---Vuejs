<?php

namespace App\Traits;

use App\Http\Resources\UssdInterface as UssdInterfaceResource;
use App\Http\Resources\UssdInterfaces as UssdInterfacesResource;

trait UssdInterfaceTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($ussd_stores = null)
    {

        try {

            if( $ussd_stores ){

                //  Transform the ussd stores
                return new UssdInterfacesResource($ussd_stores);

            }else{

                //  Transform the ussd store
                return new UssdInterfaceResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }
}
