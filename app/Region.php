<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country_id'
    ];

    protected $hidden = ['pivot'];

    public function division()
    {
        return $this->hasMany(Division::class);
    }
}
