<?php
namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;
use Closure;

class AfterMiddleware
{

    public $status;

    public function handle($request, Closure $next)
    {
         $SaveResponse = $next($request);
        $statusCode = $SaveResponse->getStatusCode();
        return tap($next($request), function (JsonResponse $response) use ($statusCode, $SaveResponse) {
            $apiData = $SaveResponse->getData(true);

            $modifiedData = array_merge($apiData, [
                'statusCode' => @$statusCode
            ]);
            $response->setData($modifiedData);
        }); 
        return $next($request);
    }
}