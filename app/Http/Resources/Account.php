<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Setting as SettingResource;
use App\Http\Resources\Document as DocumentResource;

class Account extends JsonResource
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
                    'title' => 'This account'
                ],

                //  Link to the account logo
                'oq:logo' => [ 
                    'href' => route('account-logo', ['account_id' => $this->id]),
                    'title' => 'This account\'s logo'
                ],

                //  Link to account users
                'oq:users' => [ 
                    'href' => route('account-users', ['account_id' => $this->id]),
                    'title' => 'Users associated with this account as admins, staff members, customers, vendors, e.t.c',
                    'total' => $this->users()->count()
                ],

                //  Link to account admins
                'oq:admins' => [ 
                    'href' => route('account-admins', ['account_id' => $this->id]),
                    'title' => 'Users associated with this account as admins',
                    'total' => $this->admins()->count()
                ],

                //  Link to account staff members
                'oq:staff_members' => [ 
                    'href' => route('account-staff', ['account_id' => $this->id]),
                    'title' => 'Users associated with this account as staff members',
                    'total' => $this->staff()->count()
                ],

                //  Link to account user customers
                'oq:user_customers' => [ 
                    'href' => route('account-user-customers', ['account_id' => $this->id]),
                    'title' => 'Users associated with this account as customers',
                    'total' => $this->userCustomers()->count()
                ],

                //  Link to account user vendors
                'oq:user_vendors' => [ 
                    'href' => route('account-user-vendors', ['account_id' => $this->id]),
                    'title' => 'Users associated with this account as vendors',
                    'total' => $this->userVendors()->count()
                ],

                //  Link to account recent activities
                'oq:activities' => [ 
                    'href' => '...', 
                    'title' => 'Recent activities for this account'
                ],

                //  Link to account settings
                'oq:settings' => [ 
                    'href' => route('account-settings', ['account_id' => $this->id]),
                    'title' => 'Settings for this account'
                ],

                //  Link to account stores
                'oq:stores' => [ 
                    'href' => route('account-stores', ['account_id' => $this->id]),
                    'title' => 'Stores for this account'
                ]

            ],

            /*  Embedded Resources */
            '_embedded' => [

                //  The account logo
                'logo' => $this->when( !empty($this->logo),  
                    (new DocumentResource($this->logo))
                ),

                //  The account settings
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
