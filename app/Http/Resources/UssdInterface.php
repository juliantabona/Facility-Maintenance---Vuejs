<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Store as StoreResource;
use App\Http\Resources\Company as CompanyResource;

class UssdInterface extends JsonResource
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
            'description' => $this->description,
            'code' => $this->code,
            'live_mode' => $this->live_mode,
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
                    'title' => 'This ussd interface'
                ],

                //  Link to the resource that owns this ussd interface owner
                'owner' =>  [ 
                    'href' => route('store', ['store_id' => $this->store_id]),
                    'title' => 'The store that owns this ussd interface'
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