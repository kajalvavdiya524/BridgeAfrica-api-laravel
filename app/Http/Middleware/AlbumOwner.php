<?php

namespace App\Http\Middleware;

use App\BusinessAlbum;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AlbumOwner
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
        $params = $request->route()->parameters();
        try{
            $businessAlbum = BusinessAlbum::findorfail($params['albumId']);
            if($businessAlbum->business->user_id != auth()->user()->id || $businessAlbum->business_id != $params['businessId'] ){
                $message = "Unauthorised access";
                return apiResponse($data,$message,$statusCode);
            }
        }catch(ModelNotFoundException $exception){
            $data = [];
            $message = 'Album does not exist.';
            $statusCode = 404;
            return apiResponse($data, $message, $statusCode);
        }
        return $next($request);
    }
}
