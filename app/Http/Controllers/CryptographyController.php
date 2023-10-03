<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class CryptographyController extends Controller
{
    public function encrypt(Request $request) {

        $data = $request->input('data');
        $publicKey = file_get_contents(storage_path('app/keys/public_key.pem'));

        $encryptedData = Crypt::encryptString($data, $publicKey);

        return response()->json([
            'encrypted_data' => $encryptedData, 
        ]);
    }

    public function decrypt(Request $request) {

        $data = $request->input('data'); 

        $privateKey = file_get_contents(storage_path('app/keys/private_key.pem'));

        $decryptedData = Crypt::decryptString($data, $privateKey);

        return response()->json([
            'decrypted_data' => $decryptedData
        ]);
    }
}
