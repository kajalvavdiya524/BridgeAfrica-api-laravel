<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOpenHours extends Model
{
    use HasFactory;
    protected $table 		= "business_open_hours";
    protected $fillable = [
        'business_id',
        'day',
        'opening_time',
        'closing_time',
    ];
}
