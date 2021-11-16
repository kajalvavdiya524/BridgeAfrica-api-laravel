<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\AlbumMedia;

class UserAlbum extends Model
{
    use HasFactory;
    protected $table = "user_albums";
    protected $guard = ['id'];
    protected $fillable = [
        'user_id',
        'name',
        'type',
    ];

    public function media(){
        return $this->hasMany(AlbumMedia::class);
    }
   public function userMedia()
   {
       return $this->hasMany(UserMedia::class,'user_album_id');
   }
}
