<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class NetworkBanned extends Model
{
    use HasFactory;
    protected $table = 'network_banneds';
    protected $guard = ['id'];
    protected $fillable = [
        'user_id',
        'network_id',
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'id');
    }
}
