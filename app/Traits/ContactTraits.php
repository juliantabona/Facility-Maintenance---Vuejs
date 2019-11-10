<?php

namespace App\Traits;

use App\Http\Resources\Contact as ContactResource;
use App\Http\Resources\Contacts as ContactsResource;

trait ContactTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($Contacts = null)
    {

        try {

            if( $Contacts ){

                //  Transform the Contacts
                return new ContactsResource($Contacts);

            }else{

                //  Transform the Contact
                return new ContactResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new contact.
     */
    public function initiateCreate( $contactInfo = null )
    {
        /*
         *  The $contact variable represents the contact dataset
         *  provided through the request received.
         */
        $contactInfo = $contactInfo ?? request('contact');

        /*
         *  The $template variable represents structure of the contact.
         *  If no template is provided, we create one using the 
         *  request data.
         */
        $template = $template ?? [
            'name' => $contactInfo['name'] ?? null,
            'is_vendor' => $contactInfo['is_vendor'] ?? null,
            'is_customer' => $contactInfo['is_customer'] ?? null,
            'is_individual' => $contactInfo['is_individual'] ?? null
        ];

        try {

            /*
             *  Create a new contact, then retrieve a fresh instance
             */
            $contact = $this->create($template)->fresh();

            /*  If the contact was created successfully  */
            if( $contact ){

                /*  If we have only one address  */
                if( isset($contactInfo['address']) ){

                    /*  Create a new address  */
                    $contact->createAddress( $contactInfo['address'] );

                /*  If we have many addresses  */
                }elseif( isset($contactInfo['addresses']) ){

                    /*  Foreach address  */
                    foreach( $contactInfo['addresses'] as $address){

                        /*  Create a new address  */
                        $contact->createAddress( $address );

                    }

                }      
                
                /*  If we have only one phone  */
                if( isset($contactInfo['phone']) ){
                    
                    /*  Create a new phone  */
                    $contact->createPhone( $contactInfo['phone'] );

                /*  If we have many phones  */
                }elseif( isset($contactInfo['phones']) ){
                    
                    /*  Foreach phone  */
                    foreach( $contactInfo['phones'] as $phone){

                        /*  Create a new phone  */
                        $contact->createPhone( $phone );

                    }

                }   

                /*  If we have only one email  */
                if( isset($contactInfo['email']) ){

                    /*  Create a new email  */
                    $contact->createEmail( $contactInfo['email'] );

                /*  If we have many emails  */
                }elseif( isset($contactInfo['emails']) ){

                    /*  Foreach email  */
                    foreach( $contactInfo['emails'] as $email){

                        /*  Create a new email  */
                        $contact->createEmail( $email );

                    }

                }   

                /*  Return a fresh instance of the contact  */
                return $contact->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

    /*  createAddress() method:
     *
     *  This method is used to create a new address 
     *  and assign it to the contact.
     */
    public function createAddress($address)
    {
        /*  Create a new address using the initiateCreate() method from the AddressTraits  */
        $newAddress = ( new \App\Address() )->initiateCreate($address);
        
        /*  If the address was created successfully  */
        if( $newAddress ){

            /*  Assign the new address to the contact  */
            $newAddress->update([
                'owner_id' => $this->id, 
                'owner_type' => $this->resource_type,

                /*  Make this the default address if its the first address */
                'default' => ($this->addresses()->count() == 1) ? 1 : 0
            ]);

        }

    }

    /*  createPhone() method:
     *
     *  This method is used to create a new phone 
     *  and assign it to the contact.
     */
    public function createPhone($phone)
    {
        /*  Create a new phone using the initiateCreate() method from the PhoneTraits  */
        $newPhone = ( new \App\Phone() )->initiateCreate($phone);
        
        /*  If the phone was created successfully  */
        if( $newPhone ){

            /*  Assign the new phone to the contact  */
            $newPhone->update([
                'owner_id' => $this->id, 
                'owner_type' => $this->resource_type,

                /*  Make this the default phone if its the first mobile phone */
                'default' => ($this->mobiles()->count() == 1) ? 1 : 0
            ]);

        }
    }

    /*  createEmail() method:
     *
     *  This method is used to create a new email 
     *  and assign it to the contact.
     */
    public function createEmail($email)
    {
        /*  Create a new email using the initiateCreate() method from the EmailTraits  */
        $newEmail = ( new \App\Email() )->initiateCreate($email);
        
        /*  If the email was created successfully  */
        if( $newEmail ){

            /*  Assign the new email to the contact  */
            $newEmail->update([
                'owner_id' => $this->id, 
                'owner_type' => $this->resource_type,

                /*  Make this the default email if its the first email */
                'default' => ($this->emails()->count() == 1) ? 1 : 0
            ]);
            
        }

    }

    public function getMobilePhone()
    {
        /*  Get the contact default mobile number or the first available mobile number  */
        return $this->default_mobile ?? $this->mobiles()->first();
    }

    /*  getBillingInfo() method:
     *
     *  This method is used to get the contacts billing
     *  information from the available contact information
     */
    public function getBillingInfo()
    {
        /*  Return the billing information  */
        return collect($this->toArray())->only([
            'name', 'default_email', 'default_mobile', 'default_address', 'emails', 'phones',  'addresses'
        ]);
    }

    /*  getShippingInfo() method:
     *
     *  This method is used to get the contacts shipping
     *  information from the available contact information
     */
    public function getShippingInfo()
    {
        /*  Return the shipping information from the contact information  */
        return collect($this->toArray())->only([
            'name', 'default_email', 'default_mobile', 'default_address', 'emails', 'phones',  'addresses'
        ]);
    }

}
