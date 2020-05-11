<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UssdServiceCode as UssdServiceCodeResource;


class UssdService extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name, 
            'live_mode' => $this->live_mode, 
            'offline_message' => $this->offline_message, 
    
            /*  Builder Data  */
            'builder' => $this->builder,
    
            /*  Ownership Information  */
            'owner_id' => $this->owner_id, 
            'owner_type' => $this->owner_type,
            
            /*  Attributes */
            'resource_type' => $this->resource_type,

            /*  Timestamps */
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /*  Resource Links */
            '_links' => [
                'curies' => [
                    ['name' => 'oq', 'href' => 'https://oqcloud.co.bw/docs/rels/{rel}', 'templated' => true],
                ],

                //  Link to current resource
                'self' => [
                    'href' => route('ussd-service', ['ussd_service_id' => $this->id]),
                    'title' => 'This ussd service',
                ],

                //  Link to the resource that owns this ussd service owner
                'owner' => [
                    'href' => route('store', [$this->owner_type.'_id' => $this->owner_id]),
                    'title' => 'The resource that owns this ussd service',
                ],

                //  Link to the ussd service builder
                'oq:ussd_service_builder' => [
                    'href' => route('ussd-service-builder'),
                    'title' => 'The ussd service builder',
                ],


                
            ],

            /*  Embedded Resources */
            '_embedded' => [

                'service_code' => $this->serviceCode,

            ],
        ];
    }

    /**
     * Customize the outgoing response for the resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Illuminate\Http\Response $response
     */
    public function withResponse($request, $response)
    {
        $response->header('Content-Type', 'application/hal+json');
    }
}
