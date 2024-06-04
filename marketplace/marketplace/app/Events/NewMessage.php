<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\User;

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $senderName;

    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->senderName = User::find($message->sender_id)->name; 
    }

    public function broadcastOn()
    {
        return new Channel('messages');
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'sender_name' => $this->senderName,
        ];
    }

    public function broadcastAs()
    {
        return 'NewMessage'; 
    }
}
