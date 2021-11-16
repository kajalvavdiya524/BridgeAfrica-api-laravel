<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostView extends Model
{
    use HasFactory;
    protected $table = "post_views";
    protected $guarded = ['id'];
    protected $fillable = [
        'user_id',
        'post_id'
    ];
}
