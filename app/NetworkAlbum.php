<?php

namespace App;

use App\NetworkMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NetworkAlbum extends Model
{
    use HasFactory;
    protected $fillable = [
        'network_id',
        'name'

    ];

    public function networkMedia()
    {
        return $this->hasMany(NetworkMedia::class,'network_album_id');
    }

    public function network()
    {
        return $this->belongsTo(Network::class);
    }
}
