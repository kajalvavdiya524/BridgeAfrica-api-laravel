<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserEducation extends Model
{
    protected $table 		= "user_education_details";
    protected $guard = [];

    public function user()
    {
        return $this->belongTo(User::class);
    }
}
