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
            'logo' => $this->logo,
            'name' => $this->name,
            'abbreviation' => $this->abbreviation,
            'description' => $this->description,
            'phone_list' => $this->phone_list, 
            'default_mobile' => $this->default_mobile, 
            'default_email' => $this->default_email, 
            'default_address' => $this->default_address, 
            'type' => $this->type,
            'industry' => $this->industry,
            'currency' => $this->currency,
            'support_ussd' => $this->support_ussd,

            /*  Social Info  */
            'social_links' => [

                'website_link' => $this->website_link,
                'facebook_link' => $this->facebook_link,
                'twitter_link' => $this->twitter_link,
                'linkedin_link' => $this->linkedin_link,
                'instagram_link' => $this->instagram_link,
                'youtube_link' => $this->youtube_link,

            ],
            
            'average_rating' => $this->average_rating,
            'is_approved' => $this->is_approved,
            'last_approved_activity' => $this->last_approved_activity,
            'current_activity_status' => $this->current_activity_status,
            'activity_count' => $this->activity_count,

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