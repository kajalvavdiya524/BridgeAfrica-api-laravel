<?php

namespace App\Http\Controllers\Network;

use App\User;
use Exception;
use App\Network;
use App\Business;
use App\UserFollower;
use App\BusinessFollower;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NetworkCommunityController extends Controller
{
    /**
     * users who follow this network
     * the users that this network follows
     */
    public function peopleCommunity($id)
    {
        try {
            $data = [];
            $message = "";
            $statusCode = 200;
            $userFollowers = Network::find($id)->networkFollower()->where('follower_type', 'user')->pluck('follower_id');

            $userFollowers = User::whereIn('id', $userFollowers)->get()->map(function ($item, $key) {
                $data = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'profile_picutre' => $item->profile_picture,
                    'followers' => $item->userfollower->count()
                ];
                return $data;
            });

            $userFollowing = UserFollower::where(['follower_id' => $id, 'follower_type' => 'network'])->get('user_id');
            $userFollowing = User::whereIn('id', $userFollowing)->get()->map(function ($item, $key) {
                $data = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'profile_picutre' => $item->profile_picture,
                    'followers' => $item->userfollower->count(),
                ];
                return $data;
            });

            $totalUserFollowers = count($userFollowers);
            $totalUserFollowing = Count($userFollowing);
            $totalPeople = $totalUserFollowers + $totalUserFollowing;
            $data = [
                'total_people' => $totalPeople,
                'total_followers' => $totalUserFollowers,
                'user_followers' => $userFollowers,
                'totat_following' => $totalUserFollowing,
                'user_following' => $userFollowing,
            ];
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Network does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * businesses that follow this network and
     * the businesses that this network follows
     */

    public function businessCommunity($id)
    {
        try {
            $data = [];
            $message = "";
            $statusCode = 200;
            $businessFollowers = Network::find($id)->networkFollower()->where('follower_type', 'business')->pluck('follower_id');

            $businessFollowers = Business::whereIn('id', $businessFollowers)->get()->map(function ($item, $key) {
                $data = [
                    'logo_path' => $item->logo_path,
                    'name' => $item->about_business,
                    'category' => $item->category,
                    'followers' => $item->businessfollower->count(),
                    'location_description' => $item->location_description,
                    'about_business' => $item->about_business
                ];
                return $data;
            });

            $businessFollowing = BusinessFollower::where(['follower_id' => $id, 'follower_type' => 'network'])->get('business_id');
            $businessFollowing = Business::whereIn('id', $businessFollowing)->get()->map(function ($item, $key) {
                $data = [
                    'logo_path' => $item->logo_path,
                    'name' => $item->about_business,
                    'category' => $item->category,
                    'followers' => $item->businessfollower->count(),
                    'location_description' => $item->location_description,
                    'about_business' => $item->about_business
                ];
                return $data;
            });

            $totalBusinessFollowers = count($businessFollowers);
            $totalBusinessFollowing = Count($businessFollowing);
            $totalBusiness = $totalBusinessFollowers + $totalBusinessFollowing;
            $data = [
                'total_Business' => $totalBusiness,
                'total_followers' => $totalBusinessFollowers,
                'Business_followers' => $businessFollowers,
                'totat_following' => $totalBusinessFollowing,
                'Business_following' => $businessFollowing,
            ];
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Network does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }
}
