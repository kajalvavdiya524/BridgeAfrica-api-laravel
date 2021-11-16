<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\User;
use App\Post;
use App\AlbumMedia;
use App\Album;
use Illuminate\Support\Facades\Auth;

class ProfileMediaController extends Controller
{

    /**
     * insert media from cover picture.
     *
     * @param  int  $id which is the album id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'media' => 'required|file|max:25000'
        ]);
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $mediaAlbum = Album::findorfail($id);
            $mime =  $request->media->getMimeType();
            $path = "public/users/album/" . auth()->user()->id . "/" . $id;
            $mediaAlbum = $mediaAlbum->media()->create([
                'path' => imageUpload($path, $request->media),
                'media_type' => $mime,
                'user_id' => auth()->user()->id,
                'reference_id' => auth()->user()->id,
            ]);
            $message = "Added Successfully";
        } catch (ModelNotFoundException $exception) {
            $message = "Album does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Make specified media from cover picture.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function makeProfilePic(Request $request, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $media = AlbumMedia::findorfail($id);
            $profileAlbum = Album::where(['name' => 'Profile', 'user_id' => auth()->user()->id])->first();
            if (empty($profileAlbum)) {
                $profileAlbum = AlbumMedia::create([
                    'name' => 'Profile',
                    'user_id' => auth()->user()->id
                ]);
            }
            $profileAlbum->media()->create([
                'media_type' => $media->media_type,
                'path' => $media->path,
                'reference_type' => 'Profile',
                'reference_id' => auth()->user()->id,
                'user_id' => auth()->user()->id
            ]);
            $media->album->user->update([
                'profile_picture' => $media->path
            ]);
            $media->update([
                'is_shared' => true
            ]);
            $media->update([
                'reference_type' => 'Profile',
                'reference_id' => auth()->user()->id,
            ]);
            $message = "Successfully Updated Profile Picture";
        } catch (ModelNotFoundException $exception) {
            $message = "Media does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Make specified media from cover picture.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function makeCoverPic(Request $request, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $media = AlbumMedia::findorfail($id);
            $coverAlbum = Album::where(['name' => 'Cover', 'user_id' => auth()->user()->id])->first();
            if (empty($coverAlbum)) {
                $coverAlbum = Album::create([
                    'name' => 'Cover',
                    'user_id' => auth()->user()->id,
                ]);
            }
            $coverAlbum->media()->create([
                'media_type' => $media->media_type,
                'path' => $media->path,
                'reference_type' => 'Cover',
                'reference_id' => auth()->user()->id,
                'user_id' => auth()->user()->id
            ]);
            $media->update([
                'is_shared' => true,
                'reference_type' => 'Cover',
                'reference_id' => auth()->user()->id,
            ]);
            $media->album->user->update([
                'cover_picture' => $media->path
            ]);
            $message = "Successfully Updated Cover Picture";
        } catch (ModelNotFoundException $exception) {
            $message = "Media does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * This method deletes any media not found in the 
     * logo and cover photo and post album
     * @param  int  $id which is media id
     * @return \Illuminate\Http\Response
     */
    public function destroyPicAlbum($id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            if (auth()->user()->id) {
                $media = AlbumMedia::findorfail($id);
                //check if the image has a reference type
                if (!$media->is_shared) {
                    if (!deleteMedia($media->path)) {
                        $statusCode = 404;
                        $message = "Resource Not Found";
                        return apiResponse($data, $message, $statusCode);
                    }
                }
                $media->delete();
                $message = "Deleted Successfuly";
            }
        } catch (ModelNotFoundException $exception) {
            $message = "Media does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function downloadMedia($mediaId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            if (auth()->user()->id) {
                $media = AlbumMedia::findorfail($mediaId);
                if (!$data) {
                    $message = "Media not found";
                    $statusCode = 404;
                }
                return downloadMedia($media->path);
            }
        } catch (ModelNotFoundException $exception) {
            $message = "Media does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Upload Cover picture.
     * 
     * @param  Request
     * @return \Illuminate\Http\Response
     */
    public function uploadeCoverPicture(Request $request)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $request->validate([
            'image' => 'required|file'
        ]);
        $coverPictureAlbum = Album::where(['name' => 'Cover', 'user_id' => auth()->user()->id])->first();
        if (empty($coverPictureAlbum)) {
            $coverPictureAlbum = Album::create([
                'name' => 'Cover',
                'type' => '',
                'user_id' => auth()->user()->id
            ]);
        }
        $path = "public/users/album/" . auth()->user()->id . "/" . $coverPictureAlbum->id;
        $storagePath = imageUpload($path, $request->image);
        $coverPictureAlbum->media()->create([
            'media_type' =>  $request->image->getmimetype(),
            'path' => $storagePath,
            'user_id' => auth()->user()->id,
        ]);
        User::find(auth()->user()->id)->update(['cover_picture' => $storagePath]);
        $message = "Uploaded Successfully";
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Upload Profile picture.
     * 
     * @param  Request
     * @return \Illuminate\Http\Response
     */
    public function uploadProfilePicture(Request $request)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $request->validate([
            'image' => 'required|file'
        ]);
        $profilePictureAlbum = Album::where(['name' => 'Profile', 'user_id' => auth()->user()->id])->first();
        if (empty($profilePictureAlbum)) {
            $profilePictureAlbum = Album::create([
                'name' => 'Profile',
                'type' => '',
                'user_id' => auth()->user()->id
            ]);
        }
        $path = "public/users/album/" . auth()->user()->id . "/" . $profilePictureAlbum->id;
        $storagePath = imageUpload($path, $request->image);
        $profilePictureAlbum->media()->create([
            'media_type' =>  $request->image->getmimetype(),
            'path' => $storagePath,
            'user_id' => auth()->user()->id,
        ]);
        User::find(auth()->user()->id)->update(['profile_picture' => $storagePath]);
        $message = "Uploaded Successfully";
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Delete Cover Picture.
     * 
     * @param  Request
     * @return \Illuminate\Http\Response
     */
    public function deleteCover()
    {
        $data = [];
        $message = "Deleted Successfuly";
        $statusCode = 200;
        $user = Auth::user();
        if (strcmp($user->cover_picture, '/default_cover_photo/banner.jpg') != 0) {
            if (!deleteMedia($user->cover_picture)) {
                $message = "The given resource was not found";
                $statusCode = 404;
                return apiResponse($data, $message, $statusCode);
            }
            AlbumMedia::where('path', $user->cover_picture)->delete();
            $user->update(['cover_picture' => null]);
        }else{
            $message = "No Profile Picture Was Set";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }
}
