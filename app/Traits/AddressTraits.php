<?php

namespace App\Traits;

use App\Http\Resources\Address as AddressResource;
use App\Http\Resources\Addresses as AddressesResource;

trait AddressTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($addresses = null)
    {

        try {

            if( $addresses ){

                //  Transform the addresses
                return new AddressesResource($addresses);

            }else{

                //  Transform the address
                return new AddressResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

}
