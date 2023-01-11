<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class UserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        
       
        if(Auth::check() && Auth::user()->active_status == FALSE) {
            $message = "Your account is deactivated. Please contact with administrator";
            if ($request->ajax() || $request->wantsJson()) {
                $response['message'] = $message;
                return notAuthorizedResponse($message);
            } else {
                Auth::logout();
                return redirect()->route('user.login')->with('error', $message);
            }
                
           
        }

        if(Auth::check() && Auth::user()->delete_status == ACTIVE_STATUS) {
            $message = "Your account is temprary deleted. Please contact with administrator";

            if ($request->ajax() || $request->wantsJson()) {
                $response['message'] = $message;
                return notAuthorizedResponse($message);
            } else {
                Auth::logout();
                return redirect()->route('user.login')->with('error', $message);
            }
        }           
        return $next($request);
    }
}
