<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileControlloller;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\PeopleAroundController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ProfileMediaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyOtpController;
use App\Http\Controllers\BusinessAroundController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Business\BusinessCommunity;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Network\NetworkAlbumController;
use App\Http\Controllers\Network\NetworkMediaController;
use App\Http\Controllers\Business\BusinessInfoController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Business\BusinessAlbumController;
use App\Http\Controllers\Business\BusinessMediaController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\BusinessSearchController;
use App\Http\Controllers\CouncilController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\NeighborhoodController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\Business\BusinessPostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Network\NetworkCommunityController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/user')->group(function () {
    Route::post('/register', [RegisterController::class, 'create']);
    Route::post('/account/verify', [RegisterController::class, 'verifyAccount']);
    Route::post('/verifyOtp/{user}', [VerifyOtpController::class, 'verifyOtp']);
    Route::post('/resendOtp', [RegisterController::class, 'resendOtp']);
    Route::post('/social/{provider}', [SocialAuthController::class, 'callback']);
    Route::post('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/reset', [ResetPasswordController::class, 'passwordResetOtp']);
    Route::post('/verifyResetOtp', [ResetPasswordController::class, 'verifyPasswordResetOtp']);
    Route::post('/resendPasswordOtp', [ResetPasswordController::class, 'resendPasswordOtp']);
    Route::post('/resetpassword/{user}', [ResetPasswordController::class, 'resetPassword']);
    Route::post('/email/sendOtp', [EmailVerificationController::class, 'emailVerification']);
    Route::post('/email/verifyOtp/{user}', [EmailVerificationController::class, 'verifyEmailOtp']);
    Route::post('/email/resendOtp', [EmailVerificationController::class, 'resendEmailOtp']);
    Route::get('/all-user/{keyword?}', [\App\Http\Controllers\UserController::class, 'getUsers']);
    //Route::get('/inbox/{id?}', [\App\Http\Controllers\MessageController::class, 'getUserMessages']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('user/completewelcome', [RegisterController::class, 'welcomeComplete']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::post('/complete/profile', [RegisterController::class, 'completeProfile']);
    Route::get('/business/around', [BusinessAroundController::class, 'getBusinessData']);
    Route::get('/people/around', [PeopleAroundController::class, 'getPeopleData']);
    Route::prefix('business')->group(function () {
        Route::get('info/{businessId}', [BusinessInfoController::class, 'displayInfo'])->middleware('BusinessOwner');
        Route::post('/create', [BusinessController::class, 'businessIdentity']);
        Route::post('/update/{businessId}', [BusinessInfoController::class, 'updateInfo'])->middleware('BusinessOwner');
        Route::post('insights/{business}', [BusinessController::class, 'businessInsight']);
        Route::post('/create/post/{businessId}', [BusinessPostController::class, 'store'])->middleware('BusinessOwner');
        Route::get('/show/post/{businessId}/{page?}', [BusinessPostController::class, 'index'])->middleware('BusinessOwner');
        Route::post('/delete/post/{postId}', [BusinessPostController::class, 'destroy'])->middleware('PostOwner');
        Route::get('userBusiness', [BusinessController::class, 'displayUserBusiness']);
        Route::post('/add', [BusinessController::class, 'addBusiness']);
        Route::post('/create/post/{id}', [PostController::class, 'store']);
        Route::post('/delete/post/{id}', [PostController::class, 'destroy']);
        Route::get('/community/business-follower/{id}/{page?}', [BusinessCommunity::class, 'businessFollower']);
        Route::get('/community/business-following/{id}/{page?}', [BusinessCommunity::class, 'businessFollowing']);
        Route::get('/community/people-following/{id}/{page?}', [BusinessCommunity::class, 'peopleFollowing']);
        Route::get('/community/people-follower/{id}/{page?}', [BusinessCommunity::class, 'peopleFollower']);
        Route::get('/community/network-following/{id}/{page?}', [BusinessCommunity::class, 'networkFollowing']);
        Route::get('/community/network-follower/{id}/{page?}', [BusinessCommunity::class, 'networkFollower']);
        Route::get('/community/total/{id}', [BusinessCommunity::class, 'communityTotal']);
        Route::post('/post-approve', [PostController::class, 'approvePost']);
        Route::get('/edit/{businessId}', [BusinessInfoController::class, 'edit']);
        Route::post('/edit/{businessId}', [BusinessInfoController::class, 'update']);
        Route::post('/update/post/{postId}', [BusinessPostController::class, 'update'])->middleware('PostOwner');
        Route::prefix('album')->group(function () {
            Route::get('/index/{businessId}', [BusinessAlbumController::class, 'index'])->middleware('BusinessOwner');
            Route::get('/show/{businessId}/{albumId}', [BusinessAlbumController::class, 'show']);
            Route::post('/create/{businessId}', [BusinessAlbumController::class, 'store'])->middleware('BusinessOwner');
            Route::post('/update/{businessId}/{albumId}', [BusinessAlbumController::class, 'update'])->middleware('AlbumOwner');
            Route::post('/delete/{businessId}/{albumId}', [BusinessAlbumController::class, 'destroy'])->middleware('AlbumOwner');
        });
        Route::post('/store/media/{businessId}/{albumId}', [BusinessMediaController::class, 'store'])->middleware('AlbumOwner');
        Route::group(['middleware' => ['MediaOwner']], function () {
            Route::post('/delete/media/{businessId}/{mediaId}', [BusinessMediaController::class, 'destroyPicAlbum']);
            Route::post('/make/logopic/{businessId}/{mediaId}', [BusinessMediaController::class, 'makeLogoPic']);
            Route::post('/make/coverpic/{businessId}/{mediaId}', [BusinessMediaController::class, 'makeCoverPic']);
            Route::get('/download/media/{businessId}/{mediaId}', [BusinessMediaController::class, 'downloadMedia']);
        });
        Route::get('/post/media/{businessId}', [BusinessAlbumController::class, 'showPostMedia'])->middleware('BusinessOwner');
        Route::post('upload/cover/{businessId}', [BusinessInfoController::class, 'uploadCover'])->middleware('BusinessOwner');
        Route::post('upload/logo/{businessId}', [BusinessInfoController::class, 'uploadLogo'])->middleware('BusinessOwner');
        Route::prefix('role')->group(function () {
            Route::get('/displayFollowing', [BusinessController::class, 'displayFollowing']);
            Route::get('/displayRole', [BusinessController::class, 'displayRole']);
            Route::post('/assignRole/{business}', [BusinessController::class, 'assignRole']);
            Route::get('/editor/{business}', [BusinessController::class, 'displayUserRole']);
            Route::post('/update/{id}', [BusinessController::class, 'updateRole']);
            Route::delete('/delete/{id}', [BusinessController::class, 'deleteRole']);
        });
        Route::post('businessInfo/update/{id}', [BusinessInfoController::class, 'businessInfoSetting']);
        Route::post('general/update/{id}', [BusinessInfoController::class, 'updateGeneralInfo']);
        Route::get('blocking/{business}', [BusinessController::class, 'displayUserBanned']);
        Route::delete('unblocking/{business}/{user_id}', [BusinessController::class, 'unblockUserBanned']);
        Route::delete('delete/{businessId}', [BusinessController::class, 'deleteBusiness'])->middleware('BusinessOwner');
        Route::post('businessAccount/update', [BusinessController::class, 'changeUserAccountType']);
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::put('editPost/{id}', [PostController::class, 'updatePost']);
    Route::delete('deletePost/{id}', [PostController::class, 'deletePost']);
    Route::get('editComment', [PostController::class, 'editComment']);
    Route::delete('deleteComment/{id}', [PostController::class, 'deleteComment']);
});

Route::group(['prefix' => 'users', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/', [UserController::class, 'allusers']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::resource('network', NetworkController::class);
    Route::prefix('network')->group(function () {
        Route::get('/', [\App\Http\Controllers\NetworkController::class, 'allNetworks']);
        Route::get('business/{id}', [\App\Http\Controllers\NetworkController::class, 'businessNetwork']);
        Route::post('assign/{id}/categories', [\App\Http\Controllers\NetworkController::class, 'assignNetworkCategories']);
        /*Network Approval / Unapprove */
        Route::patch('approve/{id}', [\App\Http\Controllers\NetworkController::class, 'approveNetworks']);
        Route::get('user/unapprove', [\App\Http\Controllers\NetworkController::class, 'userUnapproveNetworks']);
        Route::get('business/unapprove/{id}', [\App\Http\Controllers\NetworkController::class, 'businessUnapproveNetworks']);
        Route::get('unapprove', [\App\Http\Controllers\NetworkController::class, 'unapproveNetworks']);
        /* Network Members */
        Route::post('{id}/members', [\App\Http\Controllers\NetworkController::class, 'addNetworkMember']);
        Route::post('{id}/members/{business_id}/business', [\App\Http\Controllers\NetworkController::class, 'addNetworkBusinessMember']);
        Route::patch('{id}/members/{user_id}/approve', [\App\Http\Controllers\NetworkController::class, 'approveNetworkMember']);
        Route::patch('{id}/members/business/{business_id}/approve', [\App\Http\Controllers\NetworkController::class, 'approveNetworkBusinessMember']);
        Route::get('{id}/members/unapprove', [\App\Http\Controllers\NetworkController::class, 'unapproveNetworkMembers']);
        Route::get('{id}/business/members/unapprove', [\App\Http\Controllers\NetworkController::class, 'unapproveNetworkBusinessMembers']);
        Route::get('{id}/members', [\App\Http\Controllers\NetworkController::class, 'networkMembersList']);
        Route::post('{id}/moderator/{user_id}', [\App\Http\Controllers\NetworkController::class, 'addNetworkModerator']);
        Route::get('{id}/moderators', [\App\Http\Controllers\NetworkController::class, 'networkModeratorList']);
        /* Network Categories */
        Route::get('categories/list', [\App\Http\Controllers\NetworkController::class, 'networkCategoryList']);
        Route::post('categories/create', [\App\Http\Controllers\NetworkController::class, 'addNetworkCategory']);

        /* network community */
        Route::get('community/people/{id}', [NetworkCommunityController::class, 'peopleCommunity']);
        Route::get('community/businesses/{id}', [NetworkCommunityController::class, 'businessCommunity']);

        /* network create post section */
        Route::post('post/create/{networkId}', [\App\Http\Controllers\Network\PostController::class, 'store']);
        Route::get('show/post/{id}/{page?}', [\App\Http\Controllers\Network\PostController::class, 'show']);
        Route::get('edit/post/{id}', [\App\Http\Controllers\Network\PostController::class, 'getPostUpdate']);
        Route::post('edit/post/{id}', [\App\Http\Controllers\Network\PostController::class, 'update']);
        Route::post('delete/post/{id}', [\App\Http\Controllers\Network\PostController::class, 'destroy']);

        /* network album section */
        Route::get('album/index/{networkId}', [NetworkAlbumController::class, 'index']);
        Route::get('album/show/{networkId}/{albumId}', [NetworkAlbumController::class, 'show']);
        Route::post('album/create/{networkId}', [NetworkAlbumController::class, 'store']);
        Route::post('album/update/{networkId}/{albumId}', [NetworkAlbumController::class, 'update']);
        Route::delete('album/delete/{networkId}/{albumId}', [NetworkAlbumController::class, 'destroy']);
        Route::get('post/media/{networkId}', [NetworkAlbumController::class, 'showPostMedia']);

        /* network media section */
        Route::post('store/media/{businessId}/{albumId}', [NetworkMediaController::class, 'store']);
        Route::get('download/media/{businessId}/{mediaId}', [NetworkMediaController::class, 'downloadMedia']);
        Route::post('make/profile/{networkId}/{mediaId}', [NetworkMediaController::class, 'makeProfilePic']);
        Route::post('network/delete/media/{networkId}/{mediaId}', [NetworkMediaController::class, 'destroyPicAlbum']);
        Route::get('posts/approve/{id}/{user_id}/{post_id}', [\App\Http\Controllers\NetworkController::class, 'approveNetworkPost']);
        Route::get('posts/decline/{id}/{user_id}/{post_id}', [\App\Http\Controllers\NetworkController::class, 'declineNetworkPost']);
        Route::get('show/posts/pending/{id}', [\App\Http\Controllers\NetworkController::class, 'showNetworkPendingPost']);

        Route::get('{id}/business/members', [\App\Http\Controllers\NetworkController::class, 'networkBusinessMembersList']);
        Route::post('{id}/editor/{user_id}', [\App\Http\Controllers\NetworkController::class, 'addNetworkEditor']);
        Route::get('{id}/editors', [\App\Http\Controllers\NetworkController::class, 'networkEditorList']);
        Route::post('{id}/moderator/{user_id}', [\App\Http\Controllers\NetworkController::class, 'addNetworkModerator']);
        Route::get('{id}/moderators', [\App\Http\Controllers\NetworkController::class, 'networkModeratorList']);
        /* Network Search */
        Route::post('search', [\App\Http\Controllers\NetworkController::class, 'networkSearch']);

        //kevin's tasks 13/09/2021
        Route::get('posts/approve/{id}/{user_id}/{post_id}', [\App\Http\Controllers\NetworkController::class, 'approveNetworkPost']);
        Route::get('posts/decline/{id}/{user_id}/{post_id}', [\App\Http\Controllers\NetworkController::class, 'declineNetworkPost']);
        Route::get('show/posts/pending/{id}', [\App\Http\Controllers\NetworkController::class, 'showNetworkPendingPost']);

        //Network roles and settings//
        Route::GET('users/blocked/{id}', [\App\Http\Controllers\NetworkController::class, 'getBannedUsers']);
        Route::DELETE('users/unblocked/{networkId}/{userId}', [\App\Http\Controllers\NetworkController::class, 'unbanUser']);
        Route::GET('roles', [\App\Http\Controllers\NetworkController::class, 'displayRole']);
        Route::POST('roles/{id}/assign', [\App\Http\Controllers\NetworkController::class, 'assignRole']);
        Route::PUT('roles/{id}', [\App\Http\Controllers\NetworkController::class, 'updateRole']);
        Route::POST('general-settings/{networkId}', [\App\Http\Controllers\NetworkController::class, 'generalSettings']);
        Route::DELETE('roles/{id}', [\App\Http\Controllers\NetworkController::class, 'deleteEditor']);
        Route::DELETE('delete/{id}', [\App\Http\Controllers\NetworkController::class, 'destroy']);
        Route::POST('update/{network}', [\App\Http\Controllers\NetworkController::class, 'update']);
        Route::GET('info/{id}', [\App\Http\Controllers\NetworkController::class, 'show']);
        Route::GET('list/member/{id}', [\App\Http\Controllers\NetworkController::class, 'diplayNetworkMember']);
    });
});

Route::group(['prefix' => 'search', 'middleware' => ['auth:sanctum']], function () {
    Route::get('business', [SearchController::class, 'businessSearch']);
    Route::get('user', [SearchController::class, 'userSearch']);
    Route::get('network', [SearchController::class, 'networkSearch']);
    Route::get('market', [SearchController::class, 'marketSearch']);
    Route::get('post', [SearchController::class, 'postSearch']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('profile')->group(function () {
        //profile media
        Route::post('/album/create', [AlbumController::class, 'createAlbum']);
        Route::post('/album/edit/{id}', [AlbumController::class, 'editAlbum']);
        Route::delete('/album/delete/{id}', [AlbumController::class, 'deleteAlbum']);
        Route::get('/album/show', [AlbumController::class, 'getAlbum']);
        Route::get('/album/pictures/{id}', [AlbumController::class, 'showPicture']);
        Route::get('/album/post/{id}', [AlbumController::class, 'postAlbum']);
        Route::get('/post/media', [AlbumController::class, 'postDetail']);
        Route::post('/upload/{id}', [ProfileMediaController::class, 'store']);
        Route::post('/makeProfile/picture/{id}', [ProfileMediaController::class, 'makeProfilePic']);
        Route::post('/makeCover/picture/{id}', [ProfileMediaController::class, 'makeCoverPic']);
        Route::delete('/image/{id}', [ProfileMediaController::class, 'destroyPicAlbum']);
        Route::post('/downloadMedia/{mediaId}', [ProfileMediaController::class, 'downloadMedia']);
        Route::post('businessInfo/update/{id}', [BusinessInfoController::class, 'businessInfoSetting']);
        Route::post('general/update/{id}', [BusinessInfoController::class, 'updateGeneralInfo']);
        Route::get('blocking/{business}', [BusinessController::class, 'displayUserBanned']);
        Route::delete('unblocking/{business}/{user_id}', [BusinessController::class, 'unblockUserBanned']);
        Route::delete('/delete/{businessId}', [BusinessController::class, 'deleteBusiness']);
        Route::post('businessAccount/update', [BusinessController::class, 'changeUserAccountType']);
        //profile dashboard
        Route::get('popular/network/{page?}', [\App\Http\Controllers\ProfileController::class, 'detailNetwork']);
        Route::get('hot/business/{page?}', [\App\Http\Controllers\ProfileController::class, 'detailBusiness']);
        Route::get('businessInfo/{id}', [\App\Http\Controllers\ProfileController::class, 'businessInfo']);
        Route::get('total/community', [\App\Http\Controllers\ProfileController::class, 'communityTotal']);
        Route::get('dashboard/post/business/{id}/{page?}', [\App\Http\Controllers\PostController::class, 'dashboardPostBusiness']);
        Route::get('dashboard/post/user/{page?}', [\App\Http\Controllers\PostController::class, 'dashboardPostUser']);
        Route::get('user/follower/{page?}', [\App\Http\Controllers\ProfileController::class, 'poepleFollower']);
        Route::get('user/following/{page?}', [\App\Http\Controllers\ProfileController::class, 'poepleFollowing']);
        Route::get('business/follower/{page?}', [\App\Http\Controllers\ProfileController::class, 'businessFollower']);
        Route::get('business/following/{page?}', [\App\Http\Controllers\ProfileController::class, 'businessFollowing']);
        Route::get('network/follower/{page?}', [\App\Http\Controllers\ProfileController::class, 'networkFollower']);
        Route::get('network/following/{page?}', [\App\Http\Controllers\ProfileController::class, 'networkFollowing']);
        Route::get('user/info', [\App\Http\Controllers\ProfileController::class, 'profileInfo']);
        Route::get('business/info/{id}', [\App\Http\Controllers\ProfileController::class, 'businessInfo']);
        // User profile
        Route::get('user/businesses', [\App\Http\Controllers\ProfileController::class, 'userBusiness']);
        Route::get('user/networks', [\App\Http\Controllers\ProfileController::class, 'userNetwork']);
        Route::get('user/media', [\App\Http\Controllers\ProfileController::class, 'media']);
    });
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::resource('market', MarketController::class);
    Route::prefix('market')->group(function () {
        Route::get('products/{id}', [\App\Http\Controllers\MarketController::class, 'businessProducts']);
        Route::get('business/categories/{id}', [\App\Http\Controllers\MarketController::class, 'businessCategoryList']);
        Route::get('search', [\App\Http\Controllers\MarketController::class, 'searchMarket']);
    });
    Route::prefix('category')->group(function () {
        Route::get('/', [\App\Http\Controllers\MarketController::class, 'categoriesList']);
        Route::post('/', [\App\Http\Controllers\MarketController::class, 'addCategory']);
        Route::put('/{id}', [\App\Http\Controllers\MarketController::class, 'editCategory']);
        Route::delete('/{id}', [\App\Http\Controllers\MarketController::class, 'deleteCategory']);
    });
    Route::prefix('subcategory')->group(function () {
        Route::get('/{parent_category_id}', [\App\Http\Controllers\MarketController::class, 'subCategoriesList']);
        Route::post('/', [\App\Http\Controllers\MarketController::class, 'addSubCategory']);
        Route::put('/{id}', [\App\Http\Controllers\MarketController::class, 'editSubCategory']);
        Route::delete('/{id}', [\App\Http\Controllers\MarketController::class, 'deleteSubCategory']);
    });
    Route::prefix('filters')->group(function () {
        Route::post('/', [\App\Http\Controllers\MarketController::class, 'addCategoriesFilter']);
        Route::get('category/{id}', [\App\Http\Controllers\MarketController::class, 'categoriesFilter']);
        Route::get('subcategory/{id}', [\App\Http\Controllers\MarketController::class, 'subCategoriesFilter']);
        Route::put('category/{id}', [\App\Http\Controllers\MarketController::class, 'editCategoryFilters']);
        Route::put('subcategory/{id}', [\App\Http\Controllers\MarketController::class, 'editSubCategoryFilters']);
        Route::delete('/{id}', [\App\Http\Controllers\MarketController::class, 'delCategoriesFilter']);
    }); 
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::resource('notification', NotificationController::class);
    Route::post('notification/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAsReadAll']);
    Route::post('notification/deleteAll', [\App\Http\Controllers\NotificationController::class, 'deleteMultipleNotifications']);
});

Route::group(['prefix' => 'business-community', 'middleware' => ['auth:sanctum']], function () {
    Route::get('/people-follower/{id}/{keyword?}', [\App\Http\Controllers\BusinessCommunityController::class, 'peopleFollower']);
    Route::get('/business-follower/{id}/{keyword?}', [\App\Http\Controllers\BusinessCommunityController::class, 'businessFollower']);
    Route::get('/people-following/{id}/{keyword?}', [\App\Http\Controllers\BusinessCommunityController::class, 'peopleFollowing']);
    Route::get('/business-following/{id}/{keyword?}', [\App\Http\Controllers\BusinessCommunityController::class, 'businessFollowing']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('/download', [DownloadController::class, 'download']);
    Route::post('/upload', [UploadController::class, 'upload']);
    Route::get('/userIntro', [UserController::class, 'userIntro']);
    Route::get('/userIntro/biography', [UserController::class, 'indexBiography']);
    Route::post('user/upload/profile-picture',[ProfileMediaController::class,'uploadProfilePicture']);
    Route::post('user/upload-cover',[ProfileMediaController::class,'uploadeCoverPicture']);
    Route::post('/user/contact-create',[UserContactController::class,'store']);
    Route::post('/user/contact-update/{id}',[UserContactController::class,'update']);
    Route::post('/user/contact-delete/{id}',[UserContactController::class,'destroy']);
    Route::POST('/userIntro', [UserController::class, 'updateUserIntro']);
    Route::POST('/userIntro/biography', [UserController::class, 'updateBiography']);
    Route::POST('/userIntro/dob', [UserController::class, 'updateBirth']);
    Route::POST('/userIntro/gender', [UserController::class, 'updateGender']);
    Route::POST('/userIntro/addPhone', [UserController::class, 'updatePhoneNumber']);
    Route::POST('/userIntro/addCurrentCity/{id}', [UserController::class, 'updateCurrentCity']);
    Route::POST('/userIntro/storeWebLink', [UserController::class, 'storeWebSite']);
    Route::POST('/userIntro/updateWebLink/{id}', [UserController::class, 'updateWebSite']);
    Route::DELETE('/userIntro/deleteWebLink/{id}', [UserController::class, 'deleteWebSite']);
    Route::POST('/userIntro/storeSocialLink', [UserController::class, 'storeSocialLink']);
    Route::POST('/userIntro/addWorking', [UserController::class, 'storeWorking']);
    Route::POST('/userIntro/updateWorking/{id}', [UserController::class, 'updateWorking']);
    Route::DELETE('/userIntro/deleteWorking/{id}', [UserController::class, 'deleteWorking']);
    Route::POST('/userIntro/addSchool', [UserController::class, 'storeSchool']);
    Route::POST('/userIntro/updateSchool/{id}', [UserController::class, 'updateSchool']);
    Route::DELETE('/userIntro/deleteSchool/{id}', [UserController::class, 'deleteSchool']);
    Route::POST('/userIntro/addProfession', [UserController::class, 'updateProfession']);
    Route::POST('/userIntro/addHomeTown', [UserController::class, 'updateHomeTown']);

    Route::GET('/user/post/{page?}', [PostController::class, 'index']);
    Route::GET('/show/{id}', [PostController::class, 'show']);
    Route::POST('/user/post', [PostController::class, 'store']);
    Route::post('/user/post/update/{id}', [PostController::class, 'update']);
    Route::DELETE('/user/post/delete/{id}', [PostController::class, 'destroy']);
    Route::delete('/user/cover',[ProfileMediaController::class,'deleteCover']);
    Route::get('/user/neigborhood',[NeighborhoodController::class,'userNeigborhood']);
});
Route::get('/category', [CategoryController::class, 'show']);
Route::get('/subcategory', [SubCategoryController::class, 'show']);
Route::get('/filter', [FilterController::class, 'show']);
Route::get('/params', [BusinessSearchController::class, 'displaySearchParam']);
Route::post('/search/keyword', [BusinessSearchController::class, 'searchKeyword']);
Route::get('/business/search/index', [BusinessSearchController::class, 'displaySearch']);
Route::post('/search/{page?}', [BusinessSearchController::class, 'search']);
Route::post('/catergory/subcategory', [SubCategoryController::class, 'subCategoriesList']);


Route::get('/countries', [CountryController::class, 'show']);
Route::get('/regions', [RegionController::class, 'show']);
Route::post('/regions', [RegionController::class, 'showRegionFromCountry']);
Route::get('/divisions', [DivisionController::class, 'show']);
Route::post('/divisions', [DivisionController::class, 'showDivisionFromRegion']);
Route::get('/councils', [CouncilController::class, 'show']);
Route::post('/councils', [CouncilController::class, 'showCouncilFromDivision']);
Route::get('/neighborhoods', [NeighborhoodController::class, 'show']);
Route::post('/neighborhood/show', [NeighborhoodController::class, 'showNeighborhoodFromCouncil']);
Route::post('/neighborhoods', [NeighborhoodController::class, 'showNeighborhoodFromCouncil']);

Route::group(['prefix' => 'search', 'middleware' => ['auth:sanctum']], function () {
    Route::GET('/listUsers/{q?}', [PeopleController::class, 'listUsers']);
    Route::GET('/listProfessions/{q?}', [PeopleController::class, 'listUserProfession']);
    Route::POST('/community/profession', [PeopleController::class, 'listProfessionByCommunity']);
});

/*Messages Route */
Route::group(['prefix' => 'messages', 'middleware' => ['auth:sanctum']], function () {
    /* For User */
    Route::get('/{id}', [MessageController::class, 'getUserMessages']);
    Route::get('/business/{id}', [MessageController::class, 'getUserBusinessMessages']);
    Route::get('/network/{id}', [MessageController::class, 'getUserNetworkMessages']);
    /* For Business */
    Route::get('{id}/business/{businessID}', [MessageController::class, 'getBusinessMessages']);
    Route::get('{id}/business/{userID}/user', [MessageController::class, 'getBusinessUserMessages']);
    Route::get('{id}/business/{networkID}/network', [MessageController::class, 'getBusinessNetworkMessages']);
    /* For Network */
    Route::get('{id}/network/{networkID}', [MessageController::class, 'getNetworkMessages']);
    Route::get('{id}/network/{userID}/user', [MessageController::class, 'getNetworkUserMessages']);
    Route::get('{id}/network/{businessID}/business', [MessageController::class, 'getNetworkBusinessMessages']);
    /*Save Messages */
    /* For User */
    Route::post('', [MessageController::class, 'storeUserToBusinessMessages']);
    Route::post('', [MessageController::class, 'storeUserToNetworkMessages']);
    Route::post('', [MessageController::class, 'storeBusinesstoBusinessMessages']);
    /* For Business */
    Route::post('', [MessageController::class, 'storeBusinesstoUserMessages']);
    Route::post('', [MessageController::class, 'storeBusinesstoNetworkMessages']);
    Route::post('', [MessageController::class, 'storeNetworktoNetworkMessages']);
    /* For Network */
    Route::post('', [MessageController::class, 'storeNetworktoNetworkMessages']);
    Route::post('', [MessageController::class, 'storeNetworktoUserMessages']);
    Route::post('', [MessageController::class, 'storeNetworktoBusinessMessages']);
    
    Route::get('users', [UserController::class, 'allUsers']);
});