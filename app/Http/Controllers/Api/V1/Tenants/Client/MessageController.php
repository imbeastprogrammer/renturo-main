<?php

namespace App\Http\Controllers\API\V1\Tenants\Client;

use App\Http\Controllers\Api\V1\BaseApiController;
use Illuminate\Http\Request;
use App\Events\Chat\MessageSent;
use App\Events\Chat\MessageRead;
use App\Events\Chat\MessageDeleted;
use Illuminate\Support\Str;
use App\Models\Message;
use App\Models\Chat;
use App\Models\User;

class MessageController extends BaseApiController
{
    /**
     * Send a new message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request) {

        $userId = $request->user()->id;

        // Validation for an existing chat
        $validatedData = $request->validate([
            'chat_id' => 'sometimes|exists:chats,id',
            'recipient_ids' => 'sometimes|required|array|exists:users,id',
            'message' => 'required',
        ]);

        $chatId = $validatedData['chat_id'] ?? null;

        if ($chatId) {
            // Scenario 1: Existing Chat
            $chat = Chat::whereHas('users', function ($query) use ($userId) {
                $query->where('users.id', $userId);
            })->findOrFail($chatId);
        } else {
            // Scenario 2: New Chat
            // Create a new chat and attach participants
            $chat = Chat::create([
                'name' => $this->generateChatName($userId, $validatedData['recipient_ids'] ?? []),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Set the user who creates the chat as admin
            $participantsWithRoles = [
                $userId => ['is_admin' => true]
            ];

            // Add other participants as non-admins
            foreach ($validatedData['recipient_ids'] ?? [] as $participantId) {
                $participantsWithRoles[$participantId] = ['is_admin' => false];
            }

            // Sync users with their roles to the chat
            $chat->users()->sync($participantsWithRoles);
        }

        // Create the message in the existing or new chat
        $message = Message::create([
            'user_id' => $userId,
            'chat_id' => $chat->id,
            'message' => $validatedData['message'],
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return $this->sendSuccessResponse($message, 'Message sent successfully');
    }

    /**
     * Generate a chat name based on participants.
     */
    private function generateChatName($userId, array $participantIds)
    {
        if (count($participantIds) === 1) {
            // If there's only one other participant, use the name of the sender or receiver
            return User::find($participantIds[0])->fullName ?? 'chat-' . Str::random(30);
        } else {
            // For multiple participants, use the full names of the first three
            $names = collect($participantIds)
            ->take(3) // Take the first three participants
            ->map(function ($id) {
                $user = User::find($id);
                return $user ? $user->fullName : null;
            })
            ->filter() // Remove any null values
            ->all();

            // Concatenate the names, or return a default name if empty
            return !empty($names) ? implode(', ', $names) : 'Group Chat';
        }
    }

    public function getUnreadMessages($chatId) {

        $userId = auth()->id();
    
        // Get all messages for the chat that the user has not read yet
        $unreadMessages = Message::where('chat_id', $chatId)
            ->whereDoesntHave('reads', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->with('user') // Eager load the user relationship
            ->get();
    
        return $this->sendSuccessResponse($unreadMessages, 'Messages retrieved successfully', 200);
    }

    public function getMessages($chatId) {

        $userId = auth()->id();

        // Check if the user is part of the chat
        $chat = Chat::with('users')->findOrFail($chatId);
        $isParticipant = $chat->users->contains($userId);
        if (!$isParticipant) {
            return $this->sendErrorResponse('Not a participant of the chat.', 403);
        }

        // Retrieve a paginated list of messages for the chat
        $messages = $chat->messages()
            ->withTrashed() // Include soft-deleted messages
            ->with('user') // Eager load the user relationship
            ->orderBy('created_at', 'desc') // Sort messages by creation time
            ->paginate(20); // Paginate by 20 messages per page

        // Find the last read message for each participant
        $lastReadMessages = $chat->users->mapWithKeys(function ($participant) use ($chatId) {
            $lastRead = \App\Models\MessageRead::where('user_id', $participant->id)
                ->whereHas('message', function ($query) use ($chatId) {
                    $query->where('chat_id', $chatId);
                })
                ->latest('read_at')
                ->first();

            return [$participant->id => $lastRead ? $lastRead->message_id : null];
        });

        $data = [
            'messages' => $messages,
            'lastReadMessages' => $lastReadMessages
        ];

        return $this->sendSuccessResponse($data, 'Messages retrieved successfully', 200);
    }

    public function markAsRead($messageId) {

        $userId = auth()->id();

        // Find the message and ensure it belongs to a chat the user is part of
        $message = Message::whereHas('chat.users', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })->findOrFail($messageId);

        // Ensure the current user is not the sender and is a recipient
        if ($message->user_id != $userId) {

            // Insert or update the read status for the user and message
            $readStatus = \App\Models\MessageRead::updateOrCreate(
                ['user_id' => $userId, 'message_id' => $messageId],
                ['read_at' => now()]
            );

            // Broadcast an event that the message has been read
            broadcast(new MessageRead($message->id, $userId))->toOthers();

            return $this->sendSuccessResponse($readStatus, 'Message marked as read');
        } else {
            return $this->sendFailedResponse('Cannot mark own message as read');
        }
    }

    public function deleteMessage($messageId){

        $userId = auth()->id();

        $message = Message::findOrFail($messageId);

        // Check if the user owns the message and if it hasn't been read by anyone
        if ($message->user_id == $userId && !$message->reads()->exists()) {
            $message->delete();

            // Optionally, broadcast an event to update the message status in real time
            broadcast(new MessageDeleted($message->id, $userId))->toOthers();

            return $this->sendSuccessResponse($message, 'Message deleted');
        }

        return $this->sendFailedResponse('Cannot delete this message', 403);
    }
}
