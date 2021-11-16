<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Share;
use Exception;
use App\Business;
use App\PostLike;
use App\BusinessRole;
use App\UserFollower;
use App\BusinessAlbum;
use App\BusinessMedia;
use App\BusinessBanned;
use App\BusinessCommunity;
use App\BusinessSetting;
use App\BusinessFollower;
use App\BusinessUserRole;
use App\BusinessOpenHours;
use Illuminate\Http\Request;
use App\BusinessNotification;
use App\Http\Requests\AddBusinessRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\BusinessIdentityCreate;
use App\PostShare;
use App\UserAccountPackage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Traits\Transformer;


class BusinessController extends Controller
{
    private $limit;

    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }

    /**
     * Store a newly created resource in storage.
     * creation of business
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function businessIdentity(BusinessIdentityCreate $request)
    {
        $data = [];
        $message = [];
        $statusCode = 200;
        $business = auth()->user()->business()->create([
            'name' => $request->name,
            'timezone' => $request->timezone,
            'language' => $request->language,
            'about_business' => $request->about_business,
            'lat' =>  $request->lat,
            'lng' =>  $request->lng,
            'address' =>  $request->address,
            'city' =>  $request->city,
        ]);
        $logoAlbum = $business->businessAlbum()->create([
            'name' => 'Logo',
        ]);
        $business->businessAlbum()->create([
            'name' => 'Cover Photo',
        ]);
        if (!empty($request->logo_path)) {
            $path = "public/business/" . $business->id . "/album";
            $storePath = imageUpload($path, $request->logo_path);
            $business->update(['logo_path' => $storePath]);
            $logoAlbum->businessMedia()->create([
                'reference_type' => 'logo',
                'reference_id' => $business->id,
                'media_type' => $request->logo_path->getMimeType(),
                'media_url' => $storePath,
            ]);
        }
        $business->category()->attach($request->categoryId);
        $business->subcategory()->attach($request->subCategoryId);
        $business->filters()->attach($request->filterId);
        $business->country()->attach($request->country);
        $business->region()->attach($request->region);
        $business->division()->attach($request->division);
        $business->council()->attach($request->council);
        $business->neighborhood()->attach($request->neigborhood);
        $business->businessSettings()->create();
        if ($request->keywords) {
            foreach ($request->keywords as $key => $value) {
                $business->keywords()->create([
                    'name' => $value
                ]);
            }
        }
        $message = "Successfully created";
        $data = [
            'business_id' => $business->id
        ];
        return apiResponse($data, $message, $statusCode);
    }

    public function businessInsight(Request $request, $id, Share $post)
    {
        $request->validate([
            'dateStarting' => 'required|date',
            'dateClosing' => 'required|date'
        ]);

        $business = Business::find($id);
        $postId = $business->posts->map(function ($item, $key) {
            $data = [
                'id' => $item->id,
            ];
            return $data;
        });
        if (!$postId) {
            $numberPost = 0;
            $numberShare = 0;
            $numberLike = 0;
            $totalPost = 0;
            $totalShare = 0;
            $totalLike = 0;
            $message = 'null';
        } else {
            $numberPost = Post::where('business_id', $id)
                ->whereBetween('updated_at', [$request->dateStarting, $request->dateClosing])
                ->count();
            $numberShare = PostShare::where('post_id', $postId)
                ->whereBetween('updated_at', [$request->dateStarting, $request->dateClosing])
                ->count();
            $numberLike = PostLike::where('user_id', $postId)
                ->whereBetween('updated_at', [$request->dateStarting, $request->dateClosing])
                ->count();
            $totalPost = Post::where('user_id', auth()->user()->id)
                ->whereBetween('updated_at', [$request->dateStarting, $request->dateClosing])
                ->count();
            $totalShare = PostShare::where('user_id', auth()->user()->id)
                ->whereBetween('updated_at', [$request->dateStarting, $request->dateClosing])
                ->count();
            $totalLike = PostLike::where('user_id', auth()->user()->id)
                ->whereBetween('updated_at', [$request->dateStarting, $request->dateClosing])
                ->count();
            $message = 'Not Null';
        }
        $data = [
            'number_likes' => $numberLike,
            'number_shares' => $numberShare,
            'number_posts' => $numberPost,
            'total_likes' => $totalLike,
            'total_shares' => $totalShare,
            'total_posts' => $totalPost
        ];

        return apiResponse($data, $message, 200);
    }

    public function displayUserBusiness(Request $request,$page = 0)
    {
        $data = [];
        $message = "User Business Successful";
        $statusCode = 200;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $offset = ($page - 1) * $this->limit;
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $business = Business::where('user_id', $id)->orderByDesc('created_at')->skip($offset)
        ->take($this->limit)
        ->get();
        $data = Transformer::businesses($business);
        return apiResponse($data, $message, $statusCode);
    }

    public function addBusiness(AddBusinessRequest $request)
    {
        $message = 'Your business have been added';
        $statusCode = 200;
        $data = [];
        $business = auth()->user()->business()->create([
            'name' => $request->name,
            'timezone' => $request->timezone,
            'about_business' => $request->about_business,
            'phone' => $request->phone1,
            'secondary_phone' => $request->phone2,
            'website' => $request->website,
            'email' => $request->email,
            'city' => $request->city,
        ]);
        $business->businessAlbum()->create([
            'name' => 'Cover Photo',
        ]);
        $businessAlbum = $business->businessAlbum()->create([
            'name' => 'logo',
        ]);
        if (!empty($request->logo_path)) {
            $path = "public/business/" . $business->id . "/album";
            $storePath = imageUpload($path, $request->logo_path);
            $business->update(['logo_path' => $storePath]);
            $businessAlbum->businessMedia()->create([
                'reference_type' => 'logo',
                'reference_id' => $business->id,
                'media_type' => $request->logo_path->getMimeType(),
                'media_url' => $storePath,
            ]);
        }
        $business->category()->attach($request->categoryId);
        $business->subcategory()->attach($request->subCategoryId);
        $business->filters()->attach($request->filterId);
        $business->country()->attach($request->country);
        $business->region()->attach($request->region);
        $business->division()->attach($request->division);
        $business->council()->attach($request->council);
        $business->neighborhood()->attach($request->neigborhood);
        $business->businessSettings()->create();
        if ($request->keywords) {
            foreach ($request->keywords as $key => $value) {
                $business->keywords()->create([
                    'name' => $value
                ]);
            }
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function displayFollowing()
    {
        if (auth()->user()->id) {
            $message = 'Display following';
            $followingId = UserFollower::where('follower_id', auth()->user()->id)->get('user_id');
            $data = User::whereIn('id', $followingId)->get()->map(function ($item, $key) {
                return [
                    'user_id' => $item->id,
                    'name' => $item->name
                ];
            });
            return apiResponse($data, $message, 200);
        } else {
            $message = 'No Following';
            $data = [];
            return apiResponse($data, $message, 404);
        }
    }

    public function displayRole()
    {
        if (auth()->user()->id) {
            $message = 'Display roles';
            $data = BusinessRole::get()->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->role_name
                ];
            });
            return apiResponse($data, $message, 200);
        } else {
            $message = 'No Role';
            $data = [];
            return apiResponse($data, $message, 404);
        }
    }

    public function assignRole(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'role_id' => 'required|numeric'
        ]);

        try {
            User::findorfail($request->user_id);
            BusinessRole::findOrfail($request->role_id);
            Business::findOrfail($id);
            DB::table('business_user_roles')
                ->updateOrInsert(
                    ['user_id' => $request->user_id, 'business_id' => $id],
                    ['business_role_id' => $request->role_id]
                );
            $message = 'Role have been saved';
            $data = [];
            $statusCode = '200';
        } catch (ModelNotFoundException $exception) {
            $data = [];
            $message = 'user, business or role not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function displayUserRole($id)
    {
        try {
            $statusCode = 200;
            $message = 'Display list editor';
            Business::findOrfail($id);
            $editorId = BusinessRole::where('role_name', 'editor')->get('id');
            $userEditorId = BusinessUserRole::whereIn('business_role_id', $editorId)
                ->where('business_id', $id)
                ->get('user_id');
            $data = User::whereIn('id', $userEditorId)->get()->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'profile_picture' => $item->profile_picture,
                ];
            });
        } catch (ModelNotFoundException $exception) {
            $data = [];
            $message = 'business not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function updateRole(Request $request, $id)
    {
        $data = [];
        $request->validate([
            'role' => 'required'
        ]);
        try {
            $statusCode = 200;
            $message = 'Your role have been updated';
            User::findOrfail($id);

            BusinessRole::findOrfail($request->role);
            BusinessUserRole::where('user_id', $id)->update(['business_role_id' => $request->role]);
        } catch (ModelNotFoundException $exception) {
            $message = 'user not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }

        return apiResponse($data, $message, $statusCode);
    }

    public function deleteRole($id)
    {
        $data = [];
        try {
            $message = 'The role of user have been deleted';
            $statusCode = 200;
            User::findOrfail($id);
            BusinessUserRole::where('user_id', $id)->delete();
        } catch (ModelNotFoundException $exception) {
            $message = 'user not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function displayUserBanned($id)
    {
        $data = [];
        try {
            $message = 'List of blocked Members';
            $statusCode = '200';
            Business::findOrfail($id);
            $data = BusinessBanned::where('business_id', $id)->get()->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->user->name,
                    'user_id' => $item->user->id,
                    'profile_picture' => getMediaUrl($item->user->profile_picture),
                ];
            });
        } catch (ModelNotFoundException $exception) {
            $message = 'business not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function unblockUserBanned(Business $business, $id)
    {
        $data = [];
        $message = 'This user is unblock';
        $statusCode = 200;
        BusinessBanned::where('user_id', $id)->where('business_id', $business->id)->delete();

        return apiResponse($data, $message, $statusCode);
    }

    public function deleteBusiness($id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        Business::where('id', $id)->delete();
        BusinessMedia::where('business_id', $id)->delete();
        BusinessSetting::where('business_id', $id)->delete();
        BusinessNotification::where('business_id', $id)->delete();
        BusinessCommunity::where('business_id', $id)->delete();
        BusinessUserRole::where('business_id', $id)->delete();
        BusinessAlbum::where('business_id', $id)->delete();
        BusinessOpenHours::where('business_id', $id)->delete();

        $message = "Business deleted successfuly";
        return apiResponse($data, $message, $statusCode);
    }

    public function changeUserAccountType(Request $request)
    {
        $data = [];
        try {
            UserAccountPackage::findOrfail($request->package_id);
            $message = 'Business account have been changed';
            $statusCode = 200;

            DB::table('users')
                ->updateOrInsert(
                    ['id' => auth()->user()->id],
                    ['user_account_package_id' => $request->package_id]
                );
        } catch (ModelNotFoundException $exception) {
            $message = 'This type of account does not exist';
            $statusCode = 404;
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $statusCode = 500;
        }
        return apiResponse($data, $message, $statusCode);
    }
}
