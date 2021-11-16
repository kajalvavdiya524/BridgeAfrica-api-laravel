<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessRole extends Model
{
    protected $table = "business_roles";
    use HasFactory;

    protected $fillable = ['role_name'];
}
