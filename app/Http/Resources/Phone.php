<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Phone extends JsonResource
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
            'calling_code' => $this->calling_code,
            'number' => $this->number,
            'provider' => $this->provider,
            'default' => $this->default,
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
                    'title' => 'This phone'
                ],

                //  Link to the resource that owns this address
                'owner' =>  
                        function(){
                            switch ($this->owner_type) {
                                case 'user':
                                    return [ 
                                        'href' => ($this->owner_id == auth('api')->user()->id)  
                                            ? route('my-account')
                                                : route('user', ['user_id' => $this->owner_id]),
                                        'title' => 'The user that owns this phone'
                                    ];
                                case 'store':
                                    return [ 
                                        'href' => route('store', ['store_id' => $this->owner_id]),
                                        'title' => 'The store that owns this phone'
                                    ];
                                case 'company':
                                    return [ 
                                        'href' => route('company', ['company_id' => $this->owner_id]),
                                        'title' => 'The company that owns this phone'
                                    ];
                                default: null;
                            }
                        }
                
            ],

            /*  Embedded Resources */
            '_embedded' => [

                //  The resource that owns the phone
                'owner' =>  
                    $this->when( !empty($this->owner),
                        function(){
                            switch ($this->owner_type) {
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