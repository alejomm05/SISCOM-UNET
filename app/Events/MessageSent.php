<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Message $message
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('guardians-chat');
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'content' => $this->message->content,
            'file_path' => $this->message->file_path,
            'file_type' => $this->message->file_type,
            'user_id' => $this->message->user_id,
            'admin_id' => $this->message->admin_id,
            'user' => $this->message->authorData(), // guardia o supervisor, con 'role'
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}