<?php

namespace App\Http\Controllers\Api;

use App\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DiscountController extends Controller
{

    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getDiscounts()
    {
        //  Check if the user is authourized to view all discounts
        if ($this->user->can('viewAll', Discount::class)) {
        
            //  Get the discounts
            $discounts = Discount::paginate();

            //  Check if the discounts exist
            if ($discounts) {

                //  Return an API Readable Format of the Discount Instance
                return ( new \App\Discount )->convertToApiFormat($discounts);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getDiscount( $discount_id )
    {
        //  Get the discount
        $discount = Discount::where('id', $discount_id)->first() ?? null;

        //  Check if the discount exists
        if ($discount) {

            //  Check if the user is authourized to view the discount
            if ($this->user->can('view', $discount)) {

                //  Return an API Readable Format of the Discount Instance
                return $discount->convertToApiFormat();

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

    public function getDiscountOwner( $discount_id )
    {
        //  Get the discount
        $discount = Discount::findOrFail($discount_id);

        //  Get the discount owner
        $owner = $discount->owner ?? null;

        //  Check if the owner exists
        if ($owner) {

            //  Check if the user is authourized to view the discount owner
            if ($this->user->can('view', $discount)) {

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

    public function getDiscountProducts( $discount_id )
    {
        //  Get the discount
        $discount = Discount::findOrFail($discount_id);

        //  Get the discount products
        $products = $discount->products()->paginate() ?? null;

        //  Check if the child discounts exist
        if ($products) {

            //  Check if the user is authourized to view the discount products
            if ($this->user->can('view', $discount)) {

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

    public function getDiscountProduct( $discount_id )
    {
        //  Get the discount
        $discount = Discount::findOrFail($discount_id);

        //  Get the discount product
        $product = $discount->products()->where('products.id', $discount_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to view the discount product
            if ($this->user->can('view', $discount)) {

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
