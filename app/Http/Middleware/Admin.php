<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        if (Auth::check()) {
            if (Auth::user()->role_id != ADMIN_USER_TYPE) {
                return redirect('home')->with('error', 'You are not allow to perform this action.');
            }
        } else {
            return  redirect()->route('admin.login');           
        }
        return $next($request);
    }
}
