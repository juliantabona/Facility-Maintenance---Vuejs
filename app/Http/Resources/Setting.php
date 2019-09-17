<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Setting extends JsonResource
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
            'details' => $this->details,

            /*  Resource Links */
            '_links' => [

                //  Link to current resource
                'self' => [ 
                    'href' => url()->full(),
                    'title' => 'These settings'
                ],

                //  Link to the owning user
                'user' => $this->when($this->owner_type == 'user', [ 
                            'href' => ($this->owner_id == auth('api')->user()->id)  
                                ? route('my-account') 
                                    : route('user', ['user_id' => $this->owner_id]),
                            'title' => 'The owner of these settings'
                        ]),

                //  Link to the owning company
                'company' => $this->when($this->owner_type == 'company', [ 
                    'href' => route('company', ['company_id' => $this->owner_id]),
                    'title' => 'The owner of these settings'
                ]),

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
