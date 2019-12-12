<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Document as DocumentResource;

class Store extends JsonResource
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

            /*  Attributes  */
            'is_verified' => $this->is_verified,
            'is_email_verified' => $this->is_email_verified,
            'is_mobile_verified' => $this->is_mobile_verified,
            'customer_access_code' => $this->customer_access_code,
            'team_access_code' => $this->team_access_code,
            'resource_type' => $this->resource_type,
            'statistics' => $this->statistics,
            

            /*  Timestamps  */
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            /*  Resource Links */
            '_links' => [
                'curies' => [
                    ['name' => 'oq', 'href' => 'https://oqcloud.co.bw/docs/rels/{rel}', 'templated' => true],
                ],

                //  Link to current resource
                'self' => [
                    'href' => route('store', ['store_id' => $this->id]),
                    'title' => 'This store',
                ],

                //  Link to store owning account
                'oq:account' => [
                    'href' => route('store-account', ['store_id' => $this->id]),
                    'title' => 'The store account (Owning Account)',
                    'total' => $this->orders()->count(),
                ],
                
                //  Link to the store logo
                'oq:logo' => [
                    'href' => route('store-logo', ['store_id' => $this->id]),
                    'title' => 'This store\'s logo',
                ],

                //  Link to store documents
                'oq:documents' => [
                    'href' => route('store-documents', ['store_id' => $this->id]),
                    'title' => 'The store documents (file/image/video)',
                    'total' => $this->documents()->count(),
                ],

                //  Link to store phones
                'oq:phones' => [
                    'href' => route('store-phones', ['store_id' => $this->id]),
                    'title' => 'The store telephones, mobile phones and fax phones',
                    'total' => $this->phones()->count(),
                ],

                //  Link to store default mobile phone
                'oq:default_mobile' => [
                    'href' => route('store-default-mobile', ['store_id' => $this->id]),
                    'title' => 'The store default mobile phone'
                ],

                //  Link to store mobiles
                'oq:mobiles' => [
                    'href' => route('store-mobiles', ['store_id' => $this->id]),
                    'title' => 'The store mobile phones',
                    'total' => $this->mobiles()->count(),
                ],

                //  Link to store telephones
                'oq:telephones' => [
                    'href' => route('store-telephones', ['store_id' => $this->id]),
                    'title' => 'The store telephones',
                    'total' => $this->telephones()->count(),
                ],

                //  Link to store fax phones
                'oq:fax' => [
                    'href' => route('store-fax', ['store_id' => $this->id]),
                    'title' => 'The store fax phones',
                    'total' => $this->fax()->count(),
                ],

                //  Link to store addresses
                'oq:addresses' => [
                    'href' => route('store-addresses', ['store_id' => $this->id]),
                    'title' => 'The store addresses',
                    'total' => $this->addresses()->count(),
                ],
                
                //  Link to store emails
                'oq:emails' => [
                    'href' => route('store-emails', ['store_id' => $this->id]),
                    'title' => 'The store emails',
                    'total' => $this->emails()->count(),
                ],

                //  Link to store contacts
                'oq:contacts' => [
                    'href' => route('store-contacts', ['store_id' => $this->id]),
                    'title' => 'The store contacts',
                    'total' => $this->contacts()->count(),
                ],

                //  Link to store contacts with atleast one mobile phone
                'oq:contacts_with_mobile_phone' => [
                    'href' => route('store-contacts-with-mobiles', ['store_id' => $this->id]),
                    'title' => 'The store contacts with atleast one mobile phone',
                    'total' => $this->contactsWithMobilePhone()->count(),
                ],

                //  Link to store customer contacts
                'oq:customer_contacts' => [
                    'href' => route('store-customer-contacts', ['store_id' => $this->id]),
                    'title' => 'The store contacts classified as customers',
                    'total' => $this->customerContacts()->count(),
                ],

                //  Link to store customer contacts with atleast one mobile phone
                'oq:customer_contacts_with_mobile_phone' => [
                    'href' => route('store-customer-contacts-with-mobiles', ['store_id' => $this->id]),
                    'title' => 'The store contacts classified as customers with atleast one mobile phone',
                    'total' => $this->customerContactsWithMobilePhone()->count(),
                ],
                
                //  Link to store vendor contacts
                'oq:vendor_contacts' => [
                    'href' => route('store-vendor-contacts', ['store_id' => $this->id]),
                    'title' => 'The store contacts classified as vendors',
                    'total' => $this->vendorContacts()->count(),
                ],

                //  Link to store vendor contacts with atleast one mobile phone
                'oq:vendor_contacts_with_mobile_phone' => [
                    'href' => route('store-vendor-contacts-with-mobiles', ['store_id' => $this->id]),
                    'title' => 'The store contacts classified as vendors with atleast one mobile phone',
                    'total' => $this->vendorContactsWithMobilePhone()->count(),
                ],

                //  Link to store users
                'oq:users' => [
                    'href' => route('store-users', ['store_id' => $this->id]),
                    'title' => 'The store users classified as admins, staff members, e.t.c',
                    'total' => $this->users()->count(),
                ],

                //  Link to store admins
                'oq:admins' => [
                    'href' => route('store-admins', ['store_id' => $this->id]),
                    'title' => 'The store users classified as admins only',
                    'total' => $this->admins()->count(),
                ],

                //  Link to store staff members
                'oq:staff_members' => [
                    'href' => route('store-staff', ['store_id' => $this->id]),
                    'title' => 'The store users classified as staff members only',
                    'total' => $this->staff()->count(),
                ],

                //  Link to store USSD Interface
                'oq:ussd_interface' => [
                    'href' => route('store-ussd-interface', ['store_id' => $this->id]),
                    'title' => 'The store USSD Interface',
                ],

                //  Link to store taxes
                'oq:taxes' => [
                    'href' => route('store-taxes', ['store_id' => $this->id]),
                    'title' => 'The store taxes',
                    'total' => $this->taxes()->count(),
                ],

                //  Link to store discounts
                'oq:discounts' => [
                    'href' => route('store-discounts', ['store_id' => $this->id]),
                    'title' => 'The store discounts',
                    'total' => $this->discounts()->count(),
                ],

                //  Link to store discounts
                'oq:coupons' => [
                    'href' => route('store-coupons', ['store_id' => $this->id]),
                    'title' => 'The store coupons',
                    'total' => $this->coupons()->count(),
                ],

                //  Link to store products
                'oq:products' => [
                    'href' => route('store-products', ['store_id' => $this->id]),
                    'title' => 'The store products',
                    'total' => $this->products()->count(),
                ],

                //  Link to store orders
                'oq:orders' => [
                    'href' => route('store-orders', ['store_id' => $this->id]),
                    'title' => 'The store orders',
                    'total' => $this->orders()->count(),
                ],

                //  Link to store orders
                'oq:messages' => [
                    'href' => route('store-messages', ['store_id' => $this->id]),
                    'title' => 'The store messages',
                    'total' => $this->messages()->count(),
                ],

                //  Link to store reviews
                'oq:reviews' => [
                    'href' => route('store-reviews', ['store_id' => $this->id]),
                    'title' => 'The store reviews',
                    'total' => $this->reviews()->count(),
                ],

                

                //  Link to store recent activities
                'oq:activities' => [
                    'href' => '...',
                    'title' => 'Recent activities for this store',
                ],

                //  Link to store settings
                'oq:settings' => [
                    'href' => route('store-settings', ['store_id' => $this->id]),
                    'title' => 'Settings for this store',
                ],

                //  Link to store statistics
                'oq:statistics' => [
                    'href' => route('store-statistics', ['store_id' => $this->id]),
                    'title' => 'Statistics for this store',
                ],

            ],

            /*  Embedded Resources */
            '_embedded' => [
                //  The store logo (Show only if logo exists)
                'logo' => $this->logo ? (new DocumentResource($this->logo)) : null,
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
