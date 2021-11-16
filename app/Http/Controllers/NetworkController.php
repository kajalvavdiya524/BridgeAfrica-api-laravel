<?php

namespace App\Http\Controllers;

use Auth;
use Exception;
use App\Network;
use App\NetworkRole;
use App\NetworkUser;
use App\NetworkCategory;
use App\BusinessNetwork;
use App\NeighborhoodNetwork;
use App\NetworkSetting;
use App\Business;
use App\Country;
use App\Region;
use App\Division;
use App\Council;
use App\Neighborhood;
use App\User;
use App\Post;
use App\NetworkUserRole;
use App\NetworkBanned;
use App\Traits\Transformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\NetworkStoreRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Role;

class NetworkController extends Controller
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
     * List of Networks of a user.
     */
    public function index(Request $request)
    {
       $authUser = $request->user();
       $networks = $authUser->networks()->where('is_approve', 1)->paginate($this->limit);
       $meta = Transformer::transformCollection($networks);
       $data = Transformer::networks($networks);
       $message = $authUser->name .' Networks Listing Successfully.';
       
       return apiResponse($data, $message , 200, $meta);
    }

    /**
     * Business Networks Listing
     */
    public function businessNetwork(Request $request, $id){
        $request->merge(['business_id' => $id]);
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
        ]);
        $networks = Network::where('business_id', $id)->where('is_approve', 1)->paginate($this->limit);
        $meta = Transformer::transformCollection($networks);
        $data = Transformer::networks($networks);
        $message = 'Business Networks Listing Successfully.';
       
        return apiResponse($data, $message , 200, $meta);
    }

    /**
     * List of All Networks
     */
    public function allNetworks(){
        $networks = Network::where('is_approve', 1)->paginate($this->limit);
        $meta = Transformer::transformCollection($networks);
        $data = Transformer::networks($networks);
        $message = 'All Networks Listing Successfully.';

        return apiResponse($data, $message , 200, $meta);
    }

    /**
     * List of unapprove networks of a user
     */
    public function userUnapproveNetworks(Request $request){
        $authUser = $request->user();
        $networks = $authUser->networks()->where('is_approve', 0)->paginate($this->limit);
        $meta = Transformer::transformCollection($networks);
        $data = Transformer::networks($networks);
        $message = 'Unapprove User network Listing Successfully.';

        return apiResponse($data, $message, 200, $meta);
    }

    /**
     * List of unapprove networks of a business
     */
    public function businessUnapproveNetworks(Request $request, $id){
        $request->merge(['business_id' => $id]);
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
        ]);
        $networks = Network::where('business_id', $id)->where('is_approve', 0)->paginate($this->limit);
        $meta = Transformer::transformCollection($networks);
        $data = Transformer::networks($networks);
        $message = 'Unapprove Business Networks Listing Successfully.';

        return apiResponse($data, $message, 200, $meta);
    }

    /**
     * List of unapprove networks
     */
    public function unapproveNetworks(){
        $networks = Network::where('is_approve', 0)->paginate($this->limit);
        $meta = Transformer::transformCollection($networks);
        $data = Transformer::networks($networks);
        $message = 'Unapprove Networks Listing Successfully.';

        return apiResponse($data, $message, 200, $meta);
    }

    /**
     * Approve Networks
     */
    public function approveNetworks(Request $request, $id, Network $network){
        try{
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:networks,id',
            ]);
            $network = Network::findorfail($id);
            
            $data['is_approve'] = 1;
            
            $network->update($data);
            $message = 'Network approved successfully.';
            $statusCode = '200';

            $notificationMsg = 'Your '.$network->name.' network creation has been approved by the admin.';
            addNotification($network->user_id, 'Network', $id, $notificationMsg);

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Network does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NetworkStoreRequest $request)
    {
        $DirName = 'network';
        $file = $request->file('image');
        $fileDetails = $request->image;
        $user = Auth::user(); 
        $business = Business::get();
        
        $network_categories = json_encode(explode(',', $request->network_categories), true);
        
        $data = $request->all();
        $data = $request->except(['neighborhood_id']);
        $data['user_id'] = $user->id;
        $data['network_categories'] = $network_categories;
        $data['image'] = imageStorage($DirName, $file, $fileDetails);
        $data['secondary_phone'] = 11222222;
        $network = Network::create($data);
        if($request->has('neighborhood_id')){
            $neighborhood_ids = explode(',', $request->neighborhood_id);
            $network->NeighborhoodNetworks()->attach($neighborhood_ids);
        }

        $settingKey = array('Privay', 'Posting Permission', 'Post Approval');
        $settingValue = array('Public', 'Admin Only', 0);

        for($counter = 0; $counter < 3; $counter++){
            $setting = array(
                'network_id' => $network->id,
                'setting_key' => $settingKey[$counter],
                'setting_value' => $settingValue[$counter]
            );
            NetworkSetting::create($setting);
        }
        
        $network->networkUsers()->attach([$user->id]);

        $user->assignRole('network_admin');

        NetworkUser::where('network_id', $network->id)->where('user_id', $user->id)
            ->update(['is_approve' => 1]);
        
        $message = 'Network posted successfully.';
        return apiResponse($data, $message, 201);
    }

    /**
     * Assign Categories to a Network
     */
    public function assignNetworkCategories(Request $request, $id){
        
        $request->merge(['id' => $id]);
        
        $request->validate([
            'id' => 'required|integer|exists:networks,id',
            'category_id' => 'required|exists:categories,id'
        ]);
        
        $network = Network::findorfail($id);
        $category_ids = explode(',', $request->category_id);

        $network->networkCategory()->attach($category_ids);

        $data = [];
        $message = 'Categories added for '. $network->name . ' Network Successfully.';
        return apiResponse($data, $message, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $network = Network::findorfail($id);
            $data = Transformer::network($network);
            $message = 'Network list successfully';
            $statusCode = '200';
        } catch (ModelNotFoundException $exception) {
            $data = [];
            $message = 'Network does not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Network $network)
    {
        try {
            $request->validate([
                'business_id' => 'integer|exists:businesses,id',
                'name' => 'string|max:255',
                'network_categories' => 'string',
                'description' => 'string',
                'purpose' => 'string',
                'special_needs' => 'string',
                'address' => 'string|max:255',
                'region_id' => 'integer|exists:businesses,id',
                'division_id' => 'integer|exists:divisions,id',
                'council_id' => 'integer|exists:councils,id',
                'neighborhood_id.*' => 'exists:neighborhoods,id|nullable',
                'country_id' => 'integer|exists:countries,id',
                'image' => 'mimes:jpeg,png,jpg|max:2048',
                'allow_business' => 'boolean', 
                'email' => 'email|max:100',
                'primary_phone' => 'numeric',
                'secondary_phone' => 'numeric|nullable'
            ]);
            if ($request->hasfile('image')) {
                Storage::delete($network->image);
            }
            $DirName = 'network';
            $file = $request->file('image');
            $fileDetails = $request->image;

            $user = Auth::user();

            $data = $request->all();
            $data = $request->except(['neighborhood_id']);
            if($request->hasfile('image')){ 
                $data['image'] = imageStorage($DirName, $file, $fileDetails);
            }
            else{
                $data['image'] = $network->image;
            }
            $data['network_categories'] = json_encode(explode(',', $request->network_categories), true);
            
            $network->update($data);

            if($request->neighborhood_id){
                $neighborhood_ids = explode(',', $request->neighborhood_id);
                $network->NeighborhoodNetworks()->sync($neighborhood_ids);
            } 
            
            $message = 'Network updated successfully';
            $statusCode = '200';
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Network does not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $network = Network::findorfail($id);
            $network->delete();
            $data = [];
            $message = 'Network removed sucessfully.';
            $statusCode = '200';
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
     * Add members to the network.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addNetworkMember(Request $request, $id)
    {
        try {
            $network = Network::findorfail($id);
            if($network->is_approve == 1){
                $user = Auth::user();
                $networkMember = $network->networkUsers()->where('user_id', $user->id)
                        ->where('network_id', $id)
                        ->role('network_member')->get();
                
                if(count($networkMember) > 0){
                    $data = '';
                    $message = $user->name . ' already added as a member.';
                    $statusCode = '201';
                }
                else{
                    $notificationMsg = $user->name . ' requested to join your '. $network->name .' Network.';
                    $network->networkUsers()->attach([$user->id]);
                    $user->assignRole('network_member');
                    $data = [];
                    $message = $user->name . ' added successfully as a Network member, waiting for the admin approval.';
                    $statusCode = '201';

                    addNotification($network->user_id, 'Network', $id, $notificationMsg);
                }
            }
            else{
                $data = [];
                $message = $network->name . ' Network did not approved yet.';
                $statusCode = '201';
            }
                
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'User does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);

    }
    
    public function addNetworkBusinessMember(Request $request, $id, $business_id){
        
        try {
            $request->merge(['network_id' => $id]);
            $request->merge(['business_id' => $business_id]);
            $request->validate([
                'network_id' => 'required|integer|exists:networks,id',
                'business_id' => 'required|integer|exists:businesses,id'
            ]);
            $network = Network::findorfail($id);
            $business = Business::findorfail($business_id);
            if($network->allow_business == 1){
                $networkMember = $network->businessNetworks()->where('business_id', $business_id)
                        ->where('network_id', $id)
                        ->role('network_member')->get();

                if(count($networkMember) > 0){
                    $data = '';
                    $message = $business->name . ' already added as a member.';
                    $statusCode = '201';
                }
                else{
                    $notificationMsg = $business->name . ' (Business) requested to join your '. $network->name .' Network.';
                    $network->businessNetworks()->attach([$business_id]);
                    $business->assignRole('network_member');
                    $data = [];
                    $message = $business->name . ' added successfully as a Network member, waiting for the admin approval.';
                    $statusCode = '201';

                    addNotification('', 'Network', $id, $notificationMsg, $business->id);
                }
            }
            else{
                $data = [];
                $message = 'Business are not allowed to join this Network.';
                $statusCode = '403';
            }

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'User does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Approve Members of network
     */
    public function approveNetworkMember(Request $request, $id, $user_id)
    {
        try {
            $request->merge(['network_id' => $id]);
            $request->merge(['user_id' => $user_id]);
            $request->validate([
                'network_id' => 'required|integer|exists:networks,id',
                'user_id' => 'required|integer|exists:users,id'
            ]);
            $network = Network::findorfail($id);
            $users = User::findorfail($user_id);
            $user = Auth::user();
            
            $networkEditor = $network->networkUsers()->where('user_id', $user->id)
                            ->where('network_id', $id)->role('network_editor')->get();
            
            if($network->user_id == $user->id){
                NetworkUser::where('network_id', $id)->where('user_id', $user_id)
                ->update(['is_approve' => 1]);

                $data = [];
                $message = 'Network Member has been approved.';
                $statusCode = '200';
                $notificationMsg = $users->name . ' has been approved for '. $network->name .' Network';
                addNotification($users->id, 'Network', $id, $notificationMsg);
            }
            elseif(count($networkEditor) > 0){
                NetworkUser::where('network_id', $id)->where('user_id', $user_id)
                ->update(['is_approve' => 1]);

                $data = [];
                $message = 'Network Member has been approved by the Editor.';
                $statusCode = '200';

                $notificationMsg = $users->name . ' has been approved for '. $network->name .' Network';
                addNotification($users->id, 'Network', $id, $notificationMsg);
            }
            else{
                $data = [];
                $message = 'You are not authorized to do this operation';
                $statusCode = '403';
            } 
            
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Network does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Approve Business Members of network
     */
    public function approveNetworkBusinessMember(Request $request, $id, $business_id){
        try {
            $request->merge(['network_id' => $id]);
            $request->merge(['business_id' => $business_id]);
            $request->validate([
                'network_id' => 'required|integer|exists:networks,id',
                'business_id' => 'required|integer|exists:businesses,id'
            ]);

            $network = Network::findorfail($id);
            $business = Business::findorfail($business_id);
            $user = Auth::user();

            $networkEditor = $network->networkUsers()->where('user_id', $user->id)
                            ->where('network_id', $id)->role('network_editor')->get();

            if($network->user_id == $user->id){
                BusinessNetwork::where('network_id', $id)->where('business_id', $business_id)
                ->update(['is_approve' => 1]);

                $data = [];
                $message = 'Network Business Member has been approved.';
                $statusCode = '200';

                $notificationMsg = $business->name . ' has been approved for '. $network->name .' Network';
                addNotification('', 'Network', $id, $notificationMsg, $business->id);
            }
            elseif(count($networkEditor) > 0){
                BusinessNetwork::where('network_id', $id)->where('business_id', $business_id)
                ->update(['is_approve' => 1]);

                $data = [];
                $message = 'Network Business Member has been approved by the Editor.';
                $statusCode = '200';

                $notificationMsg = $business->name . ' (Business) has been approved for '. $network->name .' Network';
                addNotification('', 'Network', $id, $notificationMsg, $business->id);
            }
            else{
                $data = [];
                $message = 'You are not authorized to do this operation';
                $statusCode = '403';
            }

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Network does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * List of unapprove members
     */
    public function unapproveNetworkMembers(Request $request, $id){
        try{
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:networks,id',
            ]);
            $network = Network::findorfail($id);
            $data = $network->networkUsers()->where('is_approve', 0)
                            ->role('network_member')->get();
            $statusCode = '200';
            $message = 'Network unapprove members listing successfully.';
        
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'User does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * List of unapprove Business members
     */
    public function unapproveNetworkBusinessMembers(Request $request, $id){
        try{
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:networks,id',
            ]);
            $network = Network::findorfail($id);
            $data = $network->businessNetworks()->where('is_approve', 0)
                            ->role('network_member')->get();
            $statusCode = '200';
            $message = 'Network Business unapprove members listing successfully.';
        
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Business Member does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Network MembersList.
     */
    public function networkMembersList(Request $request, $id){
        try{
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:networks,id',
            ]);
            $network = Network::findorfail($id);
            $data = $network->networkUsers()->where('is_approve', 1)
                            ->role('network_member')->get();
            $statusCode = '200';
            $message = 'Network members listing successfully.';
        
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Network does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Network Business Members List.
     */
    public function networkBusinessMembersList(Request $request, $id){
        try{
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:networks,id',
            ]);
            $network = Network::findorfail($id);
            $data = $network->businessNetworks()->where('is_approve', 1)
                            ->role('network_member')->get();
            $statusCode = '200';
            $message = 'Network members listing successfully.';
        
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Network does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Add network Editors.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addNetworkEditor($id, $user_id){
        try{
            $user = User::findorfail($user_id);
            $network = Network::findorfail($id);
            
            $networkModerator = $network->networkUsers()->where('user_id', $user_id)
                    ->where('network_id', $id)
                    ->where('is_approve', 1)
                    ->role('network_editor')->get();
            
            if(count($networkModerator) > 0){
                $data = '';
                $message = $user->name . ' already added as a editor.';
                $statusCode = '201';
            }
            else{
                $network->networkUsers()->attach([$user->id]);
                $user->assignRole('network_editor');
                $data = [];
                $message =  $user->name . ' added as Network Editor successfully.';
                $statusCode = '201';

                $notificationMsg = $user->name . ' is now an editor of '. $network->name .' Network.';
                addNotification($user_id, 'Network', $id, $notificationMsg);
            }
            
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Network/User does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * List of Editor's of the network.
     */
    public function networkEditorList(Request $request, $id){
        try{
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:networks,id',
            ]);
            $network = Network::findorfail($id);
            
            $data = $network->networkUsers()->where('is_approve', 1)
                    ->role('network_editor')->get();
            $statusCode = '200';
            $message = 'Network Editors listing successfully.';
        
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Network does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Approve network request for the network.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approveNetworkPost(Request $request, $id, $user_id, $post_id)
    {

        try {
            $request->merge(['network_id' => $id]);
            $request->merge(['user_id' => $user_id]);
            $request->merge(['post_id' => $post_id]);
            $request->validate([
                'network_id' => 'required|integer|exists:networks,id',
                'user_id' => 'required|integer|exists:users,id',
                'post_id' => 'required|integer|exists:posts,id',
            ]);


            $isModeratorAndCanApprove = NetworkUser::join('network_roles', 'network_users.network_role_id', '=', 'network_roles.id')
                ->where('network_users.network_id', $id)
                ->where('network_users.user_id', $user_id)
                ->where('network_users.is_approved', 1)
                ->whereIn('network_roles.name', ['moderator', 'network_admin'])
                ->select('network_users.*')
                ->limit(1)->get()->count();

            if ($isModeratorAndCanApprove < 0) {
                $data = [];
                $message = 'User does not have permission.';
                $statusCode = '200';
            } else {
                Post::where('network_id', $id)
                    ->where('post_id', $post_id)
                    ->update(['is_approve' => 1]);
                $data = [];
                $message = 'Network Post has been approved.';
                $statusCode = '200';
            }
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Post does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Decline network request for the network.
     *
     * @return \Illuminate\Http\Response
     */   
    public function declineNetworkPost(Request $request, $id, $user_id, $post_id)
    {

        try {
            $request->merge(['network_id' => $id]);
            $request->merge(['user_id' => $user_id]);
            $request->merge(['post_id' => $post_id]);
            $request->validate([
                'network_id' => 'required|integer|exists:networks,id',
                'user_id' => 'required|integer|exists:users,id',
                'post_id' => 'required|integer|exists:posts,id',
            ]);


            $isModeratorAndCanDecline = NetworkUser::join('network_roles', 'network_users.network_role_id', '=', 'network_roles.id')
                ->where('network_users.network_id', $id)
                ->where('network_users.user_id', $user_id)
                ->where('network_users.is_approved', 1)
                ->whereIn('network_roles.name', ['moderator', 'network_admin'])
                ->select('network_users.*')
                ->limit(1)->get()->count();

            if ($isModeratorAndCanDecline < 0) {
                $data = [];
                $message = 'User does not have permission.';
                $statusCode = '200';
            } else {
                Post::where('network_id', $id)
                    ->where('post_id', $post_id)
                    ->update(['is_approve' => 0]);
                $data = [];
                $message = 'Network Post has been approved.';
                $statusCode = '200';
            }
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Post does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }

        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Show all network pending post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */    
    public function showNetworkPendingPost(Request $request, $id)
    {
        try {
            $request->merge(['network_id' => $id]);
            $request->validate([
                'network_id' => 'required|integer|exists:networks,id',
            ]);

            $posts = Post::join('business_settings', 'posts.business_id', '=', 'business_settings.business_id')
                ->where('posts.network_id', $id)
                ->where("posts.is_approve", 0)
                ->where("business_settings.post_approval", 1)
                ->paginate($this->limit)->findOrFail();
            $meta = Transformer::transformCollection($posts);
            $data = Transformer::posts($posts);
            $message = 'Pendning Network Post list successfully';
            $statusCode = '200';
        } catch (ModelNotFoundException $exception) {
            $data = [];
            $message = 'Posts belonging to this network does not exist!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }

        return apiResponse($data, $message, $statusCode, $meta);
    }

    /**
     * Network general settings.
     */
    public function generalSettings(Request $request, $networkId)
    {
        $request->validate([
            'privacy' => 'required',
            'post_permission' => 'required',
            'post_approval' => 'required'
        ]);
        $data = [];
        $network = Network::where(['user_id' => auth()->user()->id, 'id' => $networkId])->get('id');
        if ($network) {
            $message = 'settings updated';
            $statusCode = '200';
            $setting = NetworkSetting::where('network_id', $networkId)->first();
            $setting->where('setting_key', 'privacy')->update(['setting_value' => $request->privacy]);
            $setting->where('setting_key', 'post_permission')->update(['setting_value' => $request->post_permission]);
            $setting->where('setting_key', 'post_approval')->update(['setting_value' => $request->post_approval]);
        } else {
            $message = 'Network not found';
            $statusCode = '404';
        }

        return apiResponse($data, $message, $statusCode);
    }

    /**
     * List of user networks.
     */
    public function getNetwork()
    {
        try {
            $message = 'Network Info';
            $statusCode = '200';
            $network = Network::where('user_id', auth()->user()->id);
            $data = Transformer::networks($network);
        } catch (ModelNotFoundException $exception) {
            $data = [];
            $message = 'network does not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display blocked users of a network.
     */
    public function getBannedUsers($id)
    {
        $network = Network::where(['user_id' => auth()->user()->id, 'id' => $id]);
        if ($network) {
            $message = 'List of blocked Members';
            $statusCode = '200';
            $users = NetworkBanned::where('network_id', $id)->get('user_id');
            $data = User::whereIn('id', $users)->get()->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'profile_picture' => $item->profile_picture
                ];
            });
        } else {
            $message = 'Unauthorised';
            $statusCode = '404';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Unblocking a blocked user.
     */
    public function unbanUser($networkID, $userId)
    {
        $data = [];
        try {
            $ban = NetworkBanned::where(['network_id' => $networkID, 'user_id'=> $userId])->delete();
            if ($ban) {
                $message = 'Member Unblocked';
                $statusCode = '200';
            }
        } catch (ModelNotFoundException $exception) {
            $data = [];
            $message = 'user, network not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display all roles of a network.
     */
    public function displayRole()
    {
        if (auth()->user()->id) {
            $message = 'Display roles';
            $data = Role::get()->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            });
            return apiResponse($data, $message, 200);
        } else {
            $message = 'No Role';
            $data = [];
            return apiResponse($data, $message, 404);
        }
    }

    /**
     * Assign a role to a member.
     */
    public function assignRole(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|numeric',
            'role_id' => 'required|numeric'
        ]);

        try {
            User::findorfail($request->user_id);
            Role::findOrfail($request->role);
            Network::findOrfail($id);
            DB::table('network_users')
                ->updateOrInsert(
                    ['user_id' => $request->user_id, 'network_id' => $id],
                    ['network_role_id' => $request->role]
                );
            $message = 'Role have been saved';
            $data = [];
            $statusCode = '200';
        } catch (ModelNotFoundException $exception) {
            $data = [];
            $message = 'user, network or role not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Reassigning a new role to a user.
     */
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
            NetworkRole::findOrfail($request->role);
            NetworkUser::where('user_id', $id)->update(['network_role_id' => $request->role]);
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
    
    /**
     * Remove a user/member as an editor.
     */
    public function deleteEditor($id)
    {
        $data = [];
        try {
            $message = 'The role of user have been deleted';
            $statusCode = 200;
            User::findOrfail($id);
            NetworkUser::where('user_id', $id)->where('network_role_id','network_moderator')
            ->update(['network_role_id' => 'network_member']);
        } catch (ModelNotFoundException $exception) {
            $message = 'user does not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    
    /**
     * Display members to the network.
    
     * Show all network pending post.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function diplayNetworkMember($id){        
        try{
            $networkUser = NetworkUser::where('network_id', $id)->get('user_id');
            $networkBusiness = NetworkUser::where('network_id', $id)->get('business_id');
            $user = User::whereIn('id', $networkUser)->get()->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'profile_picture' => $item->profile_picture,
                ];
            });
            $business = Business::whereIn('id', $networkBusiness)->get()->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'picture' => $item->logo_path,
                    'category' => $item->category,
                    'latitute' => $item->lat,
                    'longitute' => $item->lng,
                    'about_business' => $item->about_business,
                    'location_description' => $item->location_description,
                    'followers' => $item->businessfollower->count(),
                ];
            });

            $data = [
                'user' => $user,
                'business' => $business
            ];
            $message = 'Network member successfully listed.';
            $statusCode = '200';
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'NO users.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
        
    }

    /**
     * Network Search
     */
    public function networkSearch(Request $request){
        $request->validate([
            'keyword' => 'string|nullable',
            'cat_id' => 'integer|exists:categories,id|nullable',
            'country_id' => 'integer|exists:countries,id|nullable',
            'region_id' => 'integer|exists:regions,id|nullable',
            'division_id' => 'integer|exists:divisions,id|nullable',
            'council_id' => 'integer|exists:councils,id|nullable',
            'neighborhood_id.*' => 'exists:neighborhoods,id|nullable'   
        ]);
        $keyword = $request->keyword;
        $cat_id = $request->cat_id;
        $country_id = $request->country_id;
        $region_id = $request->region_id;
        $division_id = $request->division_id;
        $council_id = $request->council_id;
        $neighborhood_id = $request->neighborhood_id;

        $network = Network::orderBy('id', 'desc')->with('networkCategory', 'NeighborhoodNetworks')->where('is_approve', 1);

        if($keyword){
            $network = $network->where('name', 'LIKE', "%{$keyword}%");                   
        }
        
        if($country_id){
            $network = $network->where('country_id', $country_id);
        }
        if($region_id){
            $network = $network->where('region_id', $request->region_id);
        }
        if($division_id){
            $network = $network->where('division_id', $division_id);
        }
        if($council_id){
            $network = $network->where('council_id', $council_id);
        }
        
        if($cat_id){
            $network = $network->whereHas('networkCategory', function($cat) use ($cat_id){
                                    $cat->where('category_id', $cat_id);
                                });
        }

        if($neighborhood_id){
            $network = $network->whereHas('NeighborhoodNetworks', function ($neighborhood) use ($neighborhood_id) {
                $neighborhood->whereIn('neighborhood_id', [$neighborhood_id]);
                });
        }
        
        $networkSearch = $network->paginate($this->limit);
        $meta = Transformer::transformCollection($networkSearch);
        $data = Transformer::networks($networkSearch);        
        $message = 'Network Listing Successfully.';

        return apiResponse($data, $message, 200, $meta);

    }
}
