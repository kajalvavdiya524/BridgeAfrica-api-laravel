<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Business;

class BusinessNotification extends Model
{
    use HasFactory;
    protected $table = 'business_notifications';
    protected $guard = ['id'];
    protected $fillable = [
        'is_read',
        'business_id',
        'mark_as_read',
        'notification_text',
        'reference_type',
        'reference_id',
    ];
    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
