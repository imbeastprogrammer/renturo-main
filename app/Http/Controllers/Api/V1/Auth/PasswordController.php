<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenants\Auth\PasswordRequest;

class PasswordController extends Controller
{
    public function update(PasswordRequest $request)
    {
        $request->user()->update([
            'password' => $request->password,
        ]);

        return response()->json([
            'message' => 'Password updated!'
        ], 200);
    }
}
