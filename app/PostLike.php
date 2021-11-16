<?php

namespace App;

use App\Post;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $table = "post_likes";
    protected $guard = ['id'];
    protected $fillable =[
        'post_id',
        'user_id',
    ];

    public function postLike(){
        return $this->belongsTo(Post::class);
    }
}
