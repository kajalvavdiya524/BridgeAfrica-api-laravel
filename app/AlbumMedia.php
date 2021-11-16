<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\UserAlbum;
use App\Post;

class AlbumMedia extends Model
{
    use HasFactory;
    protected $table = "album_media";
    protected $guard = ['id'];
    protected $fillable = [
        'user_id',
        'album_id',
        'reference_id',
        'reference_type',
        'path',
        'media_type',
    ];

    public function album(){
        return $this->belongsTo(Album::class);
    }
    public function post(){
        return $this->belongsTo(Post::class);
    }
    
}
