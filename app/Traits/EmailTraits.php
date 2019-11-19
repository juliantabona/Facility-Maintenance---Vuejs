<?php

namespace App\Traits;

use App\Http\Resources\Email as EmailResource;
use App\Http\Resources\Emails as EmailsResource;

trait EmailTraits
{
    private $email;

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
     *  The $emailInfo variable represents the  
     *  email dataset provided
     */
    public function initiateCreate( $emailInfo = null )
    {
        /*
         *  The $template variable represents accepted structure of the
         *  email data required to create a new resource.
         */
        $template = [
            'email' => $emailInfo['email'] ?? null
        ];

        try {

            /*
             *  Create a new email, then retrieve a fresh instance
             */
            $this->email = $this->create($template)->fresh();

            /*  If the email was created successfully  */
            if( $this->email ){   

                /*  Return a fresh instance of the email  */
                return $this->email->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }


}
