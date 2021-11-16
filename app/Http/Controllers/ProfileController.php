<?php

namespace App\Http\Controllers;

use App\AlbumMedia;
use Illuminate\Http\Request;
use App\Share;
use App\Post;
use App\Business;
use App\BusinessFollower;
use App\Network;
use App\NetworkFollower;
use App\User;
use App\UserCommunity;
use App\UserFollower;

class ProfileController extends Controller
{

    private $limit;

    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }

    public function profileInfo()
    {
        if (auth()->user()->id) {
            $message = 'Info about User';
            $user = User::where('id', auth()->user()->id)->get()->map(function ($item, $key) {
                $data = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'picture' => $item->profile_picture,
                    'followers' => $item->userfollower->count(),
                    'notification' => $item->notifications->count(),
                    'message' => $item->message->count(),
                ];
                return $data;
            });
            $businesses = Business::where('user_id', auth()->user()->id)->get()->map(function ($item, $key) {
                $data = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'picture' => $item->logo_path,
                    'category' => $item->category()->select('name')->get(),
                    'website' => $item->website,
                    'latitute' => $item->lat,
                    'longitute' => $item->lng,
                    'about_business' => $item->about_business,
                    'location_description' => $item->location_description,
                    'total_post' => $item->posts->count('id'),
                    'total_shares' => $item->shares = $item->posts->count('business_id'),
                    'total_visit' => $item->posts->sum('visit'),
                    'followers' => $item->businesscommunity->count(),
                    'notification' => $item->notification->count(),
                    'message' => $item->message->count(),
                ];
                return $data;
            });
            $data = [
                'owner' => $user,
                'business' => $businesses,
            ];
            return apiResponse($data, $message, 200);
        } else {
            $message = 'Not a user';
            $data = [];
            return apiResponse($data, $message, 402);
        }
    }

    public function businessInfo($id)
    {
        $message = 'Info about the Business of a user';
        $business = Business::find($id);
        $data = [
            'id' => $business->id,
            'name' => $business->name,
            'picture' => $business->logo_path,
            'category' => $business->category()->select('name')->get(),
            'website' => $business->website,
            'latitute' => $business->lat,
            'longitute' => $business->lng,
            'about_business' => $business->about_business,
            'location_description' => $business->location_description,
            'total_post' => $business->posts->count('id'),
            'total_shares' => $business->shares = $business->posts->count('business_id'),
            'total_visit' => $business->posts->sum('visit'),
            'followers' => $business->businesscommunity->count(),
            'notification' => $business->notification->count(),
            'message' => $business->message->count(),
        ];
        return apiResponse($data, $message, 200);
    }

    public function poepleFollower(Request $request, $page = 0)
    {
        $offset = ($page - 1) * $this->limit;
        $message = 'User Followers of the Owner';
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $followerId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'user', 'type' => 'follower'])->get('follower_id');
        $query = User::whereIn('id', $followerId);
        if ($request->has('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }
        $userFollowers = $query->get()->skip($offset)->take($this->limit)->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'profile_picture' => $item->profile_picture,
                'followers' => $item->userfollower->count(),
            ];
        });
        $totalUserFollowers = count($userFollowers);
        $data =  [
            'user_followers' => $userFollowers,
            'total_user_follower' => $totalUserFollowers,
        ];

        return apiResponse($data, $message, 200);
    }

    public function poepleFollowing(Request $request, $page = 0)
    {
        $offset = ($page - 1) * $this->limit;
        $message = 'Users Following the Owner';
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $followingId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'user', 'type' => 'following'])->get('follower_id');
        $query = User::whereIn('id', $followingId);
        if ($request->has('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }
        $userFollowing = $query->get()->skip($offset)->take($this->limit)->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'profile_picture' => $item->profile_picture,
                'followers' => $item->userfollower->count(),
            ];
        });
        $totalUserFollowing = Count($userFollowing);
        $data = [
            'user_following' => $userFollowing,
            'total_user_following' => $totalUserFollowing,
        ];

        return apiResponse($data, $message, 200);
    }

    public function businessFollower(Request $request, $page = 0)
    {
        $offset = ($page - 1) * $this->limit;
        $message = 'Business Followers of the Owner';
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $followerId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'business', 'type' => 'follower'])->get('follower_id');
        $query = Business::whereIn('id', $followerId);
        if ($request->has('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }
        $businessFollower = $query->get()->skip($offset)->take($this->limit)->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'picture' => $item->logo_path,
                'category' => $item->category()->select('name')->get(),
                'about_business' => $item->about_business,
                'location_description' => $item->location_description,
                'followers' => $item->businesscommunity->count(),
            ];
        });
        $totalBusinessFollowers = count($businessFollower);

        $data = [
            'business_followers' => $businessFollower,
            'total_business_follower' => $totalBusinessFollowers,
        ];

        return apiResponse($data, $message, 200);
    }

    public function businessFollowing(Request $request, $page = 0)
    {
        $offset = ($page - 1) * $this->limit;
        $message = 'Business Following of the Owner';
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $followingId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'business', 'type' => 'following'])->get('follower_id');
        $query = Business::whereIn('id', $followingId);
        if ($request->has('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }
        $businessFollowing = $query->get()->skip($offset)->take($this->limit)->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'picture' => $item->logo_path,
                'category' => $item->category()->select('name')->get(),
                'about_business' => $item->about_business,
                'location_description' => $item->location_description,
                'followers' => $item->businesscommunity->count(),
            ];
        });
        $totalBusinessFollowing = Count($businessFollowing);

        $data = [
            'business_following' => $businessFollowing,
            'total_business_following' => $totalBusinessFollowing,
        ];

        return apiResponse($data, $message, 200);
    }

    public function networkFollower(Request $request, $page = 0)
    {
        $offset = ($page - 1) * $this->limit;
        $message = 'Network Followers of the Owner';
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $followerId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'network', 'type' => 'follower'])->get('follower_id');
        $query = Network::whereIn('id', $followerId);
        if ($request->has('keyword')) {
            $query->where('name', 'like', '%' . $request->query . '%');
        }
        $networkFollower = $query->get()->skip($offset)->take($this->limit)->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'picture' => $item->image,
                'category' => $item->network_categories,
                'about_network' => $item->description,
                'location_description' => $item->address,
                'followers' => $item->networkFollower->count(),
            ];
        });
        $totalNetworkFollowers = count($networkFollower);

        $data = [
            'network_followers' => $networkFollower,
            'total_network_follower' => $totalNetworkFollowers,
        ];

        return apiResponse($data, $message, 200);
    }

    public function networkFollowing(Request $request, $page = 0)
    {
        $offset = ($page - 1) * $this->limit;
        $message = 'Network Following of the Owner';
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $followingId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'network', 'type' => 'following'])->get('follower_id');
        $query = Network::whereIn('id', $followingId);
        if ($request->has('keyword')) {
            $query->where('name', 'like', '%' . $request->query . '%');
        }
        $networkFollowing = $query->get()->skip($offset)->take($this->limit)->map(function ($item, $key) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'picture' => $item->image,
                'category' => $item->network_categories,
                'about_network' => $item->description,
                'location_description' => $item->address,
                'followers' => $item->networkFollower->count(),
            ];
        });
        $totalNetworkFollowing = Count($networkFollowing);

        $data = [
            'network_following' => $networkFollowing,
            'total_network_following' => $totalNetworkFollowing,
        ];

        return apiResponse($data, $message, 200);
    }

    public function communityTotal(Request $request)
    {
        $data = [];
        $message = "Total users, businesses, and networks following a user viewing";
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $userCommunity = UserCommunity::where('user_id', $id)->count('follower_id');

        $userUserFollowingId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'user', 'type' => 'following'])->get('follower_id');
        $userUserFollowerId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'user', 'type' => 'follower'])->get('follower_id');
        $userUserFollowers = User::whereIn('id', $userUserFollowerId)->get();
        $userUserFollowing = User::whereIn('id', $userUserFollowingId)->get();
        $totalUserFollowers = count($userUserFollowers);
        $totalUserFollowing = Count($userUserFollowing);
        $totalPeople = $totalUserFollowers + $totalUserFollowing;

        $userBusinessFollowerId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'business', 'type' => 'follower'])->get('follower_id');
        $userBusinessFollowingId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'business', 'type' => 'following'])->get('follower_id');
        $userBusinessFollower = Business::whereIn('id', $userBusinessFollowerId)->get();
        $userBusinessFollowing = Business::whereIn('id', $userBusinessFollowingId)->get();
        $totalBusinessFollowers = count($userBusinessFollower);
        $totalBusinessFollowing = Count($userBusinessFollowing);
        $totalBusiness = $totalBusinessFollowers + $totalBusinessFollowing;

        $userNetworkFollowerId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'network', 'type' => 'follower'])->get('follower_id');
        $userNetworkFollowingId = UserCommunity::where(['user_id' => $id, 'follower_type' => 'network', 'type' => 'following'])->get('follower_id');
        $userNetworkFollower = Network::whereIn('id', $userNetworkFollowerId)->get();
        $userNetworkFollowing = Network::whereIn('id', $userNetworkFollowingId)->get();
        $totalNetworkFollowers = count($userNetworkFollower);
        $totalNetworkFollowing = Count($userNetworkFollowing);
        $totalNetwork = $totalNetworkFollowers + $totalNetworkFollowing;

        $data = [
            'user_followers' => $userUserFollowers,
            'user_following' => $userUserFollowing,
            'total_user_following' => $totalUserFollowing,
            'total_user_follower' => $totalUserFollowers,

            'business_followers' => $userBusinessFollower,
            'business_following' => $userBusinessFollowing,
            'total_business_follower' => $totalBusinessFollowers,
            'total_business_following' => $totalBusinessFollowing,

            'network_followers' => $userNetworkFollower,
            'network_following' => $userNetworkFollowing,
            'total_network_follower' => $totalNetworkFollowers,
            'total_network_following' => $totalNetworkFollowing,
            'total_people' => $totalPeople,
            'total_business' => $totalBusiness,
            'total_network' => $totalNetwork,

            'total_community' => $userCommunity
        ];

        return apiResponse($data, $message, 200);
    }

    public function detailBusiness($page = 0)
    {
        $offset = ($page - 1) * $this->limit;
        $business = Business::get();
        if (!$business) {
            $message = 'Businesses not found';
            $data = [];
            return apiResponse($data, $message, 404);
        } else {
            $message = 'Detail about Business';
            $data = $business->skip($offset)->take($this->limit)->map(function ($item, $key) {
                $data = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'picture' => $item->logo_path,
                    'category' => $item->category()->select('name')->get(),
                    'about_business' => $item->about_business,
                    'location_description' => $item->location_description,
                    'followers' => $item->businesscommunity->count(),
                ];
                return $data;
            });
            return apiResponse($data, $message, 200);
        }
    }

    public function detailNetwork($page = 0)
    {
        $network = Network::get();
        $offset = ($page - 1) * $this->limit;
        if (!$network) {
            $message = 'Network not found';
            $data = [];
            return apiResponse($data, $message, 404);
        } else {
            $message = 'Detail about Network';
            $data = $network->skip($offset)->take($this->limit)->map(function ($item, $key) {
                $data = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'picture' => $item->image,
                    'category' => $item->network_categories,
                    'about_network' => $item->description,
                    'location_description' => $item->address,
                    'followers' => $item->networkFollower->count(),
                ];
                return $data;
            });
            return apiResponse($data, $message, 200);
        }
    }

    public function userBusiness(Request $request, $page = 0)
    {
        $offset = ($page - 1) * $this->limit;
        $message = 'Detail about owner Businesses';
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $business = Business::where('user_id', $id);
        $data = $business->skip($offset)->take($this->limit)->orderByDesc('created_at')->map(function ($item, $key) {
            $data = [
                'business_id' => $item->id,
                'business_name' => $item->name,
                'logo_path' => $item->logo_path,
                'category' => $item->category()->select('name')->get(),
                'content' => $item->content,
                'lat' => $item->lat,
                'lng' => $item->lng,
                'city' => $item->city,
                'about_business' => $item->about_business,
                'location_description' => $item->location_description,
                'community' => $item->businesscommunity->count(),
                'created_at' => $item->created_at,
            ];
            return $data;
        });

        return apiResponse($data, $message, 200);
    }

    public function userNetwork(Request $request, $page = 0)
    {
        $data = [];
        $offset = ($page - 1) * $this->limit;
        $id = auth()->user()->id;
        if (isset($request->id)) {
            $id = $request->id;
        }
        $having_network = Network::find($id);
        if (isset($having_network)) {
            $business = Network::where('user_id', $id);
            $data = $business->skip($offset)->take($this->limit)->map(function ($item, $key) {
                $data = [
                    'network_id' => $item->id,
                    'network_name' => $item->name,
                    'network_image' => $item->image,
                    'category' => $item->network_categories,
                    'description' => $item->description,
                    'address' => $item->address,
                    'purpose' => $item->purpose,
                    'city' => $item->city,
                    'community' => $item->networkfollower->count(),
                    'created_at' => $item->created_at,
                ];
                return $data;
            });
        }

        $message = 'Detail about Business of requested User';

        return apiResponse($data, $message, 200);
    }

    public function media(Request $request)
    {
        $message = ' User Images ';
        $statusCode = '200';
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $media = AlbumMedia::where('user_id', $id)->take(9);
        $data = $media->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'image_url' => getMediaUrl($item->path),
                'album_id' => $item->album_id,
            ];
        });

        return apiResponse($data, $message, $statusCode);
    }
}
