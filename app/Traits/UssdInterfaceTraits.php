<?php

namespace App\Traits;

use App\Http\Resources\UssdInterface as UssdInterfaceResource;
use App\Http\Resources\UssdInterfaces as UssdInterfacesResource;

trait UssdInterfaceTraits
{
    private $ussdInterface;

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($ussd_interfaces = null)
    {

        try {

            if( $ussd_interfaces ){

                //  Transform the ussd stores
                return new UssdInterfacesResource($ussd_interfaces);

            }else{

                //  Transform the ussd store
                return new UssdInterfaceResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new interface.
     *  The $ussdInterfaceInfo variable represents the  
     *  USSD Interface dataset provided
     */
    public function initiateCreate( $ussdInterfaceInfo = null )
    {
        /*
         *  The $template variable represents accepted structure of the USSD 
         *  Interface data required to create a new resource.
         */
        $template = [
            'name' => $ussdInterfaceInfo['name'] ?? null,
            'call_to_action' => $ussdInterfaceInfo['call_to_action'] ?? 'Buy Now',
        ];

        try {

            /*
             *  Create a new Ussd Interface, then retrieve a fresh instance
             */
            $this->ussdInterface = $this->create($template)->fresh();

            /*  If the interface was created successfully  */
            if( $this->ussdInterface ){

                /*  Set the USSD Code  */
                $this->ussdInterface->setUssdCode();

                /*  Return a fresh instance of the interface  */
                return $this->ussdInterface->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

    /*  setUssdCode()
     *
     *  This method creates a unique USSD Code using the USSD Interface Id.
     *  It does this by padding the unique USSD Interface Id with leading 
     *  zero's "0" so that the order number is always atleast 3 digits 
     *  long
     */
    public function setUssdCode()
    {
        /*  Generate a unique order number.
         *  Get the order id, and Pad the left side with leading "0"
         *  e.g 1 = 001, 12 = 012, 123 = 123
         */
        $code = str_pad($this->id, 3, 0, STR_PAD_LEFT);

        /*  Set the unique order number  */
        $this->update(['code' => $code]);
    }

}
