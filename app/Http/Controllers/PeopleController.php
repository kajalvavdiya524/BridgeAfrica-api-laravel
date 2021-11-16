<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\User;
use App\UserFollower;
use App\BusinessFollower;
use App\NetworkFollower;
use App\Business;
use App\Network;
use App\NetworkMember;

class PeopleController extends Controller
{
    public function listUsers($q = '')
    {
        if (isset($q)) {
            $data = User::where('name', 'like', '%' . $q . '%')->get()->map(function ($user) {
                return [
                    'name' => $user->name,
                    'profile_picture' => $user->profile_picture,
                    'followers' => $user->userfollower->count(),
                ];
            });
            $message = 'Search Term "' . $q . '" Listing Successfully.';
        } else {
            $data = User::get()->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile_picture' => $user->profile_picture,
                    'followers' => $user->userfollower->count(),
                ];
            });;
            $message = 'Users Listing Successfully.';
        }
        $message = 'Users Listing Successfully.';

        return apiResponse($data, $message, 200);
    }

    public function listUserProfession($q = '')
    {
        if (isset($q)) {
            $statusCode = '200';
            $data = User::where('profession', 'like', '%' . $q . '%')->get()->map(function ($user) {
                return [
                    'name' => $user->name,
                    'profile_picture' => $user->profile_picture,
                    'followers' => $user->userfollower->count(),
                ];
            });
            $message = 'Search Term "' . $q . '" Listing Successfully.';
        } else {
            $statusCode = '404';
            $message = 'Profession not found';
        }
        $message = 'Users Listing Successfully.';

        return apiResponse($data, $message, $statusCode);
    }

    public function listProfessionByCommunity(Request $request)
    {
        $data = [];
        $message = ' ';
        $statusCode = 200;
        $query = User::join('user_followers as uf', 'uf.user_id', '=', 'users.id');
        if ($request->has('user_follower')) {
            $query = $query->where(['uf.user_id' => auth()->user()->id, 'follower_type' => 'user']);
        }
        if ($request->has('user_following')) {
            $query = $query->where(['uf.follower_id' => auth()->user()->id, 'follower_type' => 'user']);
        }
        if ($request->has('user_community')) {
            $query = $query->orWhere(['uf.user_id' => auth()->user()->id,  'uf.follower_id' => auth()->user()->id])->where('follower_type', 'user');
        }

        if ($request->has('business_follower')) {
            $user = Business::join('business_followers as bf', 'bf.business_id', '=', 'businesses.id')
            ->where('bf.follower_type', 'user')->get('follower_id');
            $query = User::whereIn('id', $user);
        }
        if ($request->has('business_following')) {
            $business = Business::where('user_id', auth()->user()->id)->get('id');
            $user = $query->where('uf.follower_type', 'business')->whereIn('uf.follower_id', $business)->get('user_id');
            $query = User::whereIn('id', $user);
        }
        if ($request->has('business_community')) {
            $user1 = Business::join('business_followers as bf', 'bf.business_id', '=', 'businesses.id')
            ->where('bf.follower_type', 'user')->get('follower_id');
            $business = Business::where('user_id', auth()->user()->id)->get('id');
            $user2 = $query->where('uf.follower_type', 'business')->whereIn('uf.follower_id', $business)->get('user_id');
            $query = User::whereIn('id', $user1)->orWhereIn('id', $user2);
        }

        if ($request->has('network_follower')) {
            $user = Network::join('network_followers as nf', 'nf.network_id', '=', 'networks.id')
            ->where('nf.follower_type', 'user')->get('follower_id');
            $query = User::whereIn('id', $user);
        }
        if ($request->has('network_following')) {
            $network = Network::where('user_id', auth()->user()->id)->get('id');
            $user = $query->where('uf.follower_type', 'network')->whereIn('uf.follower_id', $network)->get('user_id');
            $query = User::whereIn('id', $user);
        }
        if ($request->has('network_community')) {
            $user1 = Network::join('network_followers as nf', 'nf.network_id', '=', 'networks.id')
            ->where('nf.follower_type', 'user')->get('follower_id');
            $network = Network::where('user_id', auth()->user()->id)->get('id');
            $user2 = $query->where('uf.follower_type', 'network')->whereIn('uf.follower_id', $network)->get('user_id');
            $query = User::whereIn('id', $user1)->orWhereIn('id', $user2);
        }
        if ($request->has('network_member')) {
            $network = Network::where('user_id', auth()->user()->id)->get('id');
            $networkUser = NetworkMember::whereIn('network_id', $network)->get('user_id');
            $query = User::whereIn('id', $networkUser);
        }
        $data = $query->get()->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'profile_picture' => $user->profile_picture,
                        'followers' => $user->userfollower->count(),
                    ];
                });

        return apiResponse($data, $message, $statusCode);
    }

}
