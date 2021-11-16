<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PrivacySetting extends Model
{
    protected $table 		= "privacy_settings";

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
