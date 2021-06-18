<?php

namespace App\Events;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class PrivateMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $message;
    public $conversation;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message ,Conversation $conversation)
    {
        $this->message = $message;
        $this->conversation = $conversation;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
         return new PrivateChannel('chat.'.$this->conversation->id);
    }
}
