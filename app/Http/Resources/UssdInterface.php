<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UssdInterface extends JsonResource
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
            'about_us' => $this->about_us,
            'contact_us' => $this->contact_us,
            'call_to_action' => $this->call_to_action,
            'code' => $this->code,
            'live_mode' => $this->live_mode,
            'allow_delivery' => $this->allow_delivery,
            'delivery_policy' => $this->delivery_policy,
            'metadata' => $this->metadata,
            
            /*  Attributes */
            'customer_access_code' => $this->customer_access_code,
            'team_access_code' => $this->team_access_code,
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
                    'href' => route('ussd-interface', ['ussd_interface_id' => $this->id]),
                    'title' => 'This ussd interface',
                ],

                //  Link to Ussd Interface products
                'oq:products' => [
                    'href' => route('ussd-interface-products', ['ussd_interface_id' => $this->id]),
                    'title' => 'The ussd interface products',
                    'total' => $this->products()->count(),
                ],

                //  Link to the resource that owns this ussd interface owner
                'owner' => [
                    'href' => route('store', ['store_id' => $this->store_id]),
                    'title' => 'The store that owns this ussd interface',
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
