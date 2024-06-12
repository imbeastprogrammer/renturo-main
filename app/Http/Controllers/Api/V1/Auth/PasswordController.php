<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\Auth\PasswordRequest;
use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{
    public function update(PasswordRequest $request)
    {
        try {
            $request->user()->update([
                'password' => $request->password,
            ]);

            $user = $request->user();

            return response()->json([
                'message' => 'success',
                'body' => [
                  'message' => 'Password updated!',
                  'user' => $user,
                ]
            ], 200);

        } catch (\Exception $e) { 
            Log::error($e->getMessage());
        }
    }
}
