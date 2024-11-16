<?php

namespace App\Http\Controllers\Api;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $fields = $request->validated();

        if (User::query()->where('email', $fields["email"])->first()) {
            return ResponseHelper::error(message: 'Email already exists!', statusCode: 400);
        } else {

            $user = User::query()->create([
                'name' => $fields["name"],
                'email' => $fields["email"],
                'password' => Hash::make($fields["password"])
            ]);

            return ResponseHelper::success(message: 'User has been registered successfully!', data: (array)$user->toArray(), statusCode: 201);
        }

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
