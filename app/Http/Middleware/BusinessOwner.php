<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Business;

class BusinessOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $data = [];
        $message = " ";
        $statusCode = 403;
        $businessId = $request->route('businessId');
        try{
            $business = Business::findorfail($businessId);
            if($business->user_id != auth()->user()->id){
                $message = "Unauthorised access";
                return apiResponse($data,$message,$statusCode);
            }
        }catch(ModelNotFoundException $exception){
            $data = [];
            $message = 'Business does not exist.';
            $statusCode = 404;
            return apiResponse($data, $message, $statusCode);
        }
        return $next($request);
    }
}
