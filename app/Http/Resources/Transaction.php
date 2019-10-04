<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Transaction extends JsonResource
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
            'type' => $this->type,
            'status' => $this->status,
            'automatic' => $this->automatic,
            'payment_type' => $this->payment_type,
            'payment_amount' => $this->payment_amount,
            'meta' => $this->meta,
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
                    'title' => 'This transaction'
                ],

                //  Link to the resource owner
                'owner' =>  
                    (function() {
                        switch ($this->owner_type) {
                            case 'invoice':
                                return [ 
                                    'href' => route('invoice', ['invoice_id' => $this->owner_id]),
                                    'title' => 'The invoice that owns this transaction'
                                ];
                            default: null;
                        }
                    })(),

            ]

        ];
    }
}
