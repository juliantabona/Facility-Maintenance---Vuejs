<?php

namespace App\Http\Controllers\Auth;

use DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        logout as performLogout;
    }

    public function logout(Request $request)
    {
        //  API Request
        if (oq_viaAPI($request)) {
            $accessToken = auth()->user()->token();
            $refreshToken = DB::table('oauth_refresh_tokens')
                                ->where('access_token_id', $accessToken->id)
                                ->update([
                                    'revoked' => true,
                                ]);
            $accessToken->revoke();

            return response()->json(null, 204);

        //  Non API Request
        } else {
            $this->performLogout($request);

            return redirect()->route('login');
        }
    }

    //  Allow for login using email and username
    public function username()
    {
        $identity = request()->input('identity');
        $field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$field => $identity]);

        return $field;
    }

    /**
     *  After the user is successfully logged In,
     *  we hook into the authenticated() function from "AuthenticatesUsers"
     *  and create the token used during authorization for API calls.
     */
    protected function authenticated(Request $request, $user)
    {
        //  Check if the user activated their account
        if (!$user->verified) {
            //  If this is an API Request
            if (oq_viaAPI($request)) {
                return oq_api_notify([
                    'message' => 'Activate account',
                    'user' => $user,
                ], 403);
            } else {
                //  Notify the user to activate their account
                oq_notify('Activate account', 'warning');

                //  Go to login page
                return redirect('login');
            }
        } else {
            if (oq_viaAPI($request)) {
                return $user->generateToken($request);
            } else {
                return redirect()->intended($this->redirectPath());
            }
        }
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/overview';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->guard()->validate($this->credentials($request))) {
            $user = $this->guard()->getLastAttempted();

            // Attempt to login
            if ($this->attemptLogin($request)) {
                // Send the normal successful login response
                return $this->sendLoginResponse($request);
            } else {
                // Increment the failed login attempts and redirect back to the
                // login form with an error message.
                $this->incrementLoginAttempts($request);

                return redirect()
                    ->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors(['active' => 'You must be active to login.']);
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the failed login response instance.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            //  $this->username() => [trans('auth.failed')],

            //  Just change this part
            'identity' => [trans('auth.failed')],
        ]);
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            //  $this->username() => [Lang::get('auth.throttle', ['seconds' => $seconds])],

            //  Just change this part
            'identity' => [Lang::get('auth.throttle', ['seconds' => $seconds])],
        ])->status(429);
    }
}
