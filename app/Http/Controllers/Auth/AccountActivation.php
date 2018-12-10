<?php

namespace App\Http\Controllers\Auth;

use Mail;
use App\User;
use App\VerifyUser;
use Illuminate\Http\Request;
use App\Mail\ActivateAccount;
use App\Http\Controllers\Controller;

class AccountActivation extends Controller
{
    public function resend(Request $request)
    {
        //  Check if the account id was provided
        if (empty(request('user_id'))) {
            //  API Response
            if (oq_viaAPI($request)) {
                return oq_api_notify([
                    'message' => 'Include the account id',
                ], 422);
            } else {
                //  Notify the user that the account id does not exist
                oq_notify('Include the account id');

                //  Go to login page
                return redirect()->route('login');
            }
        }

        //  Get the associated user
        $user = User::find(request('user_id'));

        //  If the user does not exist
        if (!$user) {
            //  No resource found
            return oq_api_notify_no_resource();
        }

        //  Check if the user is already verified
        if ($user->verified) {
            return oq_api_notify_error('Account already verified', null, 200);
        }

        //  Check if the user has a token
        if (!$user->verification()->count()) {
            //  Create a new account verification token
            $verification = VerifyUser::create([
                'user_id' => $user->id,
                'token' => sha1(time()),
            ]);
        }

        if ($user->email) {
            //  Send email to the user to activate account

            //  Mail::to($user->email)->send(new ActivateAccount($user));

            //  Notify the user that email was sent successfully
            if (oq_viaAPI($request)) {
                return oq_api_notify([
                    'message' => 'Account activation email sent successfully to "'.$user->email.'"!',
                    'email' => $user->email,
                ], 200);
            }
        }

        return oq_api_notify_error('Failed to resend verification token', null, 400);
    }

    public function activate(Request $request)
    {
        //  Check if the mail token exists
        if (empty(request('token'))) {
            //  API Response
            if (oq_viaAPI($request)) {
                return oq_api_notify([
                    'message' => 'Activation token not provided',
                ], 422);
            } else {
                //  Notify the user that the token does not exist
                oq_notify('Activation token not provided');

                //  Go to login page
                return redirect()->route('login');
            }
        }

        //  Get the mail token
        $mailToken = request('token');

        $token = VerifyUser::where('token', $mailToken)->first();

        //  Check if the token exists
        if (count($token)) {
            //  Check if user exists
            $userExists = count($token->user);

            if (!$userExists) {
                //  API Response
                if (oq_viaAPI($request)) {
                    return oq_api_notify([
                        'message' => 'Token does not match any account. Request a new token.',
                    ], 422);
                } else {
                    //  Notify the user that the account associated to the token does not exist
                    oq_notify('Token does not match any account. Request a new token');

                    //  Go to login page
                    return redirect()->route('login');
                }
            }

            //  Get the associated user
            $user = $token->user;

            //  If the user hasn't been verified
            if (!$user->verified) {
                //  Verify the user account
                $user->verified = 1;
                $user->save();

                //  API Response
                if (oq_viaAPI($request)) {
                    return oq_api_notify([
                        'message' => 'Account verified. You can now login',
                        'user' => $user,
                    ], 200);
                } else {
                    //  Notify the user that their account is verified
                    oq_notify('Account verified. You can now login', 'success');

                    //  Go to login page
                    return redirect()->route('login');
                }
            } else {
                //  API Response
                if (oq_viaAPI($request)) {
                    return oq_api_notify([
                        'message' => 'Account already verified. You can login.',
                        'user' => $user,
                    ], 200);
                } else {
                    //  Notify the user that their account is already verified
                    oq_notify('Account already verified. You can login.', 'success');

                    //  Go to login page
                    return redirect()->route('login');
                }
            }
        } else {
            //  API Response
            if (oq_viaAPI($request)) {
                return oq_api_notify([
                    'message' => 'Invalid Token',
                ], 422);
            } else {
                //  Notify the user that their account is already verified
                oq_notify('Invalid Token', 'warning');
                //  Go to login page
                return redirect()->route('login');
            }
        }
    }
}
