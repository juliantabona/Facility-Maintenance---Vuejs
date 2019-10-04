<?php

namespace App\Http\Controllers\Api;

use App\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CouponController extends Controller
{

    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getCoupons()
    {
        //  Check if the user is authourized to view all coupons
        if ($this->user->can('viewAll', Coupon::class)) {
        
            //  Get the coupons
            $coupons = Coupon::paginate();

            //  Check if the coupons exist
            if ($coupons) {

                //  Return an API Readable Format of the Coupon Instance
                return ( new \App\Coupon )->convertToApiFormat($coupons);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getCoupon( $coupon_id )
    {
        //  Get the coupon
        $coupon = Coupon::where('id', $coupon_id)->first() ?? null;

        //  Check if the coupon exists
        if ($coupon) {

            //  Check if the user is authourized to view the coupon
            if ($this->user->can('view', $coupon)) {

                //  Return an API Readable Format of the Coupon Instance
                return $coupon->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();
            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  OWNER RELATED RESOURCES      *
    *********************************/

    public function getCouponOwner( $coupon_id )
    {
        //  Get the coupon
        $coupon = Coupon::findOrFail($coupon_id);

        //  Get the coupon owner
        $owner = $coupon->owner ?? null;

        //  Check if the owner exists
        if ($owner) {

            //  Check if the user is authourized to view the coupon owner
            if ($this->user->can('view', $coupon)) {

                //  Return an API Readable Format of the Model Instance
                return $owner->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    /*********************************
     *  PRODUCT RELATED RESOURCES    *
    *********************************/

    public function getCouponProducts( $coupon_id )
    {
        //  Get the coupon
        $coupon = Coupon::findOrFail($coupon_id);

        //  Get the coupon products
        $products = $coupon->products()->paginate() ?? null;

        //  Check if the child coupons exist
        if ($products) {

            //  Check if the user is authourized to view the coupon products
            if ($this->user->can('view', $coupon)) {

                //  Return an API Readable Format of the Product Instance
                return ( new \App\Product )->convertToApiFormat($products);

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }

    public function getCouponProduct( $coupon_id )
    {
        //  Get the coupon
        $coupon = Coupon::findOrFail($coupon_id);

        //  Get the coupon product
        $product = $coupon->products()->where('products.id', $coupon_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to view the coupon product
            if ($this->user->can('view', $coupon)) {

                //  Return an API Readable Format of the Product Instance
                return $product->convertToApiFormat();

            } else {

                //  Not Authourized
                return oq_api_not_authorized();

            }
        }else{
            
            //  Not Found
            return oq_api_notify_no_resource();

        }
    }


}
