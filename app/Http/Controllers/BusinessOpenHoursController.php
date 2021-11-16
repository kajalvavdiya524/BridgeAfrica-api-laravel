<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class BusinessOpenHoursController extends Controller
{
    use HasFactory;

    protected $fillable = [
        'day',
        'opening_time',
        'closing_time'
    ];
}
