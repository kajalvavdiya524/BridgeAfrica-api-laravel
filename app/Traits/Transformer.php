<?php

namespace App\Traits;

trait Transformer
{
    // pagination
    public static function transformCollection($collection)
    {
        $params = http_build_query(request()->except('page'));
        $next = $collection->nextPageUrl();
        $previous = $collection->previousPageUrl();
        if ($params) {
            if ($next) {
                $next .= "&{$params}";
            }
            if ($previous) {
                $previous .= "&{$params}";
            }
        }
        $meta = [
            "next" => (string)$next,
            "previous" => (string)$previous,
            "per_page" => (int)$collection->perPage(),
            "total" => (int)$collection->total()
        ];
        return $meta;
    }

    // tranformer all networks
    public static function networks($networks, $is_collection = true)
    {
        $data = [];
        if ($is_collection) {
            foreach ($networks as $network) {
                $data[] = self::network($network);
            }
        } else {
            $data = self::network($networks);
        }
        return $data;
    }

    // tranformer single network
    public static function network($network)
    {
        $data = [
            'id' => (int)$network->id,
            'admin_user_id' => (int)$network->user_id,
            'admin_name' => (string)$network->users->name,
            'business_id' => (string)$network->business_id,
            'business_name' => (string)$network->business->name,
            'name' => (string)$network->name,
            'description' => (string)$network->description,
            'purpose' => (string)$network->purpose,
            'special_needs' => (string)$network->special_needs,
            'address' => (string)$network->address,
            'neighborhood' => (string)$network->NeighborhoodNetworks,
            'council' => (string)$network->council->name,
            'division' => (string)$network->division->name,
            'region' => (string)$network->region->name,
            'country' => (string)$network->country->name,
            'primary_phone' => (string)$network->primary_phone,
            'secondary_phone' => (string)$network->secondary_phone,
            'categories' => (String)$network->network_categories,
            'assign_categories' => $network->networkCategory,
            'image' => (string)$network->image,
            'allow_business' => (string)$network->allow_business,
            'location_description' => (string)$network->location_description,
            'created_at' => (string)$network->created_at,
            'updated_at' => (string)$network->updated_at,
            'member_count' => (int)$network->networkUsers->count(),
            'is_approve' => (int)$network->is_approve
        ];
        return $data;
    }

    //transform all posts
    public static function posts($posts, $is_collection = true){
        $data = [];
        if($is_collection){
            foreach($posts as $post){
                $data[] = self::posts($post);
            }
        } else{
            $data = self::posts($posts);
        }
        return $data;
    }

    //transformer single post
    public static function post($post)
    {
        $data = [
            'id' => (int)$post->id,
            'content' => (string)$post->content,
            'name' => (string)$post->user->name,
            'profile_picture' => (string)$post->user->profile_picture,
            'likes_count' => (int)$post->like->count(),
            'comment_count' => $post->comment->count(),
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at,
            'comments' => $post->comment->map(function ($comment, $key) {
                $data = [
                    'text' => $comment->comment,
                    'post_id' => $comment->post_id,
                    'user_id' => $comment->user_id,
                ];
                return $data;
            }),
        ];

        return $data;
    }


    //transformer all notifications
    public static function notifications($notifications, $is_collection = true)
    {
        $data = [];
        if ($is_collection) {
            foreach ($notifications as $notification) {
                $data[] = self::notification($notification);
            }
        } else {
            $data = self::notification($notifications);
        }
        return $data;
    }

    //tranformer single notification
    public static function notification($notification)
    {
        $data = [
            'id' => (int)$notification->id,
            'reference_type' => (string)$notification->reference_type,
            'reference_id' => (int)$notification->reference_id,
            'notification_text' => (string)$notification->notification_text,
            'user_id' => (int)$notification->user_id,
            'mark_as_read' => (int)$notification->mark_as_read,
            'created_at' => (string)$notification->created_at,
            'updated_at' => (string)$notification->updated_at
        ];

        return $data;
    }

    //transformer all market products
    public static function markets($markets, $is_collection = true)
    {
        $data = [];
        if ($is_collection) {
            foreach ($markets as $market) {
                $data[] = self::market($market);
            }
        } else {
            $data = self::market($markets);
        }
        return $data;
    }

    //transformer single market
    public static function market($market)
    {
        $data = [
            'id' => (int)$market->id,
            'business_id' =>  (int)$market->business_id,
            'business_name' => (string)$market->business->name,
            'name' =>  (string)$market->name,
            'description' =>  (string)$market->description,
            'picture' =>  (string)$market->picture,
            'categories' => $market->categoryMarket,
            'subcategories' => $market->marketSubcategory,
            'filters' => $market->filterMarket,
            'price' =>  (string)$market->price,
            'discount_price' =>  (string)$market->discount_price,
            'on_discount' => (int)$market->on_discount,
            'condition' =>  (string)$market->condition,
            'is_service' => (int)$market->is_service,
            'in_stock' => (int)$market->in_stock,
            'created_at' => (string)$market->created_at,
            'updated_at' => (string)$market->updated_at
        ];
        return $data;
    }

    //transformer single user
    public static function user($user)
    {
        $data = [
            'id' => (int)$user->id,
            'name' => (string)$user->name,
            'email' => (string)$user->email,
            'profile_picture' => (string)$user->profile_picture,
            'cover_picture' => (string) getMediaUrl($user->cover_picture),
            'phone' => (int)$user->phone,
            'dob' => (string)$user->dob,
            'language' => (string)$user->language,
            'status' => (int)$user->status,
            'provider' => (int)$user->provider,
            'provider_id' => (int)$user->provider_id,
            'country' => (string)$user->country,
            'city' => (string)$user->city,
            'home_town' => (string)$user->home_town,
            'gender' => (string)$user->gender,
            'neighbor' => (string)$user->neighbor,
            'region' => (string)$user->region,
            'address' => (string)$user->address,
            'profile_complete' => (int)$user->profile_complete,
            'profession' => (string)$user->profession,
            'biography' => (string)$user->biography,
            'user_account_package_id' => $user->user_account_package_id,
            'updated_at' => $user->updated_at,
            'followers' => $user->userfollower->count(),
        ];

        return $data;
    }

    //transform all users
    public static function users($users, $is_collection = true){
        $data = [];
        if($is_collection){
            foreach($users as $user){
                $data[] = self::user($user);
            }
        } else{
            $data = self::user($users);
        }
        return $data;
    }
    
    //tranformer single business
    public static function business($business)
    {
        $data = [
            'id' => $business->id,
            'logo_path' => $business->logo_path,
            'name' => $business->name,
            'category' => $business->category()->select('name')->get(),
            'lat' => $business->lat,
            'lng' => $business->lng,
            'followers' => $business->businesscommunity->where('type', 'follower')->count(),
            'location_description' => $business->location_description,
            'about_business' => $business->about_business,
            'created_at' => $business->created_at,
        ];
        return $data;
    }

    //transformer all businesses
    public static function businesses($businesses, $is_collection = true)
    {
        $data = [];
        if ($is_collection) {
            foreach ($businesses as $business) {
                $data[] = self::business($business);
            }
        } else {
            $data = self::business($businesses);
        }
        return $data;
    }

    //tranformer single user
    public static function userCard($user)
    {
        $data = [
            'id' => $user->id,
            'name' => $user->name,
            'profile_picutre' => $user->profile_picture,
            'followers' => $user->userfollower->count()
        ];
        return $data;
    }

    //transformer all user
    public static function userCards($users, $is_collection = true)
    {
        $data = [];
        if ($is_collection) {
            foreach ($users as $user) {
                $data[] = self::user($user);
            }
        } else {
            $data = self::user($users);
        }
        return $data;
    }
}
