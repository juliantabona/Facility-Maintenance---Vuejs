<?php

namespace App\Traits;

use App\Http\Resources\Discount as DiscountResource;
use App\Http\Resources\Discounts as DiscountsResource;

trait DiscountTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($discounts = null)
    {

        try {

            if( $discounts ){

                //  Transform the discounts
                return new DiscountsResource($discounts);

            }else{

                //  Transform the discount
                return new DiscountResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

}
