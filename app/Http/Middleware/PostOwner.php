<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Post;

class PostOwner
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
        $postId = $request->route('postId');
        try{
            $post = Post::findorfail($postId);
            if($post->user_id != auth()->user()->id){
                $message = "Unauthorised access to resource";
                return apiResponse($data,$message,$statusCode);
            }
        }catch(ModelNotFoundException $exception){
            $data = [];
            $message = 'Post does not exist.';
            $statusCode = 404;
            return apiResponse($data,$message,$statusCode);
        }
        return $next($request);
    }
}
