<?php

namespace App;

use App\Post;
use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $table = "post_comments";
    protected $guard = ['id'];
    protected $fillable = [
        'post_id',
        'user_id',
        'comment',
    ];
    public function postComment(){
        return $this->belongsTo(Post::class);
    }
}
