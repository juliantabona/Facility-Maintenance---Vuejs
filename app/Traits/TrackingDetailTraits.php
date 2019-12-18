<?php

namespace App\Traits;

use App\Http\Resources\TrackingDetail as TrackingDetailResource;
use App\Http\Resources\TrackingDetails as TrackingDetailsResource;

trait TrackingDetailTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($trackingDetails = null)
    {

        try {

            if( $trackingDetails ){

                //  Transform the tracking details
                return new TrackingDetailsResource($trackingDetails);

            }else{

                //  Transform the tracking details
                return new TrackingDetailResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

}
