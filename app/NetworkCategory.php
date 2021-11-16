<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Network;
use App\networkFollower;

class NetworkCategory extends Model
{
    use HasFactory;
    protected $table = 'network_categories';
    protected $guard = ['id'];
    protected $fillable = [
        'title',
    ];

    public function network(){
        return $this->hasMany(Network::class);
    }

    public function networkFollower()
    {
        return $this->hasMany(NetworkFollower::class);
    }
}
