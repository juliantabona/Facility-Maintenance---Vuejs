<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Document as DocumentResource;
use App\Http\Resources\Documents as DocumentsResource;

class Product extends JsonResource
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
            'cost_per_item' => $this->cost_per_item,
            'unit_price' => $this->unit_price,
            'unit_sale_price' => $this->unit_sale_price,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'stock_quantity' => $this->stock_quantity,
            'has_inventory' => $this->has_inventory,
            'auto_track_inventory' => $this->auto_track_inventory,
            'variants' => $this->variants,
            'variant_attributes' => $this->variant_attributes,
            'allow_variants' => $this->allow_variants,
            'allow_downloads' => $this->allow_downloads,
            'show_on_store' => $this->show_on_store,
            'is_new' => $this->is_new,
            'is_featured' => $this->is_featured,
            
            //  Attributes
            'average_rating' => $this->average_rating,
            'store_currency_symbol' => $this->store_currency_symbol,

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
                    'title' => 'This product'
                ]

            ],

            /*  Embedded Resources */
            '_embedded' => [
    
                //  The product primary picture
                'primary_picture' => $this->primary_image ? (new DocumentsResource($this->primary_image)) : null,
                
                //  The product gallery pictures
                'gallery_pictures' => count($this->gallery) ? (new DocumentsResource($this->gallery)) : null,

                //  The product downloads
                'downloads' => count($this->downloads) ? (new DocumentsResource($this->downloads)) : null
                
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