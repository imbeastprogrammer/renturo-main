<?php

namespace App\Http\Controllers\Api\V1\Tenants\Client;

use App\Http\Controllers\Api\V1\BaseApiController;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ChatController extends BaseApiController
{
    public function index()
    {
        // Return a list of chats by user id
        $userId = auth()->id();

        // Retrieve all active chat rooms where the user is a participant
        $chats = Chat::whereHas('users', function ($query) use ($userId) {
            $query->where('users.id', $userId)
                ->where('chat_user.is_active', true); // Ensure the user is active in the chat
        })->get();

        return $this->sendSuccessResponse($chats, 'Chat rooms retrieved successfully.', 200);
    }

    public function store(Request $request)
    {
        // Create a new chat
        $validatedData = $request->validate([
            'name' => 'required|string|max:255', // Validate the name field
            'user_ids' => 'required|array', // Ensure user_ids is an array
            'user_ids.*' => 'exists:users,id' // Validate each user ID exists in the users table
        ]);

        // Create a new chat
        $chat = Chat::create(['name' => $validatedData['name']]);
        
        // Get the authenticated user's ID and include it in the chatroom
        $userId = auth()->id();
        $userIds = array_unique(array_merge($validatedData['user_ids'], [$userId]));

        $chat->users()->syncWithPivotValues($userIds, ['is_admin' => false]);
        $chat->users()->updateExistingPivot($userId, ['is_admin' => true]); // Set the creator as admin

        // Associate users with the chat
        $chat->users()->sync($userIds);

        return $this->sendSuccessResponse($chat, 'Chat room created successfully.', 201);
    }

    public function show(Chat $chat)
    {
        // Retrieve the authenticated user's ID
        $userId = auth()->id();

        // Abort if the authenticated user is not a participant of the chat
        abort_if(!$chat->users->contains($userId), 404, 'Chat not found');

        // Eager load messages with the chat
        // (assuming you have a 'messages' relationship defined in your Chat model)
        $chat->load('messages');

        // Return the chat
        return $this->sendSuccessResponse($chat, 'Chat room retrieved successfully.', 200);
    }

    public function update(Request $request, Chat $chat)
    {
        // Retrieve the authenticated user's ID
        $userId = auth()->id();

        // Check if the authenticated user is the chat's admin
        $isAdmin = $chat->users->contains(function ($user) use ($userId) {
            return $user->id == $userId && $user->pivot->is_admin;
        });

        if (!$isAdmin) {
            return $this->sendErrorResponse('You are not authorized to update this chat.', 401);
        }

        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Update the chat name
        $chat->update(['name' => $validatedData['name']]);

        // Return the updated chat
        return $this->sendSuccessResponse($chat, 'Chat room updated successfully.', 200);
    }

    public function leaveChat(Chat $chat)
    {
        $userId = auth()->id();

        // Check if the user is part of the chat
        $user = $chat->users()->withPivot('is_admin', 'is_active')->find($userId);

        if (!$user || $user->pivot->is_active === false) {
            return $this->sendErrorResponse('Not a participant of the chat or already left.', 401);
        }

        // Check if the user is an admin
        $isAdmin = $user->pivot->is_admin;

        // Mark the user as having left the chat
        $chat->users()->updateExistingPivot($userId, 
            [
                'is_active' => false, 
                'left_at' => now(),
                'is_admin' => false
            ]);

        // If the user is an admin, try to assign a new admin
        if ($isAdmin) {
            $newAdmin = $chat->users()
                            ->where('users.id', '!=', $userId)
                            ->wherePivot('is_active', true)
                            ->first();

            if ($newAdmin) {
                $chat->users()->updateExistingPivot($newAdmin->id, ['is_admin' => true]);
            } else {
                // Handle the scenario when no other active users are available
                // Example: Notify users, disable chat, or delete chat
                // This part depends on your application's requirements
            }
        }

        return $this->sendSuccessResponse($chat, 'User left the chatroom successfully.', 200);
    }

    public function deleteChat(Chat $chat)
    {
        $userId = auth()->id();

        // Check if the user is part of the chat and is active
        $isActiveParticipant = $chat->users()->where('users.id', $userId)
                                            ->wherePivot('is_active', true)
                                            ->exists();

        if (!$isActiveParticipant) {
            return $this->sendErrorResponse('Not a participant of the chat or already left.', 401);
        }

        // Check if the user is an admin
        $isAdmin = $chat->users()->find($userId)->pivot->is_admin;

        // Mark the user as inactive and set the exited_at timestamp
        $chat->users()->updateExistingPivot($userId, [
            'is_active' => false,
            'deleted_at' => now(),
            'is_admin' => false,
            'is_deleted' => true,
        ]);

        // If the user is an admin, try to assign a new admin or handle no active users
        if ($isAdmin) {
            $newAdmin = $chat->users()
                            ->where('users.id', '!=', $userId)
                            ->wherePivot('is_active', true)
                            ->first();

            if ($newAdmin) {
                $chat->users()->updateExistingPivot($newAdmin->id, ['is_admin' => true]);
            } else {
                // Handle scenario when no other active users are available
                // Example: Notify users, disable chat, or delete chat
                // This part depends on your application's requirements
            }
        }

        return $this->sendSuccessResponse($chat, 'User deleted the chat successfully.', 200);
    }

    public function addParticipants(Request $request, Chat $chat) {
        
        $userId = auth()->id();

        // Check if the authenticated user is an admin of the chat
        if (!$this->isAdmin($chat, $userId)) {
            return $this->sendErrorResponse('You are not authorized to add participants to this chat', 401);
        }
        
        // Validation
        $validatedData = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        foreach ($validatedData['user_ids'] as $userId) {
            // Update or create pivot table entry for each user
            $chat->users()->syncWithoutDetaching([$userId => [
                'is_active' => true,
                'left_at' => null,
                'is_deleted' => false, // If you're using an is_deleted flag
                'deleted_at' => null,
            ]]);
        }

        return $this->sendSuccessResponse($chat, 'Participants added successfully', 200);
    }

    public function removeParticipants(Request $request, Chat $chat){

        $adminUserId = auth()->id();

        // Check if the authenticated user is an admin of the chat
        if (!$this->isAdmin($chat, $adminUserId)) {
            return $this->sendErrorResponse('You are not authorized to remove participants from this chat', 401);
        }
    
        // Custom error messages
        $customMessages = [
            'user_ids.*.exists' => 'User ID :input is invalid or does not exist.',
            'user_ids.*.not_in' => 'User ID :input cannot be the admin.',
        ];
    
        // Validate the request
        $validatedData = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => ['exists:users,id', Rule::notIn([$adminUserId])]
        ], $customMessages);
    
        // Set is_active to false for removed participants
        foreach ($validatedData['user_ids'] as $userId) {
            if ($userId != $adminUserId) { // Ensure not removing the admin
                $chat->users()->updateExistingPivot($userId, ['is_active' => false]);
            }
        }
    
        return $this->sendSuccessResponse($chat, 'Participants removed successfully', 200);
    }

    private function isAdmin(Chat $chat, $userId)
    {
        return $chat->users()
                    ->where('users.id', $userId)
                    ->wherePivot('is_admin', true)
                    ->exists();
    }
}
