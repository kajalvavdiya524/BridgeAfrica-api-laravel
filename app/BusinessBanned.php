<?php

namespace App;

use App\User;
use App\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessBanned extends Model
{
    protected $table = "business_banned";
    use HasFactory;

    protected $fillable = ['business_id','user_id','is_banned'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
