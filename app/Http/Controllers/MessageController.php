<?php

namespace App\Http\Controllers;

use App\Business;
use App\Message;
use App\Network;
use App\User;

use Illuminate\Http\Request;


class MessageController extends Controller
{
    /**
     * Get Messages of Users for User
     */
    public function getUserMessages(Request $request, $id){
        $request->merge(['receiver_id' => $id]);
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
        ]);

        $authUser = $request->user();
        $userID = $authUser->id;

        //mark all messages as read
        Message::where('receiver_id', $id)->where('sender_id', $userID)->update(['is_read' => 1]);

        $data = Message::where(function($query) use ($id, $userID) {
            $query->where('sender_id', $userID);
            $query->where('receiver_id', $id);
        })->orWhere(function($query) use ($id, $userID) {
            $query->where('sender_id', $id);
            $query->where('receiver_id', $userID);
        })->get();

        $message = 'User Messages Listing Successfully.';       
        return apiResponse($data, $message , 200);

    }

    /**
     * Get Messages of Business for User
     */
    public function getUserBusinessMessages(Request $request, $id){
        $request->merge(['receiver_business_id' => $id]);
        $request->validate([
            'receiver_business_id' => 'required|integer|exists:businesses,id', 	
        ]);

        $authUser = $request->user();
        $userID = $authUser->id;

        //mark all messages as read
        Message::where('receiver_business_id', $id)->where('sender_id', $userID)->update(['is_read' => 1]);

        $data = Message::where(function($query) use ($id, $userID) {
            $query->where('sender_id', $userID);
            $query->where('receiver_business_id', $id);
        })->orWhere(function($query) use ($id, $userID) {
            $query->where('sender_business_id', $id);
            $query->where('receiver_id', $userID);
        })->get();

        $message = 'Business Messages Listing Successfully.';       
        return apiResponse($data, $message , 200);
    }

    /**
     * Get Messages of Network for User
     */
    public function getUserNetworkMessages(Request $request, $id){
        $request->merge(['receiver_network_id' => $id]);
        $request->validate([
            'receiver_network_id' => 'required|integer|exists:networks,id',
        ]);

        $authUser = $request->user();
        $userID = $authUser->id;

        //mark all messages as read
        Message::where('receiver_network_id', $id)->where('sender_id', $userID)->update(['is_read' => 1]);        

        $data = Message::where(function($query) use ($id, $userID) {
            $query->where('sender_id', $userID);
            $query->where('receiver_network_id', $id);
        })->orWhere(function($query) use ($id, $userID) {
            $query->where('sender_network_id', $id);
            $query->where('receiver_id', $userID);
        })->get();

        $message = 'Network Messages Listing Successfully.';       
        return apiResponse($data, $message , 200);
    }

    /**
     * Get BusinessMsg for Businesses
     */
    public function getBusinessMessages(Request $request, $id, $businessID){
        $request->merge(['sender_business_id' => $id]);
        $request->merge(['receiver_business_id' => $businessID]);
        $request->validate([
            'sender_business_id' => 'required|integer|exists:businesses,id',
            'receiver_business_id' => 'required|integer|exists:businesses,id'
        ]);

        //mark all messages as read
        Message::where('receiver_business_id', $businessID)->where('sender_business_id', $id)->update(['is_read' => 1]);

        $data = Message::where(function($query) use ($id, $businessID) {
            $query->where('sender_business_id', $id);
            $query->where('receiver_business_id', $businessID);
        })->orWhere(function($query) use ($id, $businessID) {
            $query->where('sender_business_id', $businessID);
            $query->where('receiver_business_id', $id);
        })->get();

        $message = 'BusinessToBusiness Message Listing Successfully.';       
        return apiResponse($data, $message , 200);
    }

    /**
     * Get BusinessMsg for Users
     */
    public function getBusinessUserMessages(Request $request, $id, $userID){
        $request->merge(['sender_business_id' => $id]);
        $request->merge(['receiver_id' => $userID]);
        $request->validate([
            'sender_business_id' => 'required|integer|exists:businesses,id',
            'receiver_id' => 'required|integer|exists:users,id'
        ]);

        //mark all messages as read
        Message::where('receiver_id', $userID)->where('sender_business_id', $id)->update(['is_read' => 1]);

        $data = Message::where(function($query) use ($id, $userID) {
            $query->where('sender_business_id', $id);
            $query->where('receiver_id', $userID);
        })->orWhere(function($query) use ($id, $userID) {
            $query->where('sender_id', $userID);
            $query->where('receiver_business_id', $id);
        })->get();

        $message = 'BusinessToUser Message Listing Successfully.';       
        return apiResponse($data, $message , 200);
    }

    /**
     * Get BusinessMsg for Networks
     */
    public function getBusinessNetworkMessages(Request $request, $id, $networkID){
        $request->merge(['sender_business_id' => $id]);
        $request->merge(['receiver_network_id' => $networkID]);
        $request->validate([
            'sender_business_id' => 'required|integer|exists:businesses,id',
            'receiver_network_id' => 'required|integer|exists:networks,id'
        ]);

        //mark all messages as read
        Message::where('receiver_network_id', $networkID)->where('sender_business_id', $id)->update(['is_read' => 1]);

        $data = Message::where(function($query) use ($id, $networkID) {
            $query->where('sender_business_id', $id);
            $query->where('receiver_network_id', $networkID);
        })->orWhere(function($query) use ($id, $networkID) {
            $query->where('sender_network_id', $networkID);
            $query->where('receiver_business_id', $id);
        })->get();

        $message = 'BusinessToNetwork Message Listing Successfully.';       
        return apiResponse($data, $message , 200);
    }

    /**
     * Get NetworkMsg for Networks
     */
    public function getNetworkMessages(Request $request, $id, $networkID){
        $request->merge(['sender_network_id' => $id]);
        $request->merge(['receiver_network_id' => $networkID]);
        $request->validate([
            'sender_network_id' => 'required|integer|exists:networks,id',
            'receiver_network_id' => 'required|integer|exists:networks,id'
        ]);

        //mark all messages as read
        Message::where('receiver_network_id', $networkID)->where('sender_network_id', $id)->update(['is_read' => 1]);

        $data = Message::where(function($query) use ($id, $networkID) {
            $query->where('sender_network_id', $id);
            $query->where('receiver_network_id', $networkID);
        })->orWhere(function($query) use ($id, $networkID) {
            $query->where('sender_network_id', $networkID);
            $query->where('receiver_network_id', $id);
        })->get();

        $message = 'NetworkToNetwork Message Listing Successfully.';       
        return apiResponse($data, $message , 200);
    }

    /**
     * Get NetworkMsg for Users
     */
    public function getNetworkUserMessages(Request $request, $id, $userID){
        $request->merge(['sender_network_id' => $id]);
        $request->merge(['receiver_id' => $userID]);
        $request->validate([
            'sender_network_id' => 'required|integer|exists:networks,id',
            'receiver_id' => 'required|integer|exists:users,id'
        ]);

        //mark all messages as read
        Message::where('receiver_id', $userID)->where('sender_network_id', $id)->update(['is_read' => 1]);

        $data = Message::where(function($query) use ($id, $userID) {
            $query->where('sender_network_id', $id);
            $query->where('receiver_id', $userID);
        })->orWhere(function($query) use ($id, $userID) {
            $query->where('sender_id', $userID);
            $query->where('receiver_network_id', $id);
        })->get();

        $message = 'NetworkToUser Message Listing Successfully.';       
        return apiResponse($data, $message , 200);
    }

    /**
     * Get NetworkMsg for Businesses
     */
    public function getNetworkBusinessMessages(Request $request, $id, $businessID){
        $request->merge(['sender_network_id' => $id]);
        $request->merge(['receiver_business_id' => $businessID]);
        $request->validate([
            'sender_network_id' => 'required|integer|exists:networks,id',
            'receiver_business_id' => 'required|integer|exists:businesses,id'
        ]);

        //mark all messages as read
        Message::where('receiver_business_id', $businessID)->where('sender_network_id', $id)->update(['is_read' => 1]);

        $data = Message::where(function($query) use ($id, $businessID) {
            $query->where('sender_network_id', $id);
            $query->where('receiver_business_id', $businessID);
        })->orWhere(function($query) use ($id, $businessID) {
            $query->where('sender_business_id', $businessID);
            $query->where('receiver_network_id', $id);
        })->get();

        $message = 'NetworkToBusiness Message Listing Successfully.';       
        return apiResponse($data, $message , 200);
    }

    /**
     * store user to user messages
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function storeUserToUserMessages(Request $request){
        $message = Message::create([
            'sender_id' => $request->input('sender_id'),
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message')
        ]);

        broadcast(new MessageSent($message));

        return $message->fresh();
    }

    /**
     * store user to business messages
     */
    public function storeUserToBusinessMessages(){
        $message = Message::create([
            'sender_id' => $request->input('sender_id'),
            'receiver_business_id' => $request->input('receiver_business_id'),
            'message' => $request->input('message')
        ]);
    }

    /**
     * store user to network messages
     */
    public function storeUserToNetworkMessages(){
        $message = Message::create([
            'sender_id' => $request->input('sender_id'),
            'receiver_network_id' => $request->input('receiver_network_id'),
            'message' => $request->input('message')
        ]);
    }

    /**
     * store business to business messages
     */
    public function storeBusinesstoBusinessMessages(){
        $message = Message::create([
            'sender_business_id' => $request->input('sender_business_id'),
            'receiver_business_id' => $request->input('receiver_business_id'),
            'message' => $request->input('message')
        ]);
    }

    /**
     * store business to user messages
     */
    public function storeBusinesstoUserMessages(){
        $message = Message::create([
            'sender_business_id' => $request->input('sender_business_id'),
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message')
        ]);
    }

    /**
     * store business to network messages
     */
    public function storeBusinesstoNetworkMessages(){
        $message = Message::create([
            'sender_business_id' => $request->input('sender_business_id'),
            'receiver_network_id' => $request->input('receiver_network_id'),
            'message' => $request->input('message')
        ]);
    }

    /**
     * store network to network messages
     */
    public function storeNetworktoNetworkMessages(){
        $message = Message::create([
            'sender_network_id' => $request->input('sender_network_id'),
            'receiver_network_id' => $request->input('receiver_network_id'),
            'message' => $request->input('message')
        ]);
    }

    /**
     * store network to User messages
     */
    public function storeNetworktoUserMessages(){
        $message = Message::create([
            'sender_network_id' => $request->input('sender_network_id'),
            'receiver_id' => $request->input('receiver_id'),
            'message' => $request->input('message')
        ]);
    }

    /**
     * store network to business messages
     */
    public function storeNetworktoBusinessMessages(){
        $message = Message::create([
            'sender_network_id' => $request->input('sender_network_id'),
            'receiver_business_id' => $request->input('receiver_business_id'),
            'message' => $request->input('message')
        ]);
    }

    /**
     * search users for the list of users to get the old messages of selected user.
     */
    public function searchUsers(){

    }

    /**
     * search messages from history
     */
    public function messageSearch(){

    }
}
