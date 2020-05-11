<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MobileStore extends JsonResource
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
            'about_us' => $this->about_us, 
            'contact_us' => $this->contact_us, 
            'call_to_action' => $this->call_to_action, 
            'allow_delivery' => $this->allow_delivery,
            'delivery_policy' => $this->delivery_policy, 
            'live_mode' => $this->live_mode, 
            'offline_message' => $this->offline_message,
    
            /*  Meta Data  */
            'metadata' => $this->metadata,
    
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
                    'href' => route('mobile-store', ['ussd_interface_id' => $this->id]),
                    'title' => 'This mobile store',
                ],

                //  Link to Mobile Store products
                'oq:products' => [
                    'href' => route('mobile-store-products', ['ussd_interface_id' => $this->id]),
                    'title' => 'The mobile store products',
                    'total' => $this->products()->count(),
                ],

                //  Link to the resource that owns this mobile store owner
                'owner' => [
                    'href' => route('store', ['store_id' => $this->owner_id]),
                    'title' => 'The store that owns this mobile store',
                ],
            ],

            /*  Embedded Resources */
            '_embedded' => [
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
