<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'cat_id',
        'name',
        'cat_image'
    ];

    protected $hidden = ['pivot'];

    public function getCatImageAttribute($value)
    {
        if($value){
            return env('APP_URL') . ':' . $_SERVER['SERVER_PORT'] . $value;
        }
        return null;    
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class);
    }
    
    public function filters()
    {
        return $this->hasMany(Filter::class,'subcat_id');
    }
}
