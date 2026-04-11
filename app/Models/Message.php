<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'body',
        'is_read'
    ];

    // علاقة لجلب بيانات المرسل
    public function sender()
    {
        return $this->belongsTo(Dataforuser::class, 'sender_id');
    }

    // علاقة لجلب بيانات المستلم
    public function receiver()
    {
        return $this->belongsTo(Dataforuser::class, 'receiver_id');
    }
}