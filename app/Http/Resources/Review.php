<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\Store as StoreResource;
use App\Http\Resources\Company as CompanyResource;

class Review extends JsonResource
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
            'rating' => $this->rating,
            'text' => $this->text,
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
                    'title' => 'This review'
                ]
            ],

            /*  Embedded Resources */
            '_embedded' => [

                //  The resource that received the review
                'review_to' =>  
                    $this->when( !empty($this->owner),
                        function(){
                            switch ($this->reviewable_type) {
                                case 'store':
                                    return  (new StoreResource($this->owner)); break;
                                case 'company':
                                    return  (new CompanyResource($this->owner)); break;
                                default: null;
                            }
                        }
                ),

                //  The user who placed this review
                'reviewed_by' => $this->when( !empty($this->user),  
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