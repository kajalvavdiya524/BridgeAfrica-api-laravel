<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    use Hasfactory;
    protected $table = 'notifications';
    protected $guarded = [ 'id' ];
    protected $fillable = [
        'is_read',
        'user_id',
        'reference_type',
        'reference_id',
        'notification_text',
        'mark_as_read',
        'created_at'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}