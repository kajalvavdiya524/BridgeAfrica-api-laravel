<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessAlbum extends Model
{
    use HasFactory;
    protected $table = "business_albums";
    protected $guard = ['id'];
    protected $fillable = [
        'name',
        'business_id'
    ];

    public function businessMedia()
    {
        return $this->hasMany(BusinessMedia::class, 'business_album_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
