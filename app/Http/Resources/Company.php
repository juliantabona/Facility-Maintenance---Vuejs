<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Setting as SettingResource;
use App\Http\Resources\Document as DocumentResource;

class Company extends JsonResource
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
            'abbreviation' => $this->abbreviation,
            'description' => $this->description,
            'date_of_incorporation' => $this->date_of_incorporation,
            'type' => $this->type,
            'industry' => $this->industry,
            'address' => [
                
                'address_1' => $this->address_1,
                'address_2' => $this->address_2,
                'country' => $this->country,
                'province' => $this->province,
                'city' => $this->city,
                'postal_or_zipcode' => $this->postal_or_zipcode,

            ],
            'social_links' => [

                'website_link' => $this->website_link,
                'facebook_link' => $this->facebook_link,
                'twitter_link' => $this->twitter_link,
                'linkedin_link' => $this->linkedin_link,
                'instagram_link' => $this->instagram_link,
                'youtube_link' => $this->youtube_link,

            ],
            'currency_type' => $this->currency_type,
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
                    'title' => 'This company'
                ],

                //  Link to the company logo
                'oq:logo' => [ 
                    'href' => route('company-logo', ['company_id' => $this->id]),
                    'title' => 'This company\'s logo'
                ],

                //  Link to company users
                'oq:users' => [ 
                    'href' => route('company-users', ['company_id' => $this->id]),
                    'title' => 'Users associated with this company as admins, staff members, customers, vendors, e.t.c',
                    'total' => $this->users()->count()
                ],

                //  Link to company admins
                'oq:admins' => [ 
                    'href' => route('company-admins', ['company_id' => $this->id]),
                    'title' => 'Users associated with this company as admins',
                    'total' => $this->admins()->count()
                ],

                //  Link to company staff members
                'oq:staff_members' => [ 
                    'href' => route('company-staff', ['company_id' => $this->id]),
                    'title' => 'Users associated with this company as staff members',
                    'total' => $this->staff()->count()
                ],

                //  Link to company user customers
                'oq:user_customers' => [ 
                    'href' => route('company-user-customers', ['company_id' => $this->id]),
                    'title' => 'Users associated with this company as customers',
                    'total' => $this->userCustomers()->count()
                ],

                //  Link to company user vendors
                'oq:user_vendors' => [ 
                    'href' => route('company-user-vendors', ['company_id' => $this->id]),
                    'title' => 'Users associated with this company as vendors',
                    'total' => $this->userVendors()->count()
                ],

                //  Link to company recent activities
                'oq:activities' => [ 
                    'href' => '...', 
                    'title' => 'Recent activities for this company'
                ],

                //  Link to company settings
                'oq:settings' => [ 
                    'href' => route('company-settings', ['company_id' => $this->id]),
                    'title' => 'Settings for this company'
                ],

                //  Link to company stores
                'oq:stores' => [ 
                    'href' => route('company-stores', ['company_id' => $this->id]),
                    'title' => 'Stores for this company'
                ]

            ],

            /*  Embedded Resources */
            '_embedded' => [

                //  The company logo
                'logo' => $this->when( !empty($this->logo),  
                    (new DocumentResource($this->logo))
                ),

                //  The company settings
                'settings' => new SettingResource($this->whenLoaded('settings')),
                
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
