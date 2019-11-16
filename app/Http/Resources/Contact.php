<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Store as StoreResource;
use App\Http\Resources\Company as CompanyResource;

class Contact extends JsonResource
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
            'name' => $this->name,
            'is_vendor' => $this->is_vendor,
            'is_customer' => $this->is_customer,
            'is_individual' => $this->is_individual,
            'account_id' => $this->account_id,

            /*  Attributes */
            'type' => $this->type,
            'phone_list' => $this->phone_list, 
            'default_email' => $this->default_email, 
            'default_mobile' => $this->default_mobile, 
            'default_address' => $this->default_address,
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
                    'title' => 'This contact'
                ],

                //  Link to the resource that owns this contact
                'owner' =>  
                        function(){
                            switch ($this->owner_type) {
                                case 'store':
                                    return [ 
                                        'href' => route('store', ['store_id' => $this->owner_id]),
                                        'title' => 'The store that owns this contact'
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