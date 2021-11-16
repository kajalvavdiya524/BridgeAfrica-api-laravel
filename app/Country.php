<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    protected $hidden = ['pivot'];

    public function region()
    {
        return $this->hasMany(Region::class);
    }
}
