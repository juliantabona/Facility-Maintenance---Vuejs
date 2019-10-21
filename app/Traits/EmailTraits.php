<?php

namespace App\Traits;

use App\Http\Resources\Email as EmailResource;
use App\Http\Resources\Emails as EmailsResource;

trait EmailTraits
{

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($Emails = null)
    {

        try {

            if( $Emails ){

                //  Transform the Emails
                return new EmailsResource($Emails);

            }else{

                //  Transform the Email
                return new EmailResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new email.
     */
    public function initiateCreate( $template = null )
    {
        /*
         *  The $email variable represents the email dataset
         *  provided through the request received.
         */
        $email = request('email');

        /*
         *  The $template variable represents structure of the email.
         *  If no template is provided, we create one using the 
         *  request data.
         */
        $template = $template ?? [
            'email' => $email['email'] ?? null
        ];

        try {

            /*
             *  Create a new email, then retrieve a fresh instance
             */
            $email = $this->create($template)->fresh();

            /*  If the email was created successfully  */
            if( $email ){   

                /*  Return a fresh instance of the email  */
                return $email->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }


}
