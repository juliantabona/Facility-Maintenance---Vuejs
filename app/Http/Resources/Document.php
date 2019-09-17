<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Store as StoreResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Product as ProductResource;
use App\Http\Resources\Company as CompanyResource;

class Document extends JsonResource
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
            'name' => $this->name,
            'mime' => $this->mime,
            'size' => $this->size,
            'url' => $this->url,

            /*  Resource Links */
            '_links' => [

                //  Link to current resource
                'self' => [ 
                    'href' => url()->full(),
                    'title' => 'This document'
                ],
                
                //  Link to the resource that owns this document
                'owner' =>  
                        function(){
                            switch ($this->owner_type) {
                                case 'user':
                                    return [ 
                                        'href' => ($this->owner_id == auth('api')->user()->id)  
                                            ? route('my-account')
                                                : route('user', ['user_id' => $this->owner_id]),
                                        'title' => 'The user that owns this document'
                                    ];
                                case 'store':
                                    return [ 
                                        'href' => route('store', ['store_id' => $this->owner_id]),
                                        'title' => 'The store that owns this document'
                                    ];
                                case 'order':
                                    return [ 
                                        'href' => route('order', ['order_id' => $this->owner_id]),
                                        'title' => 'The order that owns this document'
                                    ];
                                case 'product':
                                    return [ 
                                        'href' => route('product', ['product_id' => $this->owner_id]),
                                        'title' => 'The product that owns this document'
                                    ];
                                case 'company':
                                    return [ 
                                        'href' => route('company', ['company_id' => $this->owner_id]),
                                        'title' => 'The company that owns this document'
                                    ];
                                default: null;
                            }
                        }

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
