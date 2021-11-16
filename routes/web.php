<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('migrate-posts-comments-data', 'UserController@fixPostsCommentsData'); // incorrect
Route::get('migrate-users-profile-data', 'UserController@fixUsersProfileData');
Route::get('migrate-posts-comments-likes-data', 'UserController@migratePostsCommentsLikesData');
Route::get('migrate-followers-data', 'UserController@migrateFollowersData');
Route::get('migrate-notifications-data', 'UserController@migrateNotificationsData');
Route::get('migrate-groups-data-to-networks', 'UserController@migrateGroupsDataToNetworks');
Route::get('migrate-groups-members-to-network-members', 'UserController@migrateGroupsMembersToNetworkMembers');
Route::get('migrate-messages-to-messages', 'UserController@migrateMessagesToMessages');
Route::get('migrate-messages-recipients-to-message-recipients', 'UserController@migrateMessagesRecipientsToMessageRecipients');