<?php

namespace App\Http\Controllers;

use App\User;
use App\UserContact;
use Illuminate\Http\Request;

class UserContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|unique:user_contacts,phone_number'
        ]);
        $data = [];
        $message = "Phone Number Added Successfuly";
        $statusCode = 200;
        $id = auth()->user()->id;
        if($request->id){
            $id = $request->id;
        }
        $user = User::find($id)->userContacts()->create([
            'phone_number' => $request->phone,
            'type' => 'phone number'
        ]);
        return apiResponse($data,$message,$statusCode);
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
    public function update(Request $request, $id)
    {
        $request->merge(['id' => $id]);
        $request->validate([
            'id' => 'required|exists:user_contacts,id',
            'phone' => 'required|string|unique:user_contacts,phone_number'
        ]);
        $data = [];
        $message = "Phone Number Upated Successfuly";
        $statusCode = 200;
        UserContact::find($request->id)->update([
            'phone_number' => $request->phone
        ]);
        return apiResponse($data,$message,$statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = [];
        $statusCode = 200;
        try {
            $userContact = UserContact::findorfail($id);
            $userContact->delete();
            $message = "Phone Number Successfully updated";
        } catch (ModelNotFoundException $exception) {
            $message = "Phone Number does not exist";
            $statusCode = 404;
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data,$message,$statusCode);
    }
}