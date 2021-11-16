<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notification;
use App\Traits\Transformer;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    private $limit; 
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->limit = config()->get('app.pagination');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $authUser = $request->user();
        $notifications = $authUser->notifications()->orderBy('id', 'desc')->paginate($this->limit);
        $meta = Transformer::transformCollection($notifications);
        $data = Transformer::notifications($notifications);
        $message = 'Notifications are listed successfully';
        
        return apiResponse($data, $message, 200, $meta);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       return addNotification($request->user_id, $request->reference_type, $request->reference_id, $request->notification_text);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Notification $notification)
    {
        try{
            $data = $request->all();
            $data['mark_as_read'] = '1';
            $notification->update($data);
            $message = 'Notification mark as read';
            $statusCode = '200';
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Notification does not exists!';
            $statusCode = '404';
        } catch(Exception $exception){
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);

    }
    
    public function markAsReadAll(Request $request, Notification $notification)
    {    
        try{
            $notificationID = $request->all();
            Notification::whereIn('id', $notificationID['ids'])->update(['mark_as_read' => 1]);
            
            $data = [];
            $message= 'Notifications successfully mark as read.';
            $statusCode = '200';

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Notifications does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
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
        try{
            $notification = Notification::findorfail($id);
            $notification->delete();
            $data = '';
            $message= 'Notification remove successfully';
            $statusCode = '200';
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Notification does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
 
    }

    public function deleteMultipleNotifications(Request $request)
    {
        try{
            $notificationID = $request->all();
            Notification::whereIn('id', $notificationID['ids'])->delete();
            
            $data = [];
            $message= 'Notifications remove successfully';
            $statusCode = '200';

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Notifications does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Push Notifications for FCM.
     */
    public function pushNotify(){
        
    }
}