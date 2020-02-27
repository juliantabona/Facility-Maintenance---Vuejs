<?php

namespace App\Http\Resources;

use App\Http\Resources\User as UserResource;
use App\Http\Resources\Order as OrderResource;
use App\Http\Resources\Store as StoreResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Message extends JsonResource
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
            'text' => $this->text,
            'metadata' => $this->metadata,
            'user_id' => $this->user_id,
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
                    'title' => 'This message'
                ],

                //  Link to the resource that received the message
                'message_to' =>  
                    (function() {
                        switch ($this->owner_type) {
                            case 'user':
                                return [ 
                                    'href' => ($this->owner_id == auth('api')->user()->id)  
                                        ? route('my-account')
                                            : route('user', ['user_id' => $this->owner_id]),
                                    'title' => 'The user that received this message'
                                ];
                            case 'store':
                                return [ 
                                    'href' => route('store', ['store_id' => $this->owner_id]),
                                    'title' => 'The store that received this message'
                                ];
                            case 'order':
                                return [ 
                                    'href' => route('order', ['order_id' => $this->owner_id]),
                                    'title' => 'The order that received this message'
                                ];
                            default: null;
                        }
                    })(),

                //  Link to user who sent this message
                'message_by' => [ 
                    'href' => ($this->user->id == auth('api')->user()->id)  
                        ? route('my-account')
                            : route('user', ['user_id' => $this->user->id]),
                    'title' => 'The user that received this message'
                ]

            ],

            /*  Embedded Resources */
            '_embedded' => [

                //  The resource that received the message
                'message_to' =>  
                    $this->when( !empty($this->owner),
                        (function() {
                            switch ($this->owner_type) {
                                case 'user':
                                    return  (new UserResource($this->owner)); break;
                                case 'store':
                                    return  (new StoreResource($this->owner)); break;
                                case 'order':
                                    return  (new OrderResource($this->owner)); break;
                                default: null;
                            }
                        })()
                ),

                //  The user who sent this message
                'message_by' => $this->when( !empty($this->user),  
                    (new UserResource($this->user))
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