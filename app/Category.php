<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'cat_image'
    ];


    public function subCategory()
    {
        return $this->hasMany(Subcategory::class, 'cat_id');
    }

    public function markets()
    {
        return $this->hasMany(Market::class);
    }

    protected $hidden = ['pivot'];
}
