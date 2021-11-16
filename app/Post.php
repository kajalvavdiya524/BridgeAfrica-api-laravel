<?php

namespace App;


use App\User;
use App\Network;
use App\PostLike;
use App\PostMedia;
use App\PostComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\AlbumMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    protected $table = 'posts';

    protected $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'content',
        'type',
        'visit',
        'business_id',
        'network_id',
        'is_approve',
        'approve_datetime',
    ];

    public function albumMedia()
    {
        return $this->hasMany(AlbumMedia::class);
    }

    public function comment()
    {
        return $this->hasmany(PostComment::class);
    }
    public function likes()
    {
        return $this->hasmany(PostLike::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'id');
    }

    public function network(){
        return $this->belongsTo(Network::class, 'network_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function businessMedia()
    {
        return $this->hasMany(BusinessMedia::class,'reference_id');
    }
    
    public function shares()
    {
        return $this->hasMany(PostShare::class);
    }

    public function networkMedia(){
        return $this->hasMany(NetworkMedia::class,'reference_id');
    }
}
