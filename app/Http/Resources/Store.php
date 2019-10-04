<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Setting as SettingResource;
use App\Http\Resources\Document as DocumentResource;

class Store extends JsonResource
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

            /*  Basic Info  */
            'id' => $this->id,
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'description' => $this->description,
            'type' => $this->type,
            'industry' => $this->industry,

            /*  Account Info  */
            'email' => $this->email,
            'additional_email' => $this->additional_email,
            'setup' => $this->setup,

            /*  Address Info  */
            'address' => [
                
                'address_1' => $this->address->address_1,
                'address_2' => $this->address->address_2,
                'country' => $this->address->country,
                'province' => $this->address->province,
                'city' => $this->address->city,
                'postal_or_zipcode' => $this->address->postal_or_zipcode,

            ],

            /*  Social Info  */
            'social_links' => [

                'website_link' => $this->website_link,
                'facebook_link' => $this->facebook_link,
                'twitter_link' => $this->twitter_link,
                'linkedin_link' => $this->linkedin_link,
                'instagram_link' => $this->instagram_link,
                'youtube_link' => $this->youtube_link,

            ],
            
            'resource_type' => $this->resource_type,

            /*  Timestamps  */
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
                    'title' => 'This store'
                ],

                
                //  Link to the store logo
                'oq:logo' => [ 
                    'href' => route('store-logo', ['store_id' => $this->id]),
                    'title' => 'This store\'s logo'
                ],

                //  Link to store users
                'oq:users' => [ 
                    'href' => route('store-users', ['store_id' => $this->id]),
                    'title' => 'Users associated with this store as admins, staff members, customers, vendors, e.t.c',
                    'total' => $this->users()->count()
                ],

                //  Link to store admins
                'oq:admins' => [ 
                    'href' => route('store-admins', ['store_id' => $this->id]),
                    'title' => 'Users associated with this store as admins',
                    'total' => $this->admins()->count()
                ],

                //  Link to store staff members
                'oq:staff_members' => [ 
                    'href' => route('store-staff', ['store_id' => $this->id]),
                    'title' => 'Users associated with this store as staff members',
                    'total' => $this->staff()->count()
                ],

                //  Link to store user customers
                'oq:user_customers' => [ 
                    'href' => route('store-user-customers', ['store_id' => $this->id]),
                    'title' => 'Users associated with this store as customers',
                    'total' => $this->userCustomers()->count()
                ],

                //  Link to store user vendors
                'oq:user_vendors' => [ 
                    'href' => route('store-user-vendors', ['store_id' => $this->id]),
                    'title' => 'Users associated with this store as vendors',
                    'total' => $this->userVendors()->count()
                ],
                
                //  Link to store recent activities
                'oq:activities' => [ 
                    'href' => '...', 
                    'title' => 'Recent activities for this store'
                ],

                //  Link to store settings
                'oq:settings' => [ 
                    'href' => route('store-settings', ['store_id' => $this->id]),
                    'title' => 'Settings for this store'
                ]

            ],

            /*  Embedded Resources */
            '_embedded' => [
                
                //  The store logo (Show only if logo exists)
                'logo' => $this->logo ? (new DocumentResource($this->logo)) : null
                
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