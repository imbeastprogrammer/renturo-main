<?php

namespace App\Http\Controllers\API\V1\Tenants\Client;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Bank;


class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Retrieve the authenticated user's ID
        $userId = $request->user()->id;

        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string',
            'account_name' => [
                'required',
                'string',
                // Unique rule: unique to user_id, account_number, and account_name combination
                Rule::unique('banks')->where(function ($query) use ($userId, $request) {
                    return $query->where('user_id', $userId)
                                 ->where('account_number', $request->account_number)
                                 ->where('account_name', $request->account_name);
                }),
            ],
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Add the user_id to the validated data
        $validatedData = $validator->validated();
        $validatedData['user_id'] = $userId;

        $bank = Bank::create($validatedData);

        return response()->json($bank, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $bankId)
    {
        // Retrieve the authenticated user's ID
        $userId = $request->user()->id;

        $bank = Bank::where('user_id', $userId)
                ->where('id', $bankId)
                ->first();

        if(!$bank) {
            return response()->json(['message' => 'Bank not found'], 404);
        }

        $bank = [
            "id" => $bank->id,
            "account_number" => $bank->account_number,
            "account_name" => $bank->account_name,
            "is_active" => $bank->is_active,
        ];

        return response()->json($bank);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $bankId)
    {
        // Retrieve the authenticated user's ID
        $userId = $request->user()->id;

        // Retrieve the bank record
        $bank = Bank::where('id', $bankId)->where('user_id', $userId)->first();

        // Check if the bank exists and belongs to the user
        if (!$bank) {
            return response()->json(['message' => 'Bank account not found or access denied'], 404);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'account_number' => 'required|string',
            'account_name' => [
                'required',
                'string',
                // Unique rule: unique to user_id, account_number, and account_name combination
                Rule::unique('banks')->where(function ($query) use ($userId, $request) {
                    return $query->where('user_id', $userId)
                                 ->where('account_number', $request->account_number)
                                 ->where('account_name', $request->account_name);
                })->ignore($bankId),
            ],
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update the bank record with validated data
        $bank->update($validator->validated());

        return response()->json($bank);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $bankId)
    {
        // Retrieve the authenticated user's ID
        $userId = $request->user()->id;

        // Find the bank account and verify it belongs to the authenticated user
        $bank = Bank::where('id', $bankId)->where('user_id', $userId)->first();

        // If the bank account is not found or does not belong to the user, abort with a 404 response
        if (!$bank) {
            return response()->json(['message' => 'Bank account not found'], 404);
        }

        // Delete the bank account
        $bank->delete();

        return response()->json(['message' => 'Bank account deleted successfully']);
    }

    public function getUserBanks(Request $request)
    {
        // Retrieve the authenticated user's ID
        $userId = $request->user()->id;

        // Retrieve all banks associated with the authenticated user
        $banks = Bank::where('user_id', $userId)->get();

        return response()->json(['submission_details', $banks]);
    }
}
