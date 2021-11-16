<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccountPackage extends Model
{
    protected $table = "user_account_packages";
    use HasFactory;

    protected $fillable = ['type','title','price','status'];
}
