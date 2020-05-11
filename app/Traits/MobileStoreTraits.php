<?php

namespace App\Traits;

use App\Http\Resources\MobileStore as MobileStoreResource;
use App\Http\Resources\MobileStores as MobileStoresResource;

trait MobileStoreTraits
{
    private $mobileStore;

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($mobile_stores = null)
    {

        try {

            if( $mobile_stores ){

                //  Transform the mobile stores
                return new MobileStoresResource($mobile_stores);

            }else{

                //  Transform the mobile store
                return new MobileStoreResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new mobile store.
     *  The $mobileStoreInfo variable represents the  
     *  Mobile Store dataset provided
     */
    public function initiateCreate( $mobileStoreInfo = null )
    {
        /*
         *  The $template variable represents accepted structure of the mobile
         *  store data required to create a new resource.
         */
        $template = [
            'name' => $mobileStoreInfo['name'] ?? null,
            'call_to_action' => $mobileStoreInfo['call_to_action'] ?? 'Buy Now',
        ];

        try {

            /*
             *  Create a new Mobile Store, then retrieve a fresh instance
             */
            $this->mobileStore = $this->create($template)->fresh();

            /*  If the mobile store was created successfully  */
            if( $this->mobileStore ){

                /*  Create a new USSD Service Code  */
                $this->mobileStore->createUssdServiceCode();

                /*  Return a fresh instance of the mobile store  */
                return $this->mobileStore->fresh();

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

            /*  Assign the new USSD Service Code to the mobile store  */
            $ussdServiceCode->update([
                'owner_id' => $this->id,
                'owner_type' => $this->resource_type,
            ]);

        }

    }

}
