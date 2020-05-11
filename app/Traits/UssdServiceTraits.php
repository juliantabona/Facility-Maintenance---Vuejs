<?php

namespace App\Traits;

use App\Http\Resources\UssdService as UssdServiceResource;
use App\Http\Resources\UssdServices as UssdServicesResource;

trait UssdServiceTraits
{
    private $ussdService;

    public $default_timeout_message = 'TIMEOUT: You have exceeded your session time limit';

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($ussd_services = null)
    {

        try {

            if( $ussd_services ){

                //  Transform the Ussd Services
                return new UssdServicesResource($ussd_services);

            }else{

                //  Transform the Ussd Service
                return new UssdServiceResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new Ussd Service.
     *  The $ussdServiceInfo variable represents the  
     *  Ussd Service dataset provided
     */
    public function initiateCreate( $ussdServiceInfo = null )
    {
        /*
         *  The $template variable represents accepted structure of the Ussd
         *  Service data required to create a new resource.
         */
        $template = [
            'name' => $ussdServiceInfo['name'] ?? null,
            'builder' => [
                'screens' => [],
                'markers' => [],
                'subscriptions' => [],
                'simulator' => [
                    'debugger' => [
                        'return_logs' => true,
                        'return_log_types' => ['info', 'warning', 'error']
                    ],
                    'subscriber' => [
                        'phone_number' => ''    //  Add the authenticated users phone number
                    ],
                    'settings' => [
                        'allow_timouts' => true,
                        'timeout_limit_in_seconds' => '120',
                        'timeout_message' => $this->default_timeout_message
                    ]
                ]
            ]
        ];

        try {

            /*
             *  Create a new Ussd Service, then retrieve a fresh instance
             */
            $this->ussdService = $this->create($template)->fresh();

            /*  If the Ussd Service was created successfully  */
            if( $this->ussdService ){

                /*  Create a new USSD Service Code  */
                $this->ussdService->createUssdServiceCode();

                /*  Return a fresh instance of the Ussd Service  */
                return $this->ussdService->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

    /*  createUssdServiceCode() method:
     *
     *  This method is used to create a new USSD Service Code e.g *321*1#
     */
    public function createUssdServiceCode()
    {
        /*  Create a new USSD Service Code using the initiateCreate() method from the UssdServiceCodeTraits  */
        $ussdServiceCode = ( new \App\UssdServiceCode() )->initiateCreate($ussdServiceCodeInfo = $this);

        /*  If the USSD Service Code was created successfully  */
        if ($ussdServiceCode) {

            /*  Assign the new USSD Service Code to the Ussd Service  */
            $ussdServiceCode->update([
                'owner_id' => $this->id,
                'owner_type' => $this->resource_type,
            ]);

        }

    }

}
