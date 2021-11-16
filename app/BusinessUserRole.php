<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessUserRole extends Model
{
    protected $table = "business_user_roles";
    use HasFactory;

    protected $fillable = ['user_id','business_id','business_role_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
