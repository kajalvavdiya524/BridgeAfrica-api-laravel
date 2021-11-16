<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkFollower extends Model
{
    use HasFactory;
    protected $table = "network_followers";

    protected $guard = ['id'];

    public function business()
    {
        return $this->hasMany(Business::class);
    }

    protected $fillable = [
        'network_id',
        'follower_id',
    ];
}
