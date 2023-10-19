<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Tenants\Auth\RegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        $accessToken = $user->createToken('personal-access-token')->accessToken;

        event(new Registered($user));

        return response()->json([
            'message' => 'Registration complete!',
            'access_token' => $accessToken
        ], 201);
    }
}
