<?php

namespace App\Events\Chat;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $chatId;

    public function __construct(User $user, $chatId)
    {
        $this->user = $user;
        $this->chatId = $chatId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->chatId);
    }
}
