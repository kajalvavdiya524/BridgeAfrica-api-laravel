<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filter extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'subcat_id',
        'name'
    ];
    
    protected $hidden = ['pivot'];

    public function businesses()
    {
        return $this->belongsToMany(Business::class);
    }

    public function markets()
    {
        return $this->hasMany(Market::class);
    }
}
