<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BusinessAlbum;
use App\Business;
use App\BusinessMedia;
use App\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;



class BusinessAlbumController extends Controller
{
    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }

    /**
     * Display a listing of the album resource of a business.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($businessId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $business = Business::findorfail($businessId);
        $data = $business->businessAlbum->map(function ($album, $key) {
            $data = [
                'id' => $album->id,
                'album_name' => $album->name,
                'media' => $album->businessMedia->take(1)->map(function ($media, $key) {
                    return getMediaUrl($media->media_url);
                }),
                'item_number' => $album->businessMedia->count(),
            ];
            return $data;
        });
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Store a newly created resource in storage.
     * create an album for a business
     * @param  \Illuminate\Http\Request  $request
     *  @param  int $id which is the business id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $businessId)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $data = [];
        $message = " ";
        $statusCode = 201;
        $business = Business::findorfail($businessId);
        $business->businessAlbum()->create([
            'name' => $request->name
        ]);
        $message = "Successfully created";
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display the specified resource.
     * list the media found in an album
     * @param  int  $id which is album_id
     * @return \Illuminate\Http\Response
     */
    public function show($businessId, $albumId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $businessAlbum = BusinessAlbum::findorfail($albumId);
        $data = [
            'name' => $businessAlbum->name,
            'media' => $businessAlbum->businessMedia->map(function ($media, $key) {
                $data = [
                    'id' => $media->id,
                    'media_url' => getMediaUrl($media->media_url),
                    'media_type' => $media->media_type
                ];
                return $data;
            })
        ];
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Update the specified resource in storage.
     *  update the name of an album of a business
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id which is album_id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $businessId, $albumId)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $businessAlbum = BusinessAlbum::findorfail($albumId);
            $businessAlbum->update([
                'name' => $request->name
            ]);
            $message = "Successfully updated";
        } catch (ModelNotFoundException $exception) {
            $message = "Album does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *  Delete media in an album
     * @param  int  $id which is album_id
     * @return \Illuminate\Http\Response
     */
    public function destroy($businessId, $albumId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $businessAlbum = BusinessAlbum::findorfail($albumId);
            // dd($businessAlbum);
            if ($businessAlbum->businessMedia->isNotEmpty()) {
                $businessAlbum->businessMedia->where('is_shared', false)
                    ->map(function ($item, $key) {
                        if (!deleteMedia($item->media_url)) {
                            $data = [];
                            $statusCode = 404;
                            $message = "Resource Not Found";
                            return apiResponse($data, $message, $statusCode);
                        }
                    });
            }
            $businessAlbum->delete();
            $message = "Successfully deleted";
        } catch (ModelNotFoundException $exception) {
            $message = "Album does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Show all the media of a post with its content.
     *
     * @param  int  $id which is album_id
     * @return \Illuminate\Http\Response
     */
    public function showPostMedia($businessId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $business = Business::find($businessId);
            $data = $business->post->map(function ($post, $key) {
                $data = [
                    'id' => $post->id,
                    'content' => $post->content,
                    'media' => BusinessMedia::where('reference_id', $post->id)->get()->map(function ($item, $key) {
                        $data = [
                            'media_ulr' => getMediaUrl($item->media_url),
                            'media_type' => $item->media_type
                        ];
                        return $data;
                    })
                ];
                return $data;
            });
            return apiResponse($data, $message, $statusCode);
        } catch (ModelNotFoundException $exception) {
            $message = "Album does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }
}
