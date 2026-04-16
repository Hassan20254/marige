<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewMessageNotification implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $notification;

    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->notification['receiver_id']);
    }

    public function broadcastAs()
    {
        return 'new-message-notification';
    }

    public function broadcastWith()
    {
        return [];
    }
}
