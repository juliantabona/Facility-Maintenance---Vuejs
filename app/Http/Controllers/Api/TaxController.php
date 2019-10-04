<?php

namespace App\Http\Controllers\Api;

use App\Tax;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TaxController extends Controller
{

    private $user;

    public function __construct()
    {
        //  Authenticated User
        $this->user = auth('api')->user();
    }

    public function getTaxes()
    {
        //  Check if the user is authourized to view all taxes
        if ($this->user->can('viewAll', Tax::class)) {
        
            //  Get the taxes
            $taxes = Tax::paginate();

            //  Check if the taxes exist
            if ($taxes) {

                //  Return an API Readable Format of the Tax Instance
                return ( new \App\Tax )->convertToApiFormat($taxes);

            }else{
                
                //  Not Found
                return oq_api_notify_no_resource();

            }

        } else {

            //  Not Authourized
            return oq_api_not_authorized();

        }
    }

    public function getTax( $tax_id )
    {
        //  Get the tax
        $tax = Tax::where('id', $tax_id)->first() ?? null;

        //  Check if the tax exists
        if ($tax) {

            //  Check if the user is authourized to view the tax
            if ($this->user->can('view', $tax)) {

                //  Return an API Readable Format of the Tax Instance
                return $tax->convertToApiFormat();

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

    public function getTaxOwner( $tax_id )
    {
        //  Get the tax
        $tax = Tax::findOrFail($tax_id);

        //  Get the tax owner
        $owner = $tax->owner ?? null;

        //  Check if the owner exists
        if ($owner) {

            //  Check if the user is authourized to view the tax owner
            if ($this->user->can('view', $tax)) {

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

    public function getTaxProducts( $tax_id )
    {
        //  Get the tax
        $tax = Tax::findOrFail($tax_id);

        //  Get the tax products
        $products = $tax->products()->paginate() ?? null;

        //  Check if the child taxes exist
        if ($products) {

            //  Check if the user is authourized to view the tax products
            if ($this->user->can('view', $tax)) {

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

    public function getTaxProduct( $tax_id )
    {
        //  Get the tax
        $tax = Tax::findOrFail($tax_id);

        //  Get the tax product
        $product = $tax->products()->where('products.id', $tax_id)->first() ?? null;

        //  Check if the product exists
        if ($product) {

            //  Check if the user is authourized to view the tax product
            if ($this->user->can('view', $tax)) {

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
