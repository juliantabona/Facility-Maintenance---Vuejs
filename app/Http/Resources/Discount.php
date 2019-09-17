<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Store as StoreResource;
use App\Http\Resources\Company as CompanyResource;

class Discount extends JsonResource
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
            'type' => $this->type,
            'rate' => $this->rate,
            'meta' => $this->meta,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
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
                    'title' => 'This discount'
                ],

                //  Link to the resource that owns this discount
                'owner' =>  
                        function(){
                            switch ($this->discountable_type) {
                                case 'store':
                                    return [ 
                                        'href' => route('store', ['store_id' => $this->discountable_id]),
                                        'title' => 'The store that owns this discount'
                                    ];
                                case 'company':
                                    return [ 
                                        'href' => route('company', ['company_id' => $this->discountable_id]),
                                        'title' => 'The company that owns this discount'
                                    ];
                                default: null;
                            }
                        }

            ],

            /*  Embedded Resources */
            '_embedded' => [

                //  The resource that owns this discount
                'owner' =>  
                    $this->when( !empty($this->owner),
                        function(){
                            switch ($this->discountable_type) {
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