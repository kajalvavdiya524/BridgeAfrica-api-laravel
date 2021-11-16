<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Business;

class Message extends Model
{
    protected $table = 'messages';
    protected $guarded = [ 'id' ];
    protected $fillable = [
        'is_read',
        'receiver_id',
        'sender_id',
        'message',
        'sender_business_id',
        'receiver_business_id',
        'sender_network_id',
        'receiver_network_id',
        'attachment'
    ];

    protected $with = ['sender', 'receiver', 'senderBusiness', 'receiverBusiness', 'senderNetwork', 'receiverNetwork'];

    // public function scopeBySender($query, $sender){
    //     $query->where('sender_id', $sender);
    // }

    // public function scopeByReceiver($query, $sender){
    //     $query->where('receiver_id', $sender);
    // }

    public function sender(){
        return $this->belongsTo(User::class, 'sender_id')->select(['id', 'name']);
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'receiver_id')->select(['id', 'name']);
    }

    public function senderBusiness(){
        return $this->belongsTo(Business::class, 'sender_business_id')->select(['id','name']);
    }

    public function receiverBusiness(){
        return $this->belongsTo(Business::class, 'receiver_business_id')->select(['id','name']);
    }

    public function senderNetwork(){
        return $this->belongsTo(Network::class, 'sender_network_id')->select(['id','name']);
    }

    public function receiverNetwork(){
        return $this->belongsTo(Network::class, 'receiver_network_id')->select(['id','name']);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'sender_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'id', 'sender_business_id');
    }

    public function network(){
        return $this->belongsTo(Network::class, 'id', 'sender_network_id');
    }

}