<?php

namespace App\Traits;

use App\Http\Resources\UssdServiceCode as UssdServiceCodeResource;
use App\Http\Resources\UssdServiceCodes as UssdServiceCodesResource;

trait UssdServiceCodeTraits
{
    private $ussdServiceCode;

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($ussd_service_codes = null)
    {

        try {

            if( $ussd_service_codes ){

                //  Transform the Ussd Service Codes
                return new UssdServiceCodesResource($ussd_service_codes);

            }else{

                //  Transform the Ussd Service Code
                return new UssdServiceCodeResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new Ussd Service Code.
     *  The $ussdServiceCodeInfo variable represents the  
     *  Ussd Service Code dataset provided
     */
    public function initiateCreate( $ussdServiceCodeInfo = null )
    {
        /*
         *  The $template variable represents accepted structure of the Ussd
         *  Service Code data required to create a new resource.
         */

        $template = [
            'shared_code' => $ussdServiceCodeInfo['shared_code'] ?? null,
            'dedicated_code' => $ussdServiceCodeInfo['dedicated_code'] ?? null,
        ];

        try {

            /*
             *  Create a new Ussd Service Code, then retrieve a fresh instance
             */
            $this->ussdServiceCode = $this->create($template)->fresh();

            /*  If the Ussd Service Code was created successfully  */
            if( $this->ussdServiceCode ){

                //  If the shared code was not provided
                if( empty( $this->ussdServiceCode->shared_code ) ){

                    //  Get the Main Ussd Service Code e.g *321#
                    $main_service_code = config('app.USSD_SERVICE_CODE');

                    //  Create a unique Shared Code from the Main Ussd Service Code e.g *321*45#
                    $unique_shared_code = str_replace('#', '', $main_service_code) .'*'. $this->ussdServiceCode.'#';

                    //  Update the Ussd Service Code with the unique service code
                    $this->ussdServiceCode->update([
                        'shared_code' => $unique_shared_code
                    ]);

                }

                /*  Return a fresh instance of the Ussd Service Code  */
                return $this->ussdServiceCode->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

}
