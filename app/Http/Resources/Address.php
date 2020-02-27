<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Store as StoreResource;
use App\Http\Resources\Company as CompanyResource;

class Address extends JsonResource
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
            'address_1' => $this->address_1,
            'address_2' => $this->address_2,
            'country' => $this->country,
            'province' => $this->province,
            'city' => $this->city,
            'postal_or_zipcode' => $this->postal_or_zipcode,
            'metadata' => $this->metadata,
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
                    'title' => 'This address'
                ],

                //  Link to the resource that owns this address
                'owner' =>  
                        function(){
                            switch ($this->addressable_type) {
                                case 'user':
                                    [ 
                                        'href' => ($this->addressable_id == auth('api')->user()->id)  
                                            ? route('my-account')
                                                : route('user', ['user_id' => $this->addressable_id]),
                                        'title' => 'The user that owns this address'
                                    ];
                                case 'store':
                                    [ 
                                        'href' => route('store', ['store_id' => $this->addressable_id]),
                                        'title' => 'The store that owns this address'
                                    ];
                                case 'company':
                                    [ 
                                        'href' => route('company', ['company_id' => $this->addressable_id]),
                                        'title' => 'The company that owns this address'
                                    ];
                                default: null;
                            }
                        }
            ],

            /*  Embedded Resources */
            '_embedded' => [

                //  The resource that owns the address
                'owner' =>  
                    $this->when( !empty($this->owner),
                        function(){
                            switch ($this->addressable_type) {
                                case 'user':
                                    return  (new UserResource($this->owner)); break;
                                case 'store':
                                    return  (new StoreResource($this->owner)); break;
                                case 'company':
                                    return  (new CompanyResource($this->owner)); break;
                                default: null;
                            }
                        }
                )
                
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