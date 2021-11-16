<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Album;
use App\AlbumMedia;
use App\Post;


class AlbumController extends Controller
{
    public function createAlbum(Request $request)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $data = [];
        if (Album::where('name', $request->name)->exists()) {
            $message = 'Album name exist';
            $statusCode = '403';
        } else {
            $message = 'New Album created';
            $statusCode = '200';
            $album = new Album;
            $album->user_id = auth()->user()->id;
            $album->name = $request->name;
            $album->type = $request->type;
            $album->save();
        }

        return apiResponse($data, $message, $statusCode);
    }

    public function getAlbum()
    {
        $message = 'All Albums of current user';
        $album = Album::where('user_id', auth()->user()->id)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'items' => $item->media->count(),
                'cover' => $item->media->take(1)->map(function ($picture) {
                    return  getMediaUrl($picture->path);
                }),
            ];
        });
        $data['album'] = $album;
        return apiResponse($data, $message, 200);
    }

    public function editAlbum(Request $request, $id)
    {
        if (Album::where('id', $id)->exists()) {
            $request->validate([
                'name' => ['required'],
            ]);
            $message = 'Album name Updated successfully';
            $album = Album::find($id);
            $album->name = is_null($request->name) ? $album->name : $request->name;
            $album->save();
            $data = [];

            return apiResponse($data, $message, 200);
        } else {
            $data = [];
            $message = 'Album not found';
            return apiResponse($data, $message, 404);
        }
    }

    public function deleteAlbum($id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $userAlbum = Album::findorfail($id);
            if ($userAlbum->media->isNotEmpty()) {
                $userAlbum->media->where('reference_type', null)
                    ->map(function ($item, $key) {
                        if (!deleteMedia($item->path)) {
                            $data = [];
                            $statusCode = 404;
                            $message = "Resource Not Found";
                            return apiResponse($data, $message, $statusCode);
                        }
                    });
            }
            $userAlbum->delete();
            $message = "Successfully deleted";
        } catch (ModelNotFoundException $exception) {
            $message = "Album does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    public function showPicture($id)
    {
        if (AlbumMedia::where('album_id', $id)->exists()) {
            $name = Album::where('id', $id)->get()->first()->name;
            $message = 'These are pictures for ' . $name;
            $statusCode = '200';
            $data = AlbumMedia::where('album_id', $id)->get()->map(function ($item) {
                $media = [
                    'id' => $item->id,
                    'path' => getMediaUrl($item->path),
                    'type' => $item->media_type,
                ];
                return [
                    'media' => [$media],
                ];
            });
        } else {
            $data = [];
            $message = 'Invalid Album';
            $statusCode = '200';
        }

        return apiResponse($data, $message, $statusCode);
    }

    public function postDetail()
    {
        $message = 'These are post media details';
        $id = auth()->user()->id;
        $data = Post::where('user_id', $id)->get()->map(function ($media) {
            $data = [
                'id' => $media->id,
                'content' => $media->content,
                'media' => AlbumMedia::where('reference_id', $media->id)->get()->map(function ($item, $key) {
                    return [
                        'id' => $item->id,
                        'path' => $item->path,
                        'type' => $item->media_type,
                    ];
                })
            ];
            return $data;
        });
        return apiResponse($data, $message, 200);
    }
}
