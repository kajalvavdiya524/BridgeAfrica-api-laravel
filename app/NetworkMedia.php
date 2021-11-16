<?php

namespace App;

use App\NetworkAlbum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NetworkMedia extends Model
{
    use HasFactory;
    protected $fillable = [
        'network_id',
        'reference_id',
        'media_url',
        'media_type',
        'network_album_id',
        'reference_type',
        'created_at',
        'is_shared',
        'updated_at'

    ];

    public function networkAlbum()
    {
        return $this->belongsTo(NetworkAlbum::class);
    }
}
