<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserSocialProfile extends Model
{
    protected $table        = "user_social_profiles";
    protected $guard = [];

    public function user()
    {
        return $this->belongTo(User::class);
    }
}
