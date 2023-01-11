<?php

namespace App\Http\Middleware;

use Closure;

class CheckAppVersion
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
        $app_version = $request->header('appversion');
        $devicetype = $request->header('devicetype');
        
        if(!$app_version) {
            $response['message'] = 'Please provide app-version in header parameters.';
            return response()->json($response, BAD_FORMAT);
        }

        if($devicetype == ANDROID && $app_version < CURRENT_ANDROID_APP_VERSION) {
            $response['message'] = 'Please update your app. You are using old app.';
            return response()->json($response, BAD_FORMAT);
        }

        if($devicetype == CURRENT_IOS_APP_VERSION && $app_version < IOS) {
            $response['message'] = 'Please update your app. You are using old app.';
            return response()->json($response, BAD_FORMAT);
        }
                    
        return $next($request);
    }
}
