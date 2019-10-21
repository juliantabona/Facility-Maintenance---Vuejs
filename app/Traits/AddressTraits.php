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


    /*  initiateCreate() method:
     *
     *  This method is used to create a new address.
     */
    public function initiateCreate( $template = null )
    {
        /*
         *  The $address variable represents the address dataset
         *  provided through the request received.
         */
        $address = request('address');

        /*
         *  The $template variable represents structure of the address.
         *  If no template is provided, we create one using the 
         *  request data.
         */
        $template = $template ?? [
            'address_1' => $address['address_1'] ?? null,
            'address_2' => $address['address_2'] ?? null,
            'country' => $address['country'] ?? null,
            'province' => $address['province'] ?? null,
            'city' => $address['city'] ?? null,
            'postal_or_zipcode' => $address['postal_or_zipcode'] ?? null
        ];

        try {

            /*
             *  Create a new address, then retrieve a fresh instance
             */
            $address = $this->create($template)->fresh();

            /*  If the address was created successfully  */
            if( $address ){   

                /*  Return a fresh instance of the address  */
                return $address->fresh();;

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

}
