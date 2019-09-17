<?php

namespace App\Traits;

use App\Http\Resources\Coupon as CouponResource;
use App\Http\Resources\Coupons as CouponsResource;

trait CouponTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($coupons = null)
    {

        try {

            if( $coupons ){

                //  Transform the coupons
                return new CouponsResource($coupons);

            }else{

                //  Transform the coupon
                return new CouponResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

}
