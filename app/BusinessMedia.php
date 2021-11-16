<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_type',
        'reference_id',
        'media_url',
        'media_type',
        'business_id',
        'business_album_id',
        'is_shared'
    ];

    public function businessAlbum()
    {
        return $this->belongsTo(BusinessAlbum::class);
    }
}
