<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserContact extends Model
{
    protected $guard = [];

    protected $fillable = [
        'phone_number',
        'type'
    ];

    public function user()
    {
        return $this->belongTo(User::class);
    }
}
