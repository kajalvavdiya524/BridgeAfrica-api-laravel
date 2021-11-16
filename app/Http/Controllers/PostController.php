<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use App\Album;
use Exception;
use App\PostLike;
use App\AlbumMedia;
use App\PostComment;
use App\Traits\Transformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\UserFollower;
use App\BusinessFollower;
use App\Business;
use App\BusinessMedia;
use App\UserCommunity;

class PostController extends Controller
{
    private $limit;
    private $offset;

    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $page = 0)
    {
        $id = auth()->user()->id;
        if ($request->id) {
            $id = $request->id;
        }
        $data = [];
        $message = 'listing of posts';
        $statusCode = 200;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid expected integer but recieve \"" . $page;
            return apiResponse($data, $message, $statusCode);
        }
        $offset = ($page - 1) * $this->limit;
        $data = Post::where('user_id', $id)->orderByDesc('created_at')->skip($offset)
            ->take($this->limit)->get()->map(function ($item, $key) {
                $media = AlbumMedia::where(['reference_id' => $item->id, 'reference_type' => 'post'])->get();
                $data = [
                    'content' => $item->content,
                    'media' => $media->map(function ($item) {
                        $data = [
                            'id' => $item->id,
                            'media_url' => getMediaUrl($item->path),
                            'media_type' => $item->media_type
                        ];
                        return $data;
                    }),
                    'type' => $item->type,
                    'post_id' => $item->id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'profile_picture' => $item->user->profile_picture,
                    'name' => $item->user->name,
                    'is_liked' => $item->likes->where('user_id', auth()->user()->id)->count(),
                    'likes_count' => $item->likes->count(),
                    'comment_count' => $item->comment->count(),
                    'comments' => $item->comment->map(function ($comment, $key) {
                        $data = [
                            'text' => $comment->comment,
                            'post_id' => $comment->post_id,
                            'user_id' => $comment->user_id,
                        ];
                        return $data;
                    }),
                ];
                return $data;
            });

        return apiResponse($data, $message, 200);
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
            'content' => ['nullable', 'string', 'max:255'],
            'media.*' => ['nullable', 'file', 'mimes:png,jpg,gif,mp4']
        ]);

        $data = [];
        $message = 'the post have been saved';

        $post = Post::create([
            'user_id' => auth()->user()->id,
            'content' => $request->content,
            'type' => 'text'
        ]);
        if ($request->media) {
            $uploadedFiles = $request->media;
            $isAlbum = Album::where(['user_id' => auth()->user()->id, 'name' => 'post'])->first();
            if (!empty($isAlbum)) {
                $this->storeFile($uploadedFiles, $post->id, $isAlbum->id);
            } else {

                $postAlbum = Album::create([
                    'name' => 'post',
                    'user_id' => auth()->user()->id,
                    'type' => 'post'
                ]);
                $this->storeFile($uploadedFiles, $post->id, $postAlbum->id);
            }
        }
        return apiResponse($data, $message, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $post = Post::findorfail($id);
            $data = Transformer::post($post);
            $message = 'Post details successfully';
            $statusCode = '200';
        } catch (ModelNotFoundException $exception) {
            $data = [];
            $message = 'Post does not exists!';
            $statusCode = '404';
        } catch (Exception $exception) {
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $post = Post::findorfail($id);
        try {
            $post->update(['content' => $request->content]);
            //delete any media content if any
            if (!empty($request->deleteImg)) {
                $userMedia = AlbumMedia::whereIn('id', $request->deleteImg)
                    ->get()
                    ->map(function ($item, $key) {
                        if (!$item->is_shared) {
                            if (!deleteMedia($item->path)) {
                                $message = "The given resource was not found";
                                $statusCode = 404;
                                $data = [];
                                return apiResponse($data, $message, $statusCode);
                            }
                        }
                    });
                $userMedia = AlbumMedia::whereIn('id', $request->deleteImg)->delete();
            }
            //store the new media files
            if ($request->media) {
                $uploadedFiles = $request->media;
                $isAlbum = Album::where(['user_id' => auth()->user()->id, 'name' => 'post'])->first();
                if ($isAlbum) {
                    $this->storeFile($uploadedFiles, $post->id, $isAlbum->id);
                } else {
                    $album = Album::create([
                        'name' => 'post',
                        'business_id' => $post->business_id
                    ]);
                    $this->storeFile($uploadedFiles, $post->id, $album->id);
                }
            }
            $message = 'Updated Successfully.';
        } catch (ModelNotFoundException $exception) {
            $data = [];
            $message = 'Post does not exists!';
            $statusCode = '404';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $post = Post::find($id);
        $postMedia = AlbumMedia::where(['reference_id' => $id, 'reference_type' => 'post', 'is_shared' => false])->get();
        if (!empty($postMedia)) {
            foreach ($postMedia as $file) {
                if (!deleteMedia($file->path)) {
                    $message = "The given resource was not found";
                    $statusCode = 404;
                    return apiResponse($data, $message, $statusCode);
                }
            }
        }
        BusinessMedia::where('reference_id', $post->id)->delete();
        $post->delete();
        $message = "Deleted Successfully";
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Display a listing of the business resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboardPostBusiness($id, $page = 0)
    {
        $data = [];
        $message = 'listing of posts';
        $statusCode = 200;
        $offset = ($page - 1) * $this->limit;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $businessCommunity = UserCommunity::where(['follower_id' => $id, 'follower_type' => 'business', 'type' => 'follower'])->get('business_id');
        $data = Post::where('business_id', $id)->orWhereIn('business_id', $businessCommunity)->orderByDesc('created_at')->skip($offset)
            ->take($this->limit)
            ->get()
            ->map(function ($item, $key) {
                $media = BusinessMedia::where(['reference_id' => $item->id, 'reference_type' => 'post'])->get();
                $data = [
                    'bussines_name' => $item->business->name,
                    'logo_path' => $item->business->logo_path,
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
     * Display a listing of the user resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboardPostUser($page = 0)
    {
        $data = [];
        $message = 'listing of posts';
        $statusCode = 200;
        $offset = ($page - 1) * $this->limit;
        if (!is_numeric($page)) {
            $statusCode = 422;
            $message = "url parameter type not valid";
            return apiResponse($data, $message, $statusCode);
        }
        $userCommunity = UserCommunity::where(['follower_id' => auth()->user()->id, 'follower_type' => 'user', 'type' => 'follower'])->get('user_id');
        $data = Post::where('user_id', auth()->user()->id)->orWhereIn('user_id', $userCommunity)->orderByDesc('created_at')->skip($offset)
            ->take($this->limit)->get()->map(function ($item, $key) {
                $media = AlbumMedia::where(['reference_id' => $item->id, 'reference_type' => 'post'])->get();
                $data = [
                    'content' => $item->content,
                    'type' => $item->type,
                    'post_id' => $item->id,
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                    'profile_picture' => $item->user->profile_picture,
                    'name' => $item->user->name,
                    'media' => $media->map(function ($item) {
                        $data = [
                            'id' => $item->id,
                            'media_url' => $item->path,
                            'media_type' => $item->media_type
                        ];
                        return $data;
                    }),
                    'is_liked' => $item->likes->where('user_id', auth()->user()->id)->count(),
                    'likes_count' => $item->likes->count(),
                    'comment_count' => $item->comment->count(),
                    'comments' => $item->comment->map(function ($comment, $key) {
                        $data = [
                            'text' => $comment->comment,
                            'post_id' => $comment->post_id,
                            'user_id' => $comment->user_id,
                        ];
                        return $data;
                    }),
                ];
                return $data;
            });
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * method to create media and store.
     *
     * @return \Illuminate\Http\Response
     */
    private function storeFile($uploadedFiles, $postId, $albumId)
    {
        foreach ($uploadedFiles as $file) {
            $mime =  $file->getMimeType();
            $path = "public/user/" . auth()->user()->id . "/album";
            $path = imageUpload($path, $file);
            $media = AlbumMedia::create([
                'user_id' => auth()->user()->id,
                'album_id' => $albumId,
                'reference_id' => $postId,
                'reference_type' => 'post',
                'path' => $path,
                'media_type' => $mime
            ]);
        }
    }
}
