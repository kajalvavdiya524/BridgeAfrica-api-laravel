<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserWebsite extends Model
{
    protected $guard = [];

    public function user()
    {
        return $this->belongTo(User::class);
    }
}
