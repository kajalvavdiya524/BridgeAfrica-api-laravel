<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NetworkSetting extends Model
{
    use HasFactory;
    protected $table = "network_settings";
    protected $guard = ['id'];
    protected $fillable = [
        'name',
        'network_id',
        'setting_value',
        'setting_key'
    ];
    public function network(){
        return $this->belongsTo(Network::class, 'network_id');
    }
}
