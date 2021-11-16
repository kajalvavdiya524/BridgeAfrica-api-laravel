<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkRole extends Model
{
    use HasFactory;
    protected $table = "network_roles";

    protected $guard = ['id'];
    protected $fillable = ['name'];

    public function networkRole(){
        return $this->hasMany(NetworkRole::class, 'network_role_id');
    }
}
