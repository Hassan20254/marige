<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Dataforuser;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $senderName; // المتغير اللي هيشيل اسم المرسل للإشعار

    public function __construct(Message $message)
    {
        $this->message = $message;

        // جلب اسم المرسل من جدول المستخدمين
        $sender = Dataforuser::find($message->sender_id);
        $this->senderName = $sender ? $sender->name : 'مستخدم';
    }

    public function broadcastOn(): array
    {
        // هنستخدم Channel عامة للتجربة لضمان وصول الإشعار في كل الصفحات
        return [
            new Channel('chat.' . $this->message->receiver_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
