<?php

namespace App\Traits;

use App\Http\Resources\Address as AddressResource;
use App\Http\Resources\Addresses as AddressesResource;

trait AddressTraits
{
    private $address;

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
     *  The $addressInfo variable represents the  
     *  address dataset provided
     */
    public function initiateCreate( $addressInfo = null )
    {
        /*
         *  The $template variable represents accepted structure of the 
         *  address data required to create a new resource.
         */
        $template = [
            'address_1' => $addressInfo['address_1'] ?? null,
            'address_2' => $addressInfo['address_2'] ?? null,
            'country' => $addressInfo['country'] ?? null,
            'province' => $addressInfo['province'] ?? null,
            'city' => $addressInfo['city'] ?? null,
            'postal_or_zipcode' => $addressInfo['postal_or_zipcode'] ?? null
        ];

        try {

            /*
             *  Create a new address, then retrieve a fresh instance
             */
            $this->address = $this->create($template)->fresh();

            /*  If the address was created successfully  */
            if( $this->address ){   

                /*  Return a fresh instance of the address  */
                return $this->address->fresh();;

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

}
