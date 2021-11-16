<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAddress extends Model
{
    protected $table 		= "user_addresses";
    protected $guard = [];

    public function user()
    {
        return $this->belongTo(User::class);
    }
}
