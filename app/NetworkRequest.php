<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkRequest extends Model
{
    use HasFactory;
    protected $table = 'network_requests';
    protected $guard = ['id'];
    protected $fillable = [
        'user_id',
        'network_id',
        'status',
    ];
}
