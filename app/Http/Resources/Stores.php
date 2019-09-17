<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class Stores extends ResourceCollection
{
    /**
     * The resource that this resource collects.
     *
     * @var string
     */
    public $collects = 'App\Http\Resources\Store';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            '_embedded' => [
                'stores' => $this->collection
            ],
            '_links' => [

                //  Link to current resource
                'self' => [ 
                    'href' => url()->full(),
                    'title' => 'These stores'
                ],

                //  Link to search stores
                'search' => [
                    'href' => url()->current().'?search={searchTerms}',
                    'templated' => true
                ]

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
