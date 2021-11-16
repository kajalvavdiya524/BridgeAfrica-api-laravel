<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Business;
use App\User;
use App\BusinessFollower;
use App\Community;
use App\Network;
use App\UserFollower;
use App\Traits\Transformer;

class BusinessCommunity extends Controller
{
    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }

    /**
     * Display listing of businesses following the business
     * 
     * @param $id  (business id)
     * @param $page (used for pagination)
     * @return array
     */
    public function businessFollower($id, $page = 0)
    {
        $data = [];
        $message = "";
        $statusCode = 200;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $offset = ($page - 1) * $this->limit;
        $query = Business::join('business_followers as bc', 'bc.follower_id', '=', 'businesses.id')
            ->where(['bc.follower_type' => 'business', 'type' => 'follower', 'bc.business_id' => $id]);
        $businessFollowers = $query->get()->skip($offset)->take($this->limit);
        $businessFollowers = Transformer::businesses($businessFollowers);
        $totalBusinessFollowers = count($businessFollowers);
        $data = [
            'total_followers' => $totalBusinessFollowers,
            'usiness_followers' => $businessFollowers,
        ];
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display listing of businesses the business follows
     * 
     * @param $id  (business id)
     * @param $page (used for pagination)
     * @return array
     */
    public function businessFollowing($id, $page = 0)
    {
        $data = [];
        $message = "";
        $statusCode = 200;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $offset = ($page - 1) * $this->limit;
        $query = Business::join('business_followers as bc', 'bc.follower_id', '=', 'businesses.id')
            ->where(['bc.follower_type' => 'business', 'type' => 'following', 'bc.business_id' => $id]);
        $businessFollowers = $query->get()->skip($offset)->take($this->limit);
        $businessFollowers = Transformer::businesses($businessFollowers);
        $totalBusinessFollowers = count($businessFollowers);
        $data = [
            'total_followers' => $totalBusinessFollowers,
            'usiness_followers' => $businessFollowers,
        ];
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display listing of people following business
     * 
     * @param $id  (business id)
     * @param $page (used for pagination)
     * @return array
     */
    public function peopleFollower($id, $page = 0)
    {
        $data = [];
        $message = "People Follower Listing Successful";
        $statusCode = 200;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $offset = ($page - 1) * $this->limit;
        $query = User::join('business_followers as bc', 'bc.follower_id', '=', 'users.id')
            ->where(['bc.follower_type' => 'user', 'type' => 'follower', 'bc.business_id' => $id]);
        $peopleFollowers = $query->get()->skip($offset)->take($this->limit);
        $peopleFollowers = Transformer::users($peopleFollowers);
        $totalpeopleFollowers = count($peopleFollowers);
        $data = [
            'total_followers' => $totalpeopleFollowers,
            'usiness_followers' => $peopleFollowers,
        ];
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display listing of people business follows
     * 
     * @param $id  (business id)
     * @param $page (used for pagination)
     * @return array
     */
    public function peopleFollowing($id, $page = 0)
    {
        $data = [];
        $message = "People Following Listing Successful";
        $statusCode = 200;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $offset = ($page - 1) * $this->limit;
        $query = User::join('business_followers as bc', 'bc.follower_id', '=', 'users.id')
            ->where(['bc.follower_type' => 'user', 'type' => 'following', 'bc.business_id' => $id]);
        $peopleFollowing = $query->get()->skip($offset)->take($this->limit);
        $peopleFollowing = Transformer::users($peopleFollowing);
        $totalpeopleFollowing = count($peopleFollowing);
        $data = [
            'total_followers' => $totalpeopleFollowing,
            'usiness_followers' => $peopleFollowing,
        ];
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display listing of people following business
     * 
     * @param $id  (business id)
     * @param $page (used for pagination)
     * @return array
     */
    public function networkFollower($id, $page = 0)
    {
        $data = [];
        $message = "Network Follower Listing Successful";
        $statusCode = 200;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $offset = ($page - 1) * $this->limit;
        $query = Network::join('business_followers as bc', 'bc.follower_id', '=', 'networks.id')
            ->where(['bc.follower_type' => 'network', 'type' => 'follower', 'bc.business_id' => $id]);
        $peopleFollowers = $query->get()->skip($offset)->take($this->limit);
        $peopleFollowers = Transformer::users($peopleFollowers);
        $totalPeopleFollowers = count($peopleFollowers);
        $data = [
            'total_followers' => $totalPeopleFollowers,
            'usiness_followers' => $peopleFollowers,
        ];
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display listing of people business follows
     * 
     * @param $id  (business id)
     * @param $page (used for pagination)
     * @return array
     */
    public function networkFollowing($id, $page = 0)
    {
        $data = [];
        $message = "Network Following Listing Successful";
        $statusCode = 200;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $offset = ($page - 1) * $this->limit;
        $query = User::join('business_followers as bc', 'bc.follower_id', '=', 'networks.id')
            ->where(['bc.follower_type' => 'network', 'type' => 'following', 'bc.business_id' => $id]);
        $peopleFollowing = $query->get()->skip($offset)->take($this->limit);
        $peopleFollowing = Transformer::users($peopleFollowing);
        $totalpeopleFollowing = count($peopleFollowing);
        $data = [
            'total_followers' => $totalpeopleFollowing,
            'usiness_followers' => $peopleFollowing,
        ];
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display ltotal number of followers
     * 
     * @param $id  (business id)
     * @return int
     */
    public function communityTotal($id)
    {
        $data = [];
        $message = "Total Followers";
        $statusCode = 200;
        $totalCommunity = Business::find($id)->businesscommunity->where('type', 'follower')->count();
        $data = [
            'total' => $totalCommunity
        ];
        return apiResponse($data, $message, $statusCode);
    }
}
