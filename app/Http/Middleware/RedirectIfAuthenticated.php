<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        //  If this is not an api call
        if (!$request->expectsJson()) {
            //  Then check if the user session still exists
            if (Auth::guard($guard)->check()) {
                //  Otherwise redirect home
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
