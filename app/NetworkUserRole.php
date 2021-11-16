<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkUserRole extends Model
{
    use HasFactory;
    protected $table = "network_user_roles";
    protected $guard = ['id'];
    protected $fillable = [
        'user_id',
        'network_id',
        'network_role_id',
    ];
}
