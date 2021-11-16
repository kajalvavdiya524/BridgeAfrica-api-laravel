<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCommunity extends Model
{
    use HasFactory;
    protected $table = 'business_community';

    public function business(){
        return $this->belongsTo(Business::class);
    }
}