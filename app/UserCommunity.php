<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCommunity extends Model
{
    use HasFactory;
    protected $table = "user_community";
    protected $guard = ['id'];
    protected $fillable = [
        'user_id',
        'follower_id',
        'follower_type',
        'type'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id');
    }
}
