<?php

namespace App\Events\Chat;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageId;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($messageId, $userId)
    {
        $this->messageId = $messageId;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->getChatIdForMessage($this->messageId));
    }

    /**
     * Determine the chat ID for the given message.
     * You need to implement this method based on your application's logic.
     *
     * @param mixed $messageId
     * @return int
     */
    private function getChatIdForMessage($messageId)
    {
        // Implement logic to retrieve chat ID based on message ID
        $message = Message::find($messageId);

        if($message) {
            return $message->chat_id;
        }

        return null;
    }
}
