<?php

namespace App\Http\Controllers\Network;


use App\Post;
use App\Network;
use App\PostLike;
use App\PostComment;
use App\NetworkAlbum;
use App\NetworkMedia;
use App\NetworkUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\NetworkPostRequest;
use App\NetworkUser;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends Controller
{
    private $limit;
    private $offset;

    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }

    /**
     * ok
     */
    public function show($id, $page = 0)
    {

        $data = [];
        $message = " ";
        $statusCode = 200;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $offset = ($page - 1) * $this->limit;
        $network = Network::find($id);
        $data = $network->post()->orderByDesc('created_at')->skip($offset)
            ->take($this->limit)
            ->get()
            ->map(function ($item, $key) {
                $media = NetworkMedia::where(['reference_id' => $item->id, 'reference_type' => 'post'])->get();
                $data = [
                    'network_name' => $item->network->name,
                    'logo_path' => $item->network->image,
                    'post_id' => $item->id,
                    'created_at' => $item->created_at,
                    'content' => $item->content,
                    'media' => $media->map(function ($item) {
                        $data = [
                            'id' => $item->id,
                            'media_url' => getMediaUrl($item->media_url),
                            'media_type' => $item->media_type
                        ];
                        return $data;
                    }),
                    'is_liked' => ($item->likes->where('user_id', auth()->user()->id)->isEmpty()) ? 0 : 1,
                    'likes_count' => $item->likes->count(),
                    'comment_count' => $item->comment->count(),
                    'comments' => $item->comment->map(function ($comment, $key) {
                        $commentInfo = [
                            'text' => $comment->comment,
                            'post_id' => $comment->post_id,
                            'user_id' => $comment->user_id,
                        ];
                        return $commentInfo;
                    }),
                ];
                return $data;
            });
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * ok
     */
    public function store(NetworkPostRequest $request, $networkId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $network = Network::find($networkId);
        $post = $network->post()->create([
            'content' => $request->content,
            'type' => $request->type,
            'user_id' => auth()->user()->id,
        ])->toArray();
        if ($request->media) {
            $uploadedFiles = $request->media;
            $isAlbum = NetworkAlbum::where(['network_id' => $network->id, 'name' => 'post'])->first();
            if ($isAlbum) {
                $isAlbum = $isAlbum->toArray();
                $this->storeFile($uploadedFiles, $post['id'], $isAlbum['id'], $network->id);
            } else {
                $album = NetworkAlbum::create([
                    'name' => 'post',
                    'network_id' => $network->id
                ])->toArray();
                $this->storeFile($uploadedFiles, $post['id'], $album['id'], $network->id);
            }
        }
        $message = "Created Successfuly";
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * ok
     * display the information of the post you want to delete
     */


    public function getPostUpdate(Request $request, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $post = Post::where('id', $id)->get()->map(function ($item, $key) {
            $media = $media = NetworkMedia::where(['reference_id' => $item->id, 'reference_type' => 'post'])->get();
            $data = [
                'id' => $item->id,
                'content' => $item->content,
                'media' => $media->map(function ($item) {
                    $data = [
                        'id' => $item->id,
                        'media_url' => getMediaUrl($item->media_url),
                        'media_type' => $item->media_type
                    ];
                    return $data;
                }),

            ];
            return $data;
        });
        $data = $post;
        return apiResponse($data, $message, $statusCode);
    }


    /**
     *ok
     *deleteImg is an array that takes as parameters the id of the images to delete
     */

    public function update(NetworkPostRequest $request, $id)
    {

        $data = [];
        $message = " ";
        $statusCode = 200;

        try{
            $post = Post::findorfail($id);

            if($post->user_id != auth()->user()->id){
                $message = "Unauthorised access to resource";
                return apiResponse($data,$message,$statusCode);
            }

            $post->update(['content' => $request->content]);

            //delete any media content if any
            if (!empty($request->deleteImg)) {
                $networkMedia = NetworkMedia::whereIn('id', $request->deleteImg)
                    ->get()
                    ->map(function ($item, $key) {
                        //we check if the image is not yet shared
                        if($item->is_shared !== "1"){
                            if (!deleteMedia($item->media_url)) {
                                $message = "The given resource was not found";
                                $statusCode = 404;
                                $data = [];
                                return apiResponse($data, $message, $statusCode);
                            }
                        }

                    });
                $networkMedia = NetworkMedia::whereIn('id', $request->deleteImg)->delete();
            }
            //store the new media files
            if ($request->media) {
                $uploadedFiles = $request->media;
                $isAlbum = NetworkAlbum::where(['network_id' => $post->network_id, 'name' => 'post'])->first();
                if ($isAlbum) {
                    $this->storeFile($uploadedFiles, $post->id, $isAlbum->id, $post->network_id);
                } else {
                    $album = NetworkAlbum::create([
                        'name' => 'post',
                        'network_id' => $post->network_id
                    ]);
                    $this->storeFile($uploadedFiles, $post->id, $album->id, $post->network_id);
                }
            }
            $message = 'Updated Successfully.';

        }catch(ModelNotFoundException $exception){
            $data = [];
            $message = 'Post does not exist.';
            $statusCode = 404;
            return apiResponse($data,$message,$statusCode);
        }
        return apiResponse($data, $message, $statusCode);
    }


    /**
     * ok
     */
    public function destroy($id)
    {
        try{
            $data = [];
            $message = " ";
            $statusCode = 200;
            $post = Post::find($id);

            if($post->user_id != auth()->user()->id){
                $message = "Unauthorised access to resource";
                return apiResponse($data,$message,$statusCode);
            }

            $postMedia = $post->networkMedia->toArray();
            if (!empty($postMedia)) {

                foreach ($postMedia as $file) {
                    if($file['is_shared'] !== "1"){
                        if (!deleteMedia($file['media_url'])) {
                            $message = "The given resource was not found";
                            $statusCode = 404;
                            return apiResponse($data, $message, $statusCode);
                        }
                    }

                }
            }
            NetworkMedia::where('reference_id', $post->id)->delete();
            $post->delete();
            $message = "Deleted Successfully";

        }catch(ModelNotFoundException $exception){
            $data = [];
            $message = 'Post does not exist.';
            $statusCode = 404;
            return apiResponse($data,$message,$statusCode);
        }
        return apiResponse($data, $message, $statusCode);
    }



    /**
     * ok
     * method to creat media and store.
     *
     * @return \Illuminate\Http\Response
     */
    private function storeFile($uploadedFiles, $postId, $albumId, $network_id)
    {
        foreach ($uploadedFiles as $file) {
            $mime =  $file->getMimeType();
            $path = "public/network/" . $network_id . "/album";
            $path = imageUpload($path, $file);
            $media = NetworkMedia::create([
                'reference_type' => 'post',
                'reference_id' => $postId,
                'media_url'  => $path,
                'media_type' => $mime,
                'network_id' => $network_id,
                'network_album_id' => $albumId
            ]);
        }
    }
}
