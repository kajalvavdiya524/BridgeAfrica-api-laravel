<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_type',
        'reference_id',
        'media_url',
        'media_type',
        'user_album_id'
        ];
}
