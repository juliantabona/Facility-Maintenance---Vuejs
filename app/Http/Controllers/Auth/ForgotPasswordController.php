<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function customSendForgotPasswordEmail()
    {
        //  User Instance
        $data = ( new User() )->initiateSendForgotPasswordEmail();
        $success = $data['success'];
        $response = $data['response'];

        //  If the email was sent successfully
        if ($success) {
            //  If this is a success then we have sucess data returned
            $user = $response;

            //  Action was executed successfully
            return oq_api_notify($user, 200);
        }

        //  If the data was not a success then return the response
        return $response;
    }

    public function customResetPassword()
    {
       //  User Instance
       $data = ( new User() )->initiateResetPassword();
       $success = $data['success'];
       $response = $data['response'];

       //  If the password was reset successfully
       if ($success) {
           //  If this is a success then we have the user data returned
           $user = $response;

           //  Action was executed successfully
           return oq_api_notify(null, 201);
       }

       //  If the data was not a success then return the response
       return $response;

    }

}
