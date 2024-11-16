<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            Log::error($validator->errors());
            return response()->json($validator->errors(), 422);
        }

        $userCreating = User::query()->create([
            'name' => $request["name"],
            'email' => $request["email"],
            'password' => Hash::make($request["password"])
        ]);
        Log::info('User creating' . $userCreating->toJson());
        return response()->json('The User ' . $userCreating->email . ' creating', 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {

        try {
            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                Log::error('Unable to login due to invalid credentials');
                return ResponseHelper::error(message: 'Unable to login due to invalid credentials.', statusCode: 400);
            }

            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('My API Token')->plainTextToken;
            $authUser = [
                'user' => $user,
                'token' => $token
            ];

            return ResponseHelper::success(message: 'You are logged in successfully!', data: $authUser, statusCode: 200);


        } catch (Exception $e) {
            Log::error('Unable to Login User : ' . $e->getMessage() . ' - Line no. ' . $e->getLine());
            return ResponseHelper::error(message: 'Unable to Login! Please try again.' . $e->getMessage(), statusCode: 500);
        }
    }
}
