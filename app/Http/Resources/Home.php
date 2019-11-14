<?php

namespace App\Http\Resources;

use App\Http\Resources\User as UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Home extends JsonResource
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

            /*  Resource Links */
            '_links' => [
                
                'curies' => [
                    [ 'name' => 'oq', 'href' => 'https://oqcloud.co.bw/docs/rels/{rel}', 'templated' => true ]
                ],

                //  Link to current resource
                'self' => [ 
                    'href' => url()->full(),
                    'title' => 'API Home - Your API starting point.'
                ]

            ],

            /*  Embedded Resources */
            '_embedded' => [
                
                //  The store logo (Show only if logo exists)
                'me' => ($user = auth('api')->user()) ? (new UserResource($user)) : null
                
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