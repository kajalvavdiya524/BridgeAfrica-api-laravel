<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessMedia;
use App\BusinessAlbum;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class BusinessMediaController extends Controller
{

    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }
    /**
     * insert media into an album.
     *
     * @param  int  $id which is the album id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $businessId, $id)
    {
        $request->validate([
            'media' => 'required|file|mimes:mp4,jpg,jpeg,png'
        ]);
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $mediaAlbum = BusinessAlbum::findorfail($id);
            $mime =  $request->media->getMimeType();
            $path = "public/business/" . $businessId . "/album";
            $mediaAlbum = $mediaAlbum->businessMedia()->create([
                'media_url' => imageUpload($path, $request->media),
                'media_type' => $mime,
                'business_id' => $businessId
            ]);
            $message = "Added Successfully";
        } catch (ModelNotFoundException $exception) {
            $message = "Album does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Make specified media logo picture.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function makeLogoPic(Request $request, $business, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $media = BusinessMedia::findorfail($id);
            $logoAlbum = BusinessAlbum::where(['name' => 'logo', 'business_id' => $business])->first();
            if (empty($logoAlbum)) {
                $logoAlbum = BusinessAlbum::create([
                    'name' => 'logo',
                    'business_id' => $business
                ]);
            }
            $logoAlbum->businessMedia()->create([
                'media_type' => $media->media_type,
                'media_url' => $media->media_url,
                'reference_type' => 'logo',
                'reference_id' => $business,
                'business_id' => $business,
            ]);
            $media->update([
                'is_shared' => true
            ]);
            $media->businessAlbum->business->update([
                'logo_path' => $media->media_url
            ]);
            $message = "Successful";
        } catch (ModelNotFoundException $exception) {
            $message = "Media does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Make specified media  cover picture.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function makeCoverPic(Request $request, $business, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $media = BusinessMedia::findorfail($id);
            $logoAlbum = BusinessAlbum::where(['name' => 'cover_photo', 'business_id' => $business])->first();
            if (empty($logoAlbum)) {
                $logoAlbum = BusinessAlbum::create([
                    'name' => 'cover_photo',
                    'business_id' => $business
                ]);
            }
            $logoAlbum->businessMedia()->create([
                'media_type' => $media->media_type,
                'media_url' => $media->media_url,
                'reference_type' => 'cover_photo',
                'reference_id' => $business,
                'business_id' => $business
            ]);
            $media->update([
                'is_shared' => true
            ]);
            $message = "Successful";
        } catch (ModelNotFoundException $exception) {
            $message = "Media does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }


    /**
     * Download the specified media from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadMedia($businessId, $mediaId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $media = BusinessMedia::findorfail($mediaId);
            if (!$data) {
                $message = "Media not found";
                $statusCode = 404;
            }
            return downloadMedia($media->media_url);
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
    public function destroyPicAlbum($business, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $media = BusinessMedia::findorfail($id);
            //check if the image has been set as cover or logo picture
            if (!$media->is_shared) {
                if (!deleteMedia($media->media_url)) {
                    $statusCode = 404;
                    $message = "Resource Not Found";
                    return apiResponse($data, $message, $statusCode);
                }
            }
            $media->delete();
            $message = "Deleted Successfuly";
        } catch (ModelNotFoundException $exception) {
            $message = "Media does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }
}
