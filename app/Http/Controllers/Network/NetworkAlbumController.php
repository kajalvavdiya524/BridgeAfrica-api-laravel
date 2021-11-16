<?php

namespace App\Http\Controllers\Network;

use App\Post;

use Exception;
use App\Network;
use App\NetworkAlbum;
use App\NetworkMedia;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NetworkAlbumController extends Controller
{
    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }

    /**
     * ok
     * affiche la liste des albums avec une photo de couverture pour chaque album
     * ->sortByDesc('created_at')
     */
    public function index($networkId)
    {
        try {
            $data = [];
            $message = " ";
            $statusCode = 200;
            $network = Network::findOrFail($networkId);
            $data = $network->networkAlbum->where('name', '<>', 'post')->map(function ($album, $key) {

                $data = [
                    'album_id' => $album->id,
                    'album_name' => $album->name,
                    'album_cover' => $album->networkMedia->take(1)->map(function ($media, $key) {
                        return getMediaUrl($media->media_url);
                    }),
                    'almbum_item_number' => $album->networkMedia->count(),
                ];
                return $data;
            });
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Album does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * ok
     * Afficher tous les medias d'un album
     */

    public function show($networkId, $albumId)
    {
        try {
            $data = [];
            $message = " ";
            $statusCode = 200;
            $networkAlbum = NetworkAlbum::findorfail($albumId);
            $data = [
                'album_id' => $networkAlbum->id,
                'media' => $networkAlbum->networkMedia->map(function ($media, $key) {
                    $data = [
                        'id' => $media->id,
                        'media_url' => getMediaUrl($media->media_url),
                        'media_type' => $media->media_type
                    ];
                    return $data;
                })
            ];
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Album does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * ok
     * creation d'un album
     */

    public function store(Request $request, $networkId)
    {
        try {
            $request->validate([
                    'name' => 'required|string|max:255'
                ]);
            $data = [];
            $message = " ";
            $statusCode = 201;
            $network = Network::findorfail($networkId);
            $network->networkAlbum()->create([
                'name' => $request->name
            ]);
            $message = "Successfully created";
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Album does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * ok
     * modifier l'album
     */

    public function update(Request $request, $networkId, $albumId)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255'
            ]);
            $data = [];
            $message = " ";
            $statusCode = 200;
            $networkAlbum = NetworkAlbum::findorfail($albumId);
            $networkAlbum->update([
                'name' => $request->name
            ]);
            $message = "Successfully updated";
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Album does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * ok
     * supprimer l'album
     */

    public function destroy($networkId, $albumId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $networkAlbum = NetworkAlbum::findorfail($albumId);

            if ($networkAlbum->networkMedia->isNotEmpty()) {
                $networkAlbum->networkMedia->where('reference_type', null)
                    ->map(function ($item, $key) {
                        if ($item->is_shared !== "1") {
                            if (!deleteMedia($item->media_url)) {
                                $data = [];
                                $statusCode = 404;
                                $message = "Resource Not Found";
                                return apiResponse($data, $message, $statusCode);
                            }
                        }
                    });
            }
            $networkAlbum->delete();
            $message = "Successfully deleted";
        } catch (ModelNotFoundException $exception) {
            $message = "Album does not exist";
            $statusCode = 404;
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * ok
     * Afficher tous les médias d'un article avec son contenu
     */

    public function showPostMedia($networkId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        try {
            $network = Network::find($networkId);
            $data = $network->post->map(function ($post, $key) {
                $data = [
                    'id' => $post->id,
                    'media' => NetworkMedia::where('reference_id', $post->id)->get()->map(function ($item, $key) {
                        $data = [
                            "post_id" => $item->reference_id,
                            'post_content' => Post::where('id', $item->reference_id)->get()->map(function ($i, $key) {
                                return $i->content;
                            }),
                            'media_id' => $item->id,
                            'media_url' => getMediaUrl($item->media_url),
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
