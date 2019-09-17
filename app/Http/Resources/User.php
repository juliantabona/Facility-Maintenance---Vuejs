<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Setting as SettingResource;
use App\Http\Resources\Document as DocumentResource;

class user extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
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
                    'title' => 'This user'
                ],

                //  Link to the users profile picture
                'oq:profile_image' => [ 
                    'href' => ($this->id == auth('api')->user()->id)  
                                ? route('my-picture') 
                                    : route('user-picture', ['user_id' => $this->id]),
                    'title' => 'The user\'s profile picture'
                ],

                //  Link to the users companies
                'oq:companies' => [ 
                    'href' => ($this->id == auth('api')->user()->id)  
                                ? route('my-companies') 
                                    : route('user-companies', ['user_id' => $this->id]),
                    'title' => 'Companies that this user has created or been added to as admin, staff, client, vendor, e.t.c',
                    'total' => $this->companies()->count()
                ],

                //  Link to the users companies where their role is an admin
                'oq:companies_where_user_is_admin' => [ 
                    'href' => route('user-companies', ['user_id' => $this->id, 'userTypes' => 'admin']),
                    'title' => 'Companies that this user has created or been added to as admin',
                    'total' => $this->companiesWhereUserIsAdmin()->count()
                ],

                //  Link to the users companies where their role is a staff member
                'oq:companies_where_user_is_staff' => [ 
                    'href' => route('user-companies', ['user_id' => $this->id, 'userTypes' => 'staff']),
                    'title' => 'Companies that this user has been added to as a staff member',
                    'total' => $this->companiesWhereUserIsStaff()->count()
                ],

                //  Link to the users companies where their role is an client
                'oq:companies_where_user_is_client' => [ 
                    'href' => route('user-companies', ['user_id' => $this->id, 'userTypes' => 'client']),
                    'title' => 'Companies that this user has been added to as a client',
                    'total' => $this->companiesWhereUserIsClient()->count()
                ],

                //  Link to the users companies where their role is an vendor
                'oq:companies_where_user_is_vendor' => [ 
                    'href' => route('user-companies', ['user_id' => $this->id, 'userTypes' => 'vendor']),
                    'title' => 'Companies that this user has been added to as a vendor(supplier)',
                    'total' => $this->companiesWhereUserIsVendor()->count()
                ],

                //  Link to the users recent activities
                'oq:activities' => [ 
                    'href' => '/users/'.$this->id.'/activities', 
                    'title' => 'This user\'s recent activities'
                ],

                //  Link to the users settings
                'oq:settings' => [ 
                    'href' => ($this->id == auth('api')->user()->id)  
                                ? route('my-settings') 
                                    : route('user-settings', ['user_id' => $this->id]),
                    'title' => 'Account settings for this user'
                ],

            ],

            /*  Embedded Resources */
            '_embedded' => [

                //  The users profile picture
                'profile_image' => $this->when( !empty($this->profile_image),  
                    (new DocumentResource($this->profile_image))
                ),

                //  The users settings
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
