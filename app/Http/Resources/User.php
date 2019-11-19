<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Emails as EmailResources;
use App\Http\Resources\Phones as PhonesResource;
use App\Http\Resources\Setting as SettingResource;
use App\Http\Resources\Document as DocumentResource;
use App\Http\Resources\Addresses as AddressResources;



class user extends JsonResource
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
            
            /*  Identifier  */
            'id' => $this->id,
            
            /*  Basic Info  */
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'bio' => $this->bio,

            /*  Basic Info  */
            'setup' => $this->setup,
            'account_type' => $this->account_type,
            
            /*  Social Info  */
            'social_links' => [
                'facebook_link' => $this->facebook_link,
                'twitter_link' => $this->twitter_link,
                'linkedin_link' => $this->linkedin_link,
                'instagram_link' => $this->instagram_link,
                'youtube_link' => $this->youtube_link,
            ],

            /*  Attributes  */
            'full_name' => $this->full_name,
            'phone_list' => $this->phone_list, 
            'default_email' => $this->default_email, 
            'default_mobile' => $this->default_mobile, 
            'default_address' => $this->default_address, 
            'is_verified' => $this->is_verified,
            'is_email_verified' => $this->is_email_verified,
            'is_mobile_verified' => $this->is_mobile_verified,
            'resource_type' => $this->resource_type,

            /*  Timestamp Info  */
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /*  Resource Links */
            '_links' => [
                'curies' => [
                    ['name' => 'oq', 'href' => 'https://oqcloud.co.bw/docs/rels/{rel}', 'templated' => true],
                ],

                //  Link to current resource
                'self' => [
                    'href' => url()->full(),
                    'title' => 'This user',
                ],

                //  Link to the users profile picture
                'oq:profile_image' => [
                    'href' => ($this->id == auth('api')->user()->id)
                                ? route('my-picture')
                                    : route('user-picture', ['user_id' => $this->id]),
                    'title' => 'The user\'s profile picture',
                ],

                /*
                //  Link to the users emails
                'oq:emails' => [
                    'href' => ($this->id == auth('api')->user()->id)
                                ? route('my-emails')
                                    : route('user-emails', ['user_id' => $this->id]),
                    'title' => 'The user\'s emails',
                    'total' => $this->emails()->count(),
                ],

                //  Link to the users mobile phones
                'oq:mobiles' => [
                    'href' => ($this->id == auth('api')->user()->id)
                                ? route('my-mobiles')
                                    : route('user-mobiles', ['user_id' => $this->id]),
                    'title' => 'The user\'s mobiles',
                    'total' => $this->mobiles()->count(),
                ],

                //  Link to the users phones
                'oq:phones' => [
                    'href' => ($this->id == auth('api')->user()->id)
                                ? route('my-phones')
                                    : route('user-phones', ['user_id' => $this->id]),
                    'title' => 'The user\'s phones',
                    'total' => $this->phones()->count(),
                ],

                //  Link to the users emails
                'oq:addresses' => [
                    'href' => ($this->id == auth('api')->user()->id)
                                ? route('my-addresses')
                                    : route('user-addresses', ['user_id' => $this->id]),
                    'title' => 'The user\'s addresses',
                    'total' => $this->addresses()->count(),
                ],
                */

                //  Link to the users accounts
                'oq:accounts' => [
                    'href' => ($this->id == auth('api')->user()->id)
                                ? route('my-accounts')
                                    : route('user-accounts', ['user_id' => $this->id]),
                    'title' => 'Accounts that this user has created or been added to as admin, staff, e.t.c',
                    'total' => $this->accounts()->count(),
                ],

                //  Link to the users accounts where their role is an admin
                'oq:accounts_where_user_is_admin' => [
                    'href' => route('user-accounts', ['user_id' => $this->id, 'userTypes' => 'admin']),
                    'title' => 'Accounts that this user has created or been added to as admin',
                    'total' => $this->accountsWhereUserIsAdmin()->count(),
                ],

                //  Link to the users accounts where their role is a staff member
                'oq:accounts_where_user_is_staff' => [
                    'href' => route('user-accounts', ['user_id' => $this->id, 'userTypes' => 'staff']),
                    'title' => 'Accounts that this user has been added to as a staff member',
                    'total' => $this->accountsWhereUserIsStaff()->count(),
                ],

                //  Link to the users accounts where their role is an customer
                'oq:accounts_where_user_is_customer' => [
                    'href' => route('user-accounts', ['user_id' => $this->id, 'userTypes' => 'customer']),
                    'title' => 'Accounts that this user has been added to as a customer',
                    'total' => $this->accountsWhereUserIsCustomer()->count(),
                ],

                //  Link to the users accounts where their role is an vendor
                'oq:accounts_where_user_is_vendor' => [
                    'href' => route('user-accounts', ['user_id' => $this->id, 'userTypes' => 'vendor']),
                    'title' => 'Accounts that this user has been added to as a vendor(supplier)',
                    'total' => $this->accountsWhereUserIsVendor()->count(),
                ],

                //  Link to the users accounts
                'oq:stores' => [
                    'href' => ($this->id == auth('api')->user()->id)
                                ? route('my-stores')
                                    : route('user-stores', ['user_id' => $this->id]),
                    'title' => 'Stores that this user has created or been added to as admin, staff, e.t.c',
                    'total' => $this->stores()->count(),
                ],
                
                //  Link to the users recent activities
                'oq:activities' => [
                    'href' => '/users/'.$this->id.'/activities',
                    'title' => 'This user\'s recent activities',
                ],

                //  Link to the users settings
                'oq:settings' => [
                    'href' => ($this->id == auth('api')->user()->id)
                                ? route('my-settings')
                                    : route('user-settings', ['user_id' => $this->id]),
                    'title' => 'Account settings for this user',
                ],
            ],

            /*  Embedded Resources */
            '_embedded' => [

                //  The users profile picture
                'profile_image' => $this->when(!empty($this->profile_image),
                    (new DocumentResource($this->profile_image))
                ),

                //  The users settings
                'settings' => new SettingResource($this->whenLoaded('settings')),
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
