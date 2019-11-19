<?php

namespace App\Traits;

use App\Http\Resources\Verification as VerificationResource;
use App\Http\Resources\Verifications as VerificationsResource;

trait VerificationTraits
{
    private $verification;

    /*  convertToApiFormat() method:
     *
     *  Converts to the appropriate Api Response Format
     *
     */
    public function convertToApiFormat($verifications = null)
    {

        try {

            if( $verifications ){

                //  Transform the verifications
                return new VerificationsResource($verifications);

            }else{

                //  Transform the verification
                return new VerificationResource($this);

            }

        } catch (\Exception $e) {

            //  Log the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }
    }

    /*  initiateCreate() method:
     *
     *  This method is used to create a new verification.
     *  The $verificationInfo variable represents the  
     *  verification dataset provided
     */
    public function initiateCreate( $verificationInfo = ['tokenType' => 'six_digit_number'] )
    {
        /*  If the verification method requires a 6 digit number token  */
        if( $verificationInfo['tokenType'] == 'six_digit_number' ){

            /*  Generate a six digits number by picking a random number between 100,000 and 999,999  */
            $token = mt_rand(100000,999999);

        }

        /*
         *  The $template variable represents accepted structure of the
         *  verification data required to create a new resource.
         */
        $template = [
            'token' => $token
        ];

        try {

            /*
             *  Create a new verification, then retrieve a fresh instance
             */
            $this->verification = $this->create($template)->fresh();

            /*  If the verification was created successfully  */
            if( $this->verification ){   

                /*  Return a fresh instance of the verification  */
                return $this->verification->fresh();

            }

        } catch (\Exception $e) {

            //  Return the error
            return oq_api_notify_error('Query Error', $e->getMessage(), 404);

        }

    }

}
