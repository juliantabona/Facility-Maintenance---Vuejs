<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Documents as DocumentsResource;

class Product extends JsonResource
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
            'description' => $this->description,
            'type' => $this->type,
            'cost_per_item' => $this->cost_per_item,
            'unit_regular_price' => $this->unit_regular_price,
            'unit_sale_price' => $this->unit_sale_price,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'stock_quantity' => $this->stock_quantity,

            'allow_stock_management' => $this->allow_stock_management,
            'auto_manage_stock' => $this->auto_manage_stock,
            'variant_attributes' => $this->variant_attributes,
            'allow_variants' => $this->allow_variants,
            'allow_downloads' => $this->allow_downloads,
            'show_on_store' => $this->show_on_store,
            'is_new' => $this->is_new,
            'is_featured' => $this->is_featured,

            //  Relationships
            'variables' => $this->variables,

            //  Attributes
            'discount_total' => $this->discount_total,
            'tax_total' => $this->tax_total,
            'sub_total' => $this->sub_total,
            'grand_total' => $this->grand_total,
            'on_sale' => $this->on_sale,
            'has_price' => $this->has_price,
            'has_prices_on_all_variations' => $this->has_prices_on_all_variations,
            'stock_status' => $this->stock_status,
            'has_enough_stock_on_all_variations' => $this->has_enough_stock_on_all_variations,
            'currency' => $this->currency,
            'rating_count' => $this->rating_count,
            'average_rating' => $this->average_rating,
            'parent_variant_attributes' => $this->parent_variant_attributes,
            'resource_type' => $this->resource_type,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /*  Resource Links */
            '_links' => [
                'curies' => [
                    ['name' => 'oq', 'href' => 'https://oqcloud.co.bw/docs/rels/{rel}', 'templated' => true],
                ],

                //  Link to current resource
                'self' => [
                    'href' => route('product', ['product_id' => $this->id]),
                    'title' => 'This product',
                ],

                //  Link to product variations
                'oq:variations' => [
                    'href' => route('product-variations', ['product_id' => $this->id]),
                    'title' => 'The product variations',
                    'total' => $this->variations()->count(),
                ],

                //  Link to product variations
                'oq:variables' => [
                    'href' => route('product-variables', ['product_id' => $this->id]),
                    'title' => 'The product variables',
                    'total' => $this->variables()->count(),
                ],
            ],

            /*  Embedded Resources */
            '_embedded' => [
                //  The product primary picture
                'primary_picture' => $this->primary_image ? (new DocumentsResource($this->primary_image)) : null,

                //  The product gallery pictures
                'gallery_pictures' => count($this->gallery) ? (new DocumentsResource($this->gallery)) : null,

                //  The product downloads
                'downloads' => count($this->downloads) ? (new DocumentsResource($this->downloads)) : null,
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
