<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NewMessage implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('session_.' . $this->message->session_id);
    }

    public function broadcastWith()
{
    $session = $this->message->session;
    $sender = $this->message->sender;

    // Apply the same masking logic:
    if ($session->is_anonymous && $sender->id === $session->client_id) {
        $senderName = 'Anonymous Client';
    } else {
        $senderName = $sender->name;
    }

    return [
        'id'          => $this->message->id,
        'message'     => $this->message->message,
        'sender_id'   => $sender->id,
        'sender_name' => $senderName,
        'created_at'  => $this->message->created_at->toDateTimeString(),
    ];
}
}
