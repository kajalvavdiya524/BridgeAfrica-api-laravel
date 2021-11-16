<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MessageRecipients extends Model
{
    protected $table = 'message_recipients';
    protected $guarded = [ 'id' ];
    protected $fillable = [
        'user_id',
        'thread_id',
        'unread_count',
        'sender_only',
        'is_deleted',
    ];

}