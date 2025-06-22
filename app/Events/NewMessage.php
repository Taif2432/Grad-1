<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        \Log::info('NewMessage event constructed', ['message_id' => $message->id]);
        $this->message = $message;
    }

    public function broadcastOn()
    {
        \Log::info('broadcastOn called', ['session_id' => $this->message->session_id]);
        return new PresenceChannel('session.' . $this->message->session_id);
    }

    public function broadcastWith()
    {
        \Log::info('broadcastWith called', ['message' => $this->message->message]);

        return [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_name' => $this->message->sender->name,
            'created_at' => $this->message->created_at->toDateTimeString(),
        ];
    }
}