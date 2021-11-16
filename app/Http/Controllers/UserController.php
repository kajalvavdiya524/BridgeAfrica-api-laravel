<?php

namespace App\Http\Controllers;
use Exception;
use App\Post;
use App\User;
use App\Message;
use App\Network;
use App\PostLike;
use Carbon\Carbon;
use App\PostComment;
use App\Notification;
use App\UserFollower;
use App\NetworkMember;
use App\UserAddress;
use App\UserContact;
use App\UserWebsite;
use App\UserEducation;
use App\PrivacySetting;
use App\MessageRecipients;
use App\UserSocialProfile;
use App\UserWorkExperience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Traits\Transformer;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{

    public function migrateMessagesRecipientsToMessageRecipients()
    {
        $wp_messages = DB::connection(config('main.connections.maxinemo'))->table('wp_bp_messages_recipients')->select('*')->get();
        $messages = $wp_messages->map(function ($message_row) {
            return [
                'id'            => $message_row->id,
                'user_id'       => $message_row->user_id,
                'thread_id'     => $message_row->thread_id,
                'unread_count'  => $message_row->unread_count,
                'sender_only'   => $message_row->sender_only,
                'is_deleted'    => $message_row->is_deleted
            ];
        });
        MessageRecipients::truncate();
        (collect($messages)->chunk(4000))->each(function ($messages_chunk) {
            MessageRecipients::insert($messages_chunk->toArray());
        });
        dd('MAXINEMO_MOOJA.wp_bp_messages_recipients => BRIDGE_AFRICA.message_recipients *** (MESSAGES RECIPIENTS DATA MIGRATED TO MESSAGES RECIPIENTS COMPLETELY)');
    }

    public function migrateMessagesToMessages()
    {
        $wp_messages = DB::connection(config('main.connections.maxinemo'))->table('wp_bp_messages_messages')->select('*')->get();
        $messages = $wp_messages->map(function ($message_row) {
            return [
                'id'            => $message_row->id,
                'thread_id'     => $message_row->thread_id,
                'sender_id'     => $message_row->sender_id,
                'network_id'    => $message_row->group_id,
                'subject'       => $message_row->subject,
                'message'       => $message_row->message,
                'date_sent'     => Carbon::parse($message_row->date_sent)
            ];
        });
        Message::truncate();
        (collect($messages)->chunk(600))->each(function ($messages_chunk) {
            Message::insert($messages_chunk->toArray());
        });
        dd('MAXINEMO_MOOJA.wp_bp_messages_messages => BRIDGE_AFRICA.messages *** (MESSAGES DATA MIGRATED TO MESSAGES COMPLETELY)');
    }

    public function migrateGroupsMembersToNetworkMembers()
    {
        $wp_groups_members = DB::connection(config('main.connections.maxinemo'))->table('wp_bp_groups_members')->select('*')->get();
        $groups_members = $wp_groups_members->map(function ($group_member_row) {
            return [
                'id'            => $group_member_row->id,
                'user_id'       => $group_member_row->user_id,
                'network_id'    => $group_member_row->group_id,
                // 'inviter_id'    => $group_member_row->inviter_id,
                'created_at'    => Carbon::parse($group_member_row->date_modified),
                'updated_at'    => Carbon::parse($group_member_row->date_modified),
            ];
        });
        NetworkMember::truncate();
        (collect($groups_members)->chunk(1750))->each(function ($groups_members_chunk) {
            NetworkMember::insert($groups_members_chunk->toArray());
        });
        dd('MAXINEMO_MOOJA.wp_bp_groups_members => BRIDGE_AFRICA.network_members *** (GROUPS MEMBERS DATA MIGRATED TO NETWORK REQUESTS COMPLETELY)');
    }

    public function migrateGroupsDataToNetworks()
    {
        $wp_groups = DB::connection(config('main.connections.maxinemo'))->table('wp_bp_groups')->select('*')->get();
        $groups = $wp_groups->map(function ($group_row) {
            return [
                'id'            => $group_row->id,
                'user_id'       => $group_row->creator_id,
                'business_id'   => 0,
                'name'          => $group_row->name,
                'description'   => $group_row->description,
                'purpose'       => '',
                'special_needs' => '',
                'created_at'    => Carbon::parse($group_row->date_created),
                'updated_at'    => Carbon::parse($group_row->date_created),
            ];
        });
        Network::truncate();
        (collect($groups)->chunk(1000))->each(function ($groups_chunk) {
            Network::insert($groups_chunk->toArray());
        });
        dd('MAXINEMO_MOOJA.wp_bp_groups => BRIDGE_AFRICA.networks *** (GROUPS DATA MIGRATED TO NETWORKS COMPLETELY)');
    }

    public function migrateNotificationsData()
    {
        $web_user_notifications_data = DB::connection(config('main.connections.maxinemo'))->table('web_user_notifications')->select('*')->get();
        $notifications = $web_user_notifications_data->map(function ($notification) {
            return [
                'id'                => $notification->id,
                'notification_text' => $notification->notification,
                'reference_id'      => 0,
                'reference_type'    => '',
                'created_at'        => Carbon::parse($notification->created_time),
                'updated_at'        => Carbon::parse($notification->modified_time),
            ];
        });
        Notification::truncate();
        (collect($notifications)->chunk(1000))->each(function ($notifications_chunk) {
            Notification::insert($notifications_chunk->toArray());
        });
        dd('MAXINEMO_MOOJA.web_user_notifications => BRIDGE_AFRICA.notifications *** (NOTIFICATION DATA MIGRATED COMPLETELY)');
    }

    public function migratePostsCommentsLikesData()
    {
        $wp_bp_activity_data = DB::connection(config('main.connections.maxinemo'))->table('wp_bp_activity')->select('*')->get();
        $posts_data = [];
        $comments_data = [];
        $likes_data = [];
        $wp_bp_activity_data->each(function ($activity) use (&$posts_data, &$comments_data, &$likes_data) {
            if ($activity->type === 'activity_update') {
                $posts_data[] = [
                    'id'            => $activity->id,
                    'user_id'       => $activity->user_id,
                    'content'       => $activity->content,
                    'type'          => $activity->type,
                    'created_at'    => $activity->date_recorded,
                    'updated_at'    => $activity->date_recorded,
                ];
            } else if ($activity->type === 'activity_comment') {
                $comments_data[] = [
                    'id'            => $activity->id,
                    'post_id'       => $activity->item_id,
                    'user_id'       => $activity->user_id,
                    'comment'       => $activity->content,
                    'created_at'    => $activity->date_recorded,
                    'updated_at'    => $activity->date_recorded,
                ];
            } else if ($activity->type === 'activity_liked') {
                $likes_data[] = [
                    'id'            => $activity->id,
                    'post_id'       => $activity->item_id,
                    'user_id'       => $activity->user_id,
                    'created_at'    => $activity->date_recorded,
                    'updated_at'    => $activity->date_recorded,
                ];
            }
        });
        Post::truncate();
        Post::insert($posts_data);
        PostLike::truncate();
        PostLike::insert($likes_data);
        PostComment::truncate();
        PostComment::insert($comments_data);
        dd('MAXINEMO_MOOJA.wp_bp_activity => (BRIDGE_AFRICA.posts | BRIDGE_AFRICA.post_comments | BRIDGE_AFRICA.post_likes) *** (POST, COMMENTS, LIKE DATA MIGRATED COMPLETELY)');
    }

    public function migrateFollowersData()
    {
        $wp_bp_followers_data = DB::connection(config('main.connections.maxinemo'))
            ->table('wp_bp_follow')
            ->select('*')
            ->get();
        $followers_data = $wp_bp_followers_data->map(function ($follower) {
            return [
                'id' => $follower->id,
                'user_id' => $follower->leader_id,
                'follower_id' => $follower->follower_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        });
        UserFollower::truncate();
        (collect($followers_data)->chunk(5000))->each(function ($followers_chunk) {
            UserFollower::insert($followers_chunk->toArray());
        });
        dd('MAXINEMO_MOOJA.wp_bp_follow => BRIDGE_AFRICA.user_followers *** (FOLLOWERS DATA MIGRATED COMPLETELY)');
    }

    public function fixUsersProfileData()
    {
        $all_users = [];
        collect(DB::connection(config('main.connections.maxinemo'))->table('wp_users')->select('id')->get()->pluck('id')->all())->each(function ($user_id) use (&$all_users) {
            $wp_users = DB::connection(config('main.connections.maxinemo'))
                ->table('wp_users')
                ->leftjoin('wp_bp_xprofile_data', 'wp_bp_xprofile_data.user_id', '=', 'wp_users.id')
                ->leftjoin('wp_bp_xprofile_fields', 'wp_bp_xprofile_fields.id', '=', 'wp_bp_xprofile_data.field_id')
                ->select('wp_users.*', 'wp_bp_xprofile_fields.name', 'wp_bp_xprofile_data.value')
                ->groupBy('wp_bp_xprofile_data.field_id')
                ->where('wp_users.id', '=', $user_id)
                ->get();
            $user = [];
            $wp_users->each(function ($user_row) use (&$user) {
                $user["id"] = $user_row->ID;
                $user["name"] = $user_row->display_name;
                $user["email"] = $user_row->user_email;
                $user["password"] = $user_row->user_pass;
                $user["status"] = $user_row->user_status;
                $user["created_at"] = Carbon::parse($user_row->user_registered);
                $user["updated_at"] = Carbon::parse($user_row->user_registered);
                if ($user_row->name === 'Full Name')
                    $user['name'] = $user_row->value;
                if ($user_row->name === 'Birthday') {
                    $dob = check_valid_date($user_row->value);
                    if ($dob !== false)
                        $user['dob'] = $dob;
                }
                if ($user_row->name === 'I am a') {
                    $gender = $user_row->value;
                    if ($gender !== '') {
                        if (is_serialized($gender)) {
                            $gender = unserialize($user_row->value);
                            if (is_array($gender) && count($gender) > 0)
                                $user['gender'] = $gender[0];
                        } else {
                            $user['gender'] = $gender;
                        }
                    }
                }
                if ($user_row->name === 'Language')
                    $user['language'] = $user_row->value;
                if ($user_row->name === 'Phone')
                    $user['phone'] = $user_row->value;
                if (!isset($user["phone"]))
                    $user["phone"] = "";
                if (!isset($user["dob"]))
                    $user["dob"] = NULL;
                if (!isset($user["gender"]))
                    $user["gender"] = "";
                if (!isset($user["language"]))
                    $user["language"] = "";
                if (!isset($user["verification_token"]))
                    $user["verification_token"] = "";
                if (!isset($user["verified_at"]))
                    $user["verified_at"] = NULL;
                if (!isset($user["profile_picture"]))
                    $user["profile_picture"] = "";
                // column still missing from update
                // "verification_token" => "",
                // "verified_at" => NULL,
                // "profile_picture" => "",
            });
            $all_users[] = $user;
        });
        User::truncate();
        (collect($all_users)->chunk(2500))->each(function ($users_chunk) {
            User::insert($users_chunk->toArray());
        });
        dd('MAXINEMO_MOOJA.wp_users => BRIDGE_AFRICA.users *** (USERS DATA MIGRATED COMPLETELY)');
    }


    public function getUsers($keyword = '')
    {
        if (isset($keyword)) {
            $data = User::select('id', 'name')
                ->where('name', 'like', '%' . $keyword . '%')->get();
            $message = 'Search Term "' . $keyword . '" Listing Successfully.';
        } else {
            $data = User::select('id', 'name')->get();
            $message = 'Users Listing Successfully.';
        }
        $message = 'Users Listing Successfully.';

        return apiResponse($data, $message, 200);
    }

    public function userIntro(User $user, Request $request)
    {
        $id = auth()->user()->id;
        if($request->id) {
            $id = $request->id;
        }
        $user = User::find($id);
        $data['user'] = Transformer::user($user);
        $data['user_experience'] = $user->userWorkExperiences;
        $data['user_websites'] = $user->userWebsites;
        $data['user_address'] = $user->userAddresses;
        $data['user_education'] = $user->education;
        $data['user_contact'] = $user->userContacts;

        $message = 'Listing of user Intro';

        return apiResponse($data, $message, 200);
    }

    public function updateBiography(Request $request)
    {
        $request->validate([
            'biography' => 'required',
            'value' => ['required', 'string', 'max:10', 'in:public,private']
        ]);

        $key = 'biography';
        $value = $request->value;

        $biography = User::where('id', auth()->user()->id)->update(['biography' => $request->biography]);

        DB::table('privacy_settings')
            ->updateOrInsert(
                ['user_id' => auth()->user()->id, 'key' => $key],
                ['value' => $value]
            );

        $message = 'Your biography have been edited';

        return apiResponse([], $message, 200);
    }

    public function updateUserIntro(Request $request)
    {
        $request->validate([
            'companyName' => 'required',
            'cityTown' => 'required',
            'address' => 'required',
            'schoolName' => 'required',
            'value' => ['required', 'string', 'max:10', 'in:public,private']
        ]);

        $key = 'biography';

        $company = $request->companyName;
        $city = $request->cityTown;
        $homeTown = $request->address;
        $studiedAt = $request->schoolName;
        $value = $request->value;

        DB::table('privacy_settings')
            ->updateOrInsert(
                ['user_id' => auth()->user()->id, 'key' => $key],
                ['value' => $value]
            );

        UserWorkExperience::where('user_id', auth()->user()->id)->update(['company_name' => $company, 'city_town' => $city]);
        UserEducation::where('user_id', auth()->user()->id)->update(['school_name' => $studiedAt]);
        UserAddress::where('user_id', auth()->user()->id)->update(['address' => $homeTown]);

        $data = [
            'company_name' => $company,
            'city_town' => $city,
            'address' => $homeTown,
            'studiedAt' => $studiedAt
        ];

        $message = 'Your Intro have been edited';
        return apiResponse($data, $message, 200);
    }

    public function indexBiography()
    {
        $data = User::where('id', auth()->user()->id)->pluck('biography');

        $message = 'Display your biography';
        return apiResponse($data, $message, 200);
    }

    public function updateBirth(Request $request)
    {
        $request->validate([
            'dob' => ['required', 'date']
        ]);

        $birth = User::where('id', auth()->user()->id)->update(['dob' => $request->dob]);

        $message = 'Date of birthday have been edited';
        return apiResponse([], $message, 200);
    }

    public function updateGender(Request $request)
    {
        $request->validate([
            'gender' => ['required', 'string', 'max:10', 'in:male,female,other']
        ]);

        $gender = User::where('id', auth()->user()->id)->update(['gender' => $request->gender]);

        $message = 'Gender have been edited';

        return apiResponse([], $message, 200);
    }


    public function updatePhoneNumber(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required|numeric'
        ]);

        DB::table('user_contacts')
            ->updateOrInsert(
                ['user_id' => auth()->user()->id, 'phone_number' => $request->phoneNumber],
                ['type' => 'public']
            );

        $message = 'Your phone number have been added';
        return apiResponse([], $message, 200);
    }

    public function updateCurrentCity(Request $request, $id)
    {
        $request->validate([
            'city' => 'required'
        ]);

        $city = UserWorkExperience::where('id', $id)->update(['city_town' => $request->city]);

        $message = 'Your city have been added';
        return apiResponse([], $message, 200);
    }

    public function storeWebSite(Request $request)
    {
        $request->validate([
            'webUrl' => 'required|url'
        ]);

        $web_url = UserWebsite::insert(['user_id' => auth()->user()->id, 'website_url' => $request->webUrl]);

        $message = 'Your web site have been added';
        return apiResponse([], $message, 200);
    }

    public function updateWebSite(Request $request, $id)
    {
        $request->validate([
            'webUrl' => 'required|url'
        ]);

        $webUrl = UserWebsite::where('id', $id)->update(['website_url' => $request->webUrl]);

        $message = 'Your web site have been updated';
        return apiResponse([], $message, 200);
    }

    public function deleteWebSite($id)
    {
        try {
            
            $data = [];
            $statusCode = 200;
            $message = 'Deleted Website!';
            $userWorkExp = UserWebsite::where([ ['id', $id], ['user_id', auth()->user()->id] ])->first();

            if(!$userWorkExp) {
                $message = 'Website not exists!';
                $statusCode = '404';
            }else {
                $userWorkExp->delete();
            }

        }catch (ModelNotFoundException $exception) {
            $message = 'work not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function storeSocialLink(Request $request)
    {
        $request->validate([
            'socialLink' => 'required',
            'value' => ['required', 'string', 'max:10', 'in:public,private']
        ]);

        $key = 'socialLink';
        $value = $request->value;

        DB::table('privacy_settings')
            ->updateOrInsert(
                ['user_id' => auth()->user()->id, 'key' => $key],
                ['value' => $value]
            );

        UserSocialProfile::insert(['user_id' => auth()->user()->id, 'social_link' => $request->socialLink, 'type' => $value]);

        $message = 'Your social link have been added';
        return apiResponse([], $message, 200);
    }


    public function storeWorking(Request $request)
    {
        $request->validate([
            'companyName' => 'required',
            'cityTown' => 'required',
            'position' => 'required',
            'jobResponsibilities' => 'required',
            'currentlyWorking' => ['required', 'numeric', 'max:1', 'in:0,1'],
            'startDate' => 'required|date',
        ]);
        $company = $request->companyName;
        $city = $request->cityTown;
        $position = $request->position;
        $responsibilities = $request->jobResponsibilities;
        $currentWorking = $request->currentlyWorking;
        $startDate = array($request->startDate);
        $endDate = isset($request->endDate) ? array($request->endDate) : null;

        $startSplit = $this->SplitDate($startDate);
        $endSplit = isset($request->endDate) ? $this->SplitDate($endDate) : null;

        $userExp = UserWorkExperience::insert([
            'user_id' => auth()->user()->id,
            'company_name' => $company,
            'city_town' => $city,
            'position' => $position,
            'job_responsibilities' => $responsibilities,
            'currently_working'    => $currentWorking,
            'start_year' =>  $startSplit[2],
            'start_month' => $startSplit[1],
            'start_day' => $startSplit[0],
            'end_year' => isset($request->endDate) ? $endSplit[2] : null,
            'end_month' => isset($request->endDate) ? $endSplit[1] : null,
            'end_day' => isset($request->endDate) ? $endSplit[0] : null
        ]);

        $data = [
            'user_id' => auth()->user()->id,
            'company_name' => $company,
            'city_town' => $city,
            'position' => $position,
            'job_responsibilities' => $responsibilities,
            'currently_working'    => $currentWorking,
            'start_year' =>  $startSplit[2],
            'start_month' => $startSplit[1],
            'start_day' => $startSplit[0],
            'end_year' => isset($request->endDate) ? $endSplit[2] : null,
            'end_month' => isset($request->endDate) ? $endSplit[1] : null,
            'end_day' => isset($request->endDate) ? $endSplit[0] : null
        ];

        $message = 'Your workplace have been added';
        return apiResponse($data, $message, 200);
    }

    public function updateWorking(Request $request, $id)
    {
        $request->validate([
            'companyName' => 'required',
            'jobResponsibilities' => 'required',
            'startDate' => 'required|date'
        ]);

        $company = $request->companyName;
        $responsibilities = $request->jobResponsibilities;
        $startDate = array($request->startDate);
        $endDate = isset($request->endDate) ? array($request->endDate) : null;

        $startSplit = $this->SplitDate($startDate);
        $endSplit = isset($request->endDate) ? $this->SplitDate($endDate) : null;

        UserWorkExperience::where('id', $id)
            ->update([
                'job_responsibilities' => $responsibilities,
                'company_name' => $company,
                'start_year' =>  $startSplit[2],
                'start_month' => $startSplit[1],
                'start_day' => $startSplit[0],
                'end_year' => isset($request->endDate) ? $endSplit[2] : null,
                'end_month' => isset($request->endDate) ? $endSplit[1] : null,
                'end_day' => isset($request->endDate) ? $endSplit[0] : null
            ]);

        $data = [
            'job_responsibilities' => $responsibilities,
            'company_name' => $company,
            'start_year' =>  $startSplit[0],
            'start_month' => $startSplit[1],
            'start_day' => $startSplit[2],
            'end_year' => isset($request->endDate) ? $endSplit[2] : null,
            'end_month' => isset($request->endDate) ? $endSplit[1] : null,
            'end_day' => isset($request->endDate) ? $endSplit[0] : null
        ];
        $message = 'Your workplace have been updated';
        return apiResponse($data, $message, 200);
    }

    public function deleteWorking($id)
    {
        try {
            
            $data = [];
            $statusCode = 200;
            $message = 'Deleted Work!';
            $userWorkExp = UserWorkExperience::where([ ['id', $id], ['user_id', auth()->user()->id] ])->first();

            if(!$userWorkExp) {
                $message = 'work not exists!';
                $statusCode = '404';
            }else {
                $userWorkExp->delete();
            }

        }catch (ModelNotFoundException $exception) {
            $message = 'work not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function SplitDate($paragraph)
    {
        $paragraphString = implode('-', $paragraph);

        $paragraphSplit = preg_split("/[-\s:]/", $paragraphString);

        return $paragraphSplit;
    }

    public function storeSchool(Request $request)
    {
        $request->validate([
            'schoolName' => 'required',
            'graduated' => ['required', 'numeric', 'max:1', 'in:0,1'],
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'major_subjects' => 'required'
        ]);
        $school = $request->schoolName;
        $graduated = $request->graduated;
        $startDate = array($request->startDate);
        $endDate = array($request->endDate);
        $major = $request->major_subjects;

        $startSplit = $this->SplitDate($startDate);
        $endSplit = $this->SplitDate($endDate);

        UserEducation::insert([
            'user_id' => auth()->user()->id,
            'school_name' => $school,
            'is_graduated' => $graduated,
            'start_year' => $startSplit[0],
            'end_year' => $endSplit[0],
            'major_subjects' => json_encode($major),
            'description' => ""
        ]);


        $data = [
            'user_id' => auth()->user()->id,
            'gratuated' => $graduated,
            'start_year' => $startSplit[2],
            'end_year' => $endSplit[2],
            'major_subjects' => $major
        ];

        $message = 'Your school or university have been added';
        return apiResponse($data, $message, 200);
    }


    public function updateSchool(Request $request, $id)
    {
        $request->validate([
            'schoolName' => 'required',
            'graduated' => ['required', 'numeric', 'max:1', 'in:0,1'],
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'major_subjects' => 'required'
        ]);
        $school = $request->schoolName;
        $graduated = $request->graduated;
        $startDate = array($request->startDate);
        $endDate = array($request->endDate);
        $major = $request->major_subjects;

        $startSplit = $this->SplitDate($startDate);
        $endSplit = $this->SplitDate($endDate);

        UserEducation::where('id', $id)->update([
            'user_id' => auth()->user()->id,
            'school_name' => $school,
            'is_graduated' => $graduated,
            'start_year' => $startSplit[0],
            'end_year' => $endSplit[0],
            'major_subjects' => json_encode($major),
            'description' => ""
        ]);


        $data = [
            'user_id' => auth()->user()->id,
            'gratuated' => $graduated,
            'start_year' => $startSplit[2],
            'end_year' => $endSplit[2],
            'major_subjects' => $major
        ];

        $message = 'Your school or university has been updated';
        return apiResponse($data, $message, 200);
    }

    public function deleteSchool($id)
    {
        try {
            
            $data = [];
            $statusCode = 200;
            $message = 'Deleted Education!';
            $userEducation = UserEducation::where([ ['id', $id], ['user_id', auth()->user()->id] ])->first();

            if(!$userEducation) {
                $message = 'Education not exists!';
                $statusCode = '404';
            }else {
                $userEducation->delete();
            }

        }catch (ModelNotFoundException $exception) {
            $message = 'Education not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function updateProfession(Request $request)
    {
        $data = [];
        $message = 'Your profession have been added';
        $statusCode = 200;

        $request->validate([
            'profession' => 'required'
        ]);

        User::where('id', auth()->user()->id)->update(['profession' => $request->profession]);
        return apiResponse($data, $message, $statusCode);
    }

    public function updateHomeTown(Request $request)
    {
        $data = [];
        $message = 'Your Home Town have been updated';
        $statusCode = 200;

        $request->validate([
            'home_town' => 'required'
        ]);

        User::where('id', auth()->user()->id)->update(['home_town' => $request->home_town]);
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Listing All users for the messages part
     */
    public function allusers(){
        $users = User::get();

        $data = Transformer::users($users);
        $message = 'All Users Listing Successfully.';

        return apiResponse($data, $message , 200);
    }
}
