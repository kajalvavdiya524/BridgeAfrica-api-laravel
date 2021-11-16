<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Council extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'division_id'
    ];

    protected $hidden = ['pivot'];

    public function neighborhood()
    {
        return $this->hasMany(Neighborhood::class);
    }
}
