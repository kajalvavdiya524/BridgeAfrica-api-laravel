<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'region_id'
    ];

    protected $hidden = ['pivot'];

    public function council()
    {
        return $this->hasMany(Council::class);
    }
}