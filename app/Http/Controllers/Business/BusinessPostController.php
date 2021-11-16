<?php

namespace App\Http\Controllers\Business;

use App\Post;
use Exception;
use App\Business;
use App\PostLike;
use App\PostComment;
use App\BusinessAlbum;
use App\BusinessMedia;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use function PHPUnit\Framework\isEmpty;

class BusinessPostController extends Controller
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
    public function index($id, $page = 0)
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
        $business = Business::find($id);
        $data = $business->post()->orderByDesc('created_at')->skip($offset)
            ->take($this->limit)
            ->get()
            ->map(function ($item, $key) {
                $media = BusinessMedia::where(['reference_id' => $item->id, 'reference_type' => 'post'])->get();
                $data = [
                    // 'viewed' => postViewed($item->id),
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request, $businessId)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $business = Business::find($businessId);
        $post = $business->post()->create([
            'content' => $request->content,
            'type' => $request->type,
            'user_id' => auth()->user()->id,
        ])->toArray();
        if ($request->media) {
            $uploadedFiles = $request->media;
            $isAlbum = BusinessAlbum::where(['business_id' => $business->id, 'name' => 'post'])->first();
            if ($isAlbum) {
                $isAlbum = $isAlbum->toArray();
                $this->storeFile($uploadedFiles, $post['id'], $isAlbum['id'], $business->id);
            } else {
                $album = BusinessAlbum::create([
                    'name' => 'post',
                    'business_id' => $business->id
                ])->toArray();
                $this->storeFile($uploadedFiles, $post['id'], $album['id'], $business->id);
            }
        }
        $message = "Created Successfuly";
        return apiResponse($data, $message, $statusCode);
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
    public function update(PostRequest $request, $id)
    {
        $data = [];
        $message = " ";
        $statusCode = 200;
        $post = Post::findorfail($id);
        $post->update(['content' => $request->content]);
        //delete any media content if any
        if (!empty($request->deleteImg)) {
            $businessMedia = BusinessMedia::whereIn('id', $request->deleteImg)
                ->get()
                ->map(function ($item, $key) {
                    if (!deleteMedia($item->media_url)) {
                        $message = "The given resource was not found";
                        $statusCode = 404;
                        $data = [];
                        return apiResponse($data, $message, $statusCode);
                    }
                });
            $businessMedia = BusinessMedia::whereIn('id', $request->deleteImg)->delete();
        }
        //store the new media files
        if ($request->media) {
            $uploadedFiles = $request->media;
            $isAlbum = BusinessAlbum::where(['business_id' => $post->business_id, 'name' => 'post'])->first();
            if ($isAlbum) {
                $this->storeFile($uploadedFiles, $post->id, $isAlbum->id, $post->business_id);
            } else {
                $album = BusinessAlbum::create([
                    'name' => 'post',
                    'business_id' => $post->business_id
                ]);
                $this->storeFile($uploadedFiles, $post->id, $album->id, $post->business_id);
            }
        }
        $message = 'Updated Successfully.';
        return apiResponse($data, $message, $statusCode);
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
        $message = " ";
        $statusCode = 200;
        $post = Post::find($id);
        $postMedia = $post->businessMedia->toArray();
        if (!empty($postMedia)) {
            foreach ($postMedia as $file) {
                if (!deleteMedia($file['media_url'])) {
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
     * method to creat media and store.
     *
     * @return \Illuminate\Http\Response
     */
    private function storeFile($uploadedFiles, $postId, $albumId, $business_id)
    {
        foreach ($uploadedFiles as $file) {
            $mime =  $file->getMimeType();
            $path = "public/business/" . $business_id . "/album";
            $path = imageUpload($path, $file);
            $media = BusinessMedia::create([
                'reference_type' => 'post',
                'reference_id' => $postId,
                'media_url'  => $path,
                'media_type' => $mime,
                'business_id' => $business_id,
                'business_album_id' => $albumId
            ]);
        }
    }

    /** Approved the Post
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function approvePost(Request $request, Post $post)
    {
        $dateTime = \Carbon\Carbon::now()->toDateTimeString();
        try {
            $postIDs = $request->all();
            Post::whereIn('id', $postIDs['ids'])->update(['is_approve' => 1, 'approve_datetime' => $dateTime]);
            $data = [];
            $message = 'Post successfully approved.';
            $statusCode = '200';
        } catch (ModelNotFoundException $expection) {
            $data = [];
            $message = 'Post does not exist.';
            $statusCode = '404';
        } catch (Exception $expection) {
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }
}
