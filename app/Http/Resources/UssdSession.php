<?php

namespace App\Http\Resources;

use App\Http\Resources\User as UserResource;
use App\Http\Resources\Store as StoreResource;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Company as CompanyResource;

class UssdSession extends JsonResource
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
            'session_id' => $this->session_id,
            'service_code' => $this->service_code,
            'phone_number' => $this->phone_number,
            'text' => $this->text,
            'metadata' => $this->metadata,

            /*  Attributes  */
            'status' => $this->status,
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
                    'title' => 'This ussd session'
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