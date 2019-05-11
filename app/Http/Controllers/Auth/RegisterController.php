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
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        //  If the user was created successfully
        if ($user) {

            //  Create and send an account activation token and email
            $user->initiateSendAccountActivationMail();

            /*  Validate and Create the new company and associated branch and upload related documents
            *  [e.g logo, company profile, other documents]. Update recent activities
            *
            *  @param $request - The request with all the parameters to create
            *  @param $company - The company model if we are updating, in this case it must be null
            *  @param $user - The user creating/updating the company
            *
            *  @return SQL Error - If SQL Execution failed
            *  @return Company - If successful
            */

    //        $response = oq_createOrUpdateCompany($data, null, $user);

            //  If validation passed but we had issues while trying to create the company
            //  E.g SQL related issues.
    //        if (oq_failed_sql($response)) {
                //  Return failed sql error with an alert or json response if API request
    //            return oq_failed_sql_return($request, $response);
    //        }

            //  At this point we are certain we have a company
    //        $company = $response;
        }

        $user = User::where('id', $user->id)->with([
            'companyBranch' => function ($query) {
                $query->with('company');
            }, ])->first();

        //  Return new user
        return $user;
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
