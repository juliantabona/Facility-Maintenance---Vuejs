<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/activate-account';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        //  Get the rules for validating a user on creation
        $user_rules = oq_user_create_v_rules();

        //  Get the rules for validating a company on creation
        $company_rules = oq_company_create_v_rules();

        //  Customized error messages for validating a user on creation
        $user_messages = oq_company_create_v_msgs();

        //  Customized error messages for validating a company on creation
        $company_messages = oq_company_create_v_msgs();

        $rules = array_merge($user_rules, $company_rules);
        $messages = array_merge($user_messages, $company_messages);

        // Now pass the input and rules into the validator
        $validator = Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return \App\User
     */
    protected function create(array $data)
    {
        //  Register the user
        return ( new User )->initiateRegistration();
    }


    /**
     *  After the user is successfully registered,
     *  we hook into the registered() function from "RegistersUsers"
     *  and create the token used during authorization for API calls.
     */
    protected function registered(Request $request, $user)
    {
        //  If this is an API Response
        if (oq_viaAPI($request)) {
            //  Return API Response
            return oq_api_notify( $user, 201 );
        } else {
            return $user;
        }
    }
    
}
