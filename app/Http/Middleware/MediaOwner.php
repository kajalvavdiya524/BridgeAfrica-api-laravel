<?php

namespace App\Http\Middleware;

use App\BusinessMedia;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MediaOwner
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
        $params = $request->route()->parameters();        try{
            $businessMedia = BusinessMedia::findorfail($params['mediaId']);
            if($businessMedia->businessAlbum->business->user_id != auth()->user()->id ||$businessMedia->business_id != $params['businessId']){
                $message = "Unauthorised access";
                return apiResponse($data,$message,$statusCode);
            }
        }catch(ModelNotFoundException $exception){
            $data = [];
            $message = 'Media does not exist.';
            $statusCode = 404;
            return apiResponse($data, $message, $statusCode);
        }
        return $next($request);
    }
}
