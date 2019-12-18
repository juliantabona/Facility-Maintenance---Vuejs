<?php

namespace App\Traits;

use App\Http\Resources\Fulfillment as FulfillmentResource;
use App\Http\Resources\Fulfillments as FulfillmentsResource;

trait FulfillmentTraits
{
    private $fulfillmentInstance;

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($fulfillments = null)
    {

        try {

            if( $fulfillments ){

                //  Transform the fulfillments
                return new FulfillmentsResource($fulfillments);

            }else{

                //  Transform the fulfillment
                return new FulfillmentResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new fulfillment.
     *  The $fulfillmentInfo variable represents the  
     *  fulfillment dataset provided
     */
    public function initiateCreate( $fulfillmentInfo = null )
    {
        /*
         *  The $template variable represents accepted structure of the
         *  fulfillment data required to create a new resource.
         */
        $template = [


            //  Fulfillment notes 
            'notes' => $fulfillmentInfo['notes'] ?? null,

            //  Fulfillment item lines
            'item_lines' => $fulfillmentInfo['item_lines'] ?? null,
            
            //  Recipient name 
            'recipient_name' => $fulfillmentInfo['recipient_name'] ?? null,
            
            //  Recipient contact e.g Phone / Email 
            'recipient_contact' => $fulfillmentInfo['recipient_contact'] ?? null
            
        ];

        try {

            /*
             *  Create a new fulfillment, then retrieve a fresh instance
             */
            $this->fulfillmentInstance = $this->create($template)->fresh();

            /*  If the fulfillment was created successfully  */
            if( $this->fulfillmentInstance ){   

                /*  Return the fresh instance of the fulfillment  */
                return $this->fulfillmentInstance;

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

}
