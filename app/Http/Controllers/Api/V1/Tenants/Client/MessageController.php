<?php

namespace App\Http\Controllers\API\V1\Tenants\Client;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Events\UserTyping;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Send a new message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendMessage(Request $request) {

        $request->validate([
            'chat_id' =>'required',
            'message' =>'required',
        ]);

        // Validate if the user exists
        $userId = $request->user()->id;

        if (!User::find($userId)) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $message = Message::create([
            'user_id' => $userId,
            'chat_id' => $request->chat_id,
            'message' => $request->message,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    public function getMessage() {
    }

    public function markAsRead() {
    }

    public function userTyping() {
    }
}
