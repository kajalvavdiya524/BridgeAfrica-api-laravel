<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkUser extends Model
{
    use HasFactory;

    protected $table = 'network_user';
    protected $fillable = [
        'user_id',
        'network_id',
        'network_role_id',
        'is_approve'
    ];

}
