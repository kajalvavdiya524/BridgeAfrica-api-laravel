<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\AlbumMedia;

class Album extends Model
{
    use HasFactory;
    protected $table = "albums";
    protected $guard = ['id'];
    protected $fillable = [
        'user_id',
        'name',
        'type',
    ];

    public function media(){
        return $this->hasMany(AlbumMedia::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
