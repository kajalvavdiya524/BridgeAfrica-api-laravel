<?php

namespace App\Http\Controllers;

use App\BusinessCommunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class BusinessCommunityController extends Controller
{
    private $limit;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->limit = config()->get('app.pagination');
    }

    /**
     * Display a listing of Follower (People)
     * 
     * @return \Illuminate\Http\Resonse
     */
    public function peopleFollower($id, $keyword = ''){
        if(isset($keyword)){
            $data = Community::join('users', 'business_followers.follower_id', '=', 'users.id')
                ->where('business_followers.business_id', $id)
                ->where('business_followers.follower_type', 'user')
                ->where('business_followers.type', 'follower')
                ->where('users.name','LIKE','%'.$keyword.'%')
                ->select('users.id', 'users.name', 'users.profile_picture', 'users.city', 'users.country')
                ->paginate($this->limit);
            $message = 'Search Term "'.$keyword.'" Listing Successfully.';

        }else{
            $data = Community::join('users', 'business_followers.follower_id', '=', 'users.id')
                ->where('business_followers.business_id', $id)
                ->where('business_followers.follower_type', 'user')
                ->where('business_followers.type', 'follower')
                ->select('users.id', 'users.name', 'users.profile_picture', 'users.city', 'users.country')
                ->paginate($this->limit);
            $message = 'User Follower Listing Successfully.';
        }

        return apiResponse($data, $message , 200);
    }

    /**
     * Display a listing of Follower (Business)
     * 
     * @return \Illuminate\Http\Response
     */
    public function businessFollower($id, $keyword = ''){
        if(isset($keyword)){
            $data = Community::join('businesses', 'business_community.business_id', '=', 'businesses.id')
                ->where('business_community.business_id', $id)
                ->where('business_community.follower_type', 'business')
                ->where('business_community.type', 'follower')
                ->where('businesses.name', 'like','%'.$keyword.'%' )
                ->select('businesses.id', 'businesses.name', 'businesses.logo_path', 'businesses.about_business', 'businesses.city', 'businesses.country')
                ->paginate($this->limit);
            $message = 'Search Term "'.$keyword.'" Listing Successfully.';
        }else{
            $data = Community::join('businesses', 'business_community.follower_id', '=', 'businesses.id')
                ->where('business_community.business_id', $id)
                ->where('business_community.follower_type', 'business')
                ->where('business_community.type', 'follower')
                ->select('businesses.id', 'businesses.name', 'businesses.logo_path', 'businesses.about_business', 'businesses.city', 'businesses.country')
                ->paginate($this->limit);
            $message = 'Business Follower Listing Successfully.';
        }

        return apiResponse($data, $message , 200);
    }

    /**
     * Display a listing of Following (People)
     * 
     * @return \Illuminate\Http\Resonse
     */
    public function peopleFollowing($id, $keyword = ''){
        if(isset($keyword)){
            $data = Community::join('users', 'business_community.follower_id', '=', 'users.id')
                ->where('business_community.business_id', $id)
                ->where('business_community.follower_type', 'user')
                ->where('business_community.type', 'following')
                ->where('users.name', 'like','%'.$keyword.'%' )
                ->select('users.id', 'users.name', 'users.profile_picture', 'users.city', 'users.country')
                ->paginate($this->limit);
            $message = 'Search Term "'.$keyword.'" Listing Successfully.';
        }else{
            $data = Community::join('users', 'business_community.follower_id', '=', 'users.id')
                    ->where('business_community.business_id', $id)
                    ->where('business_community.follower_type', 'user')
                    ->where('business_community.type', 'following')
                    ->select('users.id', 'users.name', 'users.profile_picture', 'users.city', 'users.country')
                    ->paginate($this->limit);
            $message = 'User Following Listing Successfully.';
        }

        return apiResponse($data, $message , 200);
    }

    /**
     * Display a listing of Following (Business)
     * 
     * @return \Illuminate\Http\Resonse
     */
    public function businessFollowing($id, $keyword = ''){
        if(isset($keyword)){
            $data = Community::join('businesses', 'business_community.business_id', '=', 'businesses.id')
                ->where('business_community.business_id', $id)
                ->where('business_community.follower_type', 'business')
                ->where('business_community.type', 'following')
                ->where('businesses.name', 'like','%'.$keyword.'%' )
                ->select('businesses.id', 'businesses.name', 'businesses.logo_path', 'businesses.about_business', 'businesses.city', 'businesses.country')
                ->paginate($this->limit);
            $message = 'Search Term "'.$keyword.'" Listing Successfully.';
        }else{
            $data = Community::join('businesses', 'business_community.follower_id', '=', 'businesses.id')
                ->where('business_community.business_id', $id)
                ->where('business_community.follower_type', 'business')
                ->where('business_community.type', 'following')
                ->select('businesses.id', 'businesses.name', 'businesses.logo_path', 'businesses.about_business', 'businesses.city', 'businesses.country')
                ->paginate($this->limit);
            $message = 'Business Following Listing Successfully.'; 
        }
        
        return apiResponse($data, $message , 200);
    }
}
