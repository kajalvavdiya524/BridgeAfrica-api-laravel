<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Market extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'items';

    protected $fillable = [
        'name',
        'description',
        'picture',
        'price',
        'on_discount',
        'discount_price',
        'condition',
        'is_service',
        'in_stock',
        'status',
        'business_id'
    ];

    public function business(){
        return $this->belongsTo(Business::class);
    }

    public function categoryMarket()
    {
        return $this->belongsToMany(Category::class);
    }

    public function marketSubcategory(){
        return $this->belongsToMany(Subcategory::class);
    }

    public function filterMarket(){
        return $this->belongsToMany(Filter::class);
    }
}
