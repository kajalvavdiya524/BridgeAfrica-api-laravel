<?php

namespace App\Http\Controllers\Network;

use App\NetworkAlbum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\NetworkMedia;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NetworkMediaController extends Controller
{

    /**
     * ok
     * reference_id est null
     * reference_type est null
     * Ajouter plusieurs photos dans un album
     */
    public function store(Request $request, $networkId, $albumId)
    {
        $request->validate([
            'media.*' => 'file|required',
            'media.*' => 'required|mimes:jpeg,png,jpg,mp4'
        ]);
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $path = "public/network/" . $networkId . "/album";
            $uploadedFiles = $request->media;
            foreach($uploadedFiles as $file){
                $mime =  $file->getMimeType();
                NetworkMedia::create([
                    'media_url'  => imageUpload($path,$file),
                    'media_type' => $mime,
                    'network_id' => $networkId,
                    'network_album_id' => $albumId
                ]);
            }
            $message = "Added Successfully";
        } catch (ModelNotFoundException $exception) {
            $message = "Album does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * ok
     *modifier la photo de profil
     *
     */

    public function makeProfilePic(Request $request, $network, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $media = NetworkMedia::findorfail($id);
            $logoAlbum = NetworkAlbum::where(['name' => 'logo', 'network_id' => $network])->first();
            if (empty($logoAlbum)) {
                $logoAlbum = NetworkAlbum::create([
                    'name' => 'logo',
                    'network_id' => $network
                ]);
            }
            $logoAlbum->networkMedia()->create([
                'media_type' => $media->media_type,
                'media_url' => $media->media_url,
                // 'reference_type' => 'logo',
                // 'reference_id' => $network,
                'network_id' => $network
            ]);
            $media->update([
                'is_shared' => "1",
            ]);
            $media->networkAlbum->network->update([
                'image' => $media->media_url
            ]);
            $message = "Successful";
        } catch (ModelNotFoundException $exception) {
            $message = "Media does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * ok
     */
    public function destroyPicAlbum($networkId, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $media = NetworkMedia::findorfail($id);
            //check if the image has a reference type
            if (strcmp($media->is_shared, "1") !== 0) {
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


    /**
     * ok
     */
    public function downloadMedia($networkId,$mediaId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $media = NetworkMedia::findorfail($mediaId);
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

}
