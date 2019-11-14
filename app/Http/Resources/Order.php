<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Document as DocumentResource;
use App\Http\Resources\Documents as DocumentsResource;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [

            'id' => $this->id,
            /*  Basic Info  */
            'number' => $this->number, 
            'currency' => $this->currency, 
            'created_date' => $this->created_date, 
            'manual_status' => $this->manual_status,

            /*  Item Info  */
            'item_lines' => $this->item_lines,

            /*  Taxes, Disounts & Coupon Info  */
            'tax_lines' => $this->tax_lines, 
            'discount_lines' => $this->discount_lines, 
            'coupon_lines' => $this->coupon_lines,

            /*  Grand Total, Sub Total, Tax Total, Discount Total, Shipping Total  */
            'sub_total' => $this->sub_total, 
            'item_tax_total' => $this->item_tax_total, 
            'global_tax_total' => $this->global_tax_total, 
            'grand_tax_total' => $this->grand_tax_total, 
            'item_discount_total' => $this->item_discount_total,
            'global_discount_total' => $this->global_discount_total, 
            'grand_discount_total' => $this->grand_discount_total, 
            'shipping_total' => $this->shipping_total, 
            'grand_total' => $this->grand_total,

            /*  Reference Info  */
            'reference_id' => $this->reference_id, 
            'reference_ip_address' => $this->reference_ip_address, 
            'reference_user_agent' => $this->reference_user_agent,

            /*  Customer Info  */
            'customer_id' => $this->customer_id, 
            'customer_note' => $this->customer_note, 
            'billing_info' => $this->billing_info, 
            'shipping_info' => $this->shipping_info, 

            /*  Merchant Info  */
            'merchant_id' => $this->merchant_id, 
            'merchant_type' => $this->merchant_type, 
            'merchant_info' => $this->merchant_info, 

            /*  Meta Data  */
            'metadata' => $this->metadata, 

            /*  Additional Attributes   */ 
            'transaction_total' => $this->transaction_total, 
            'refund_total' => $this->refund_total, 
            'outstanding_balance' => $this->outstanding_balance, 
            'created_at_format' => $this->created_at_format, 
            'lifecycle_status_title' => $this->lifecycle_status_title, 
            'lifecycle_status_description' => $this->lifecycle_status_description, 
            'lifecycle_history' => $this->lifecycle_history, 
            'lifecycle_flow' => $this->lifecycle_flow, 
            'resource_type' => $this->resource_type, 
        
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /*  Resource Links */
            '_links' => [
                
                'curies' => [
                    [ 'name' => 'oq', 'href' => 'https://oqcloud.co.bw/docs/rels/{rel}', 'templated' => true ]
                ],

                //  Link to current resource
                'self' => [ 
                    'href' => url()->full(),
                    'title' => 'This order'
                ]
            ],

            /*  Embedded Resources */
            '_embedded' => [
                    
              
            ]
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response  $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->header('Content-Type', 'application/hal+json');
    }

}