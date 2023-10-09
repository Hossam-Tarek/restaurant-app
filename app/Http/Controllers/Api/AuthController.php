<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\RegisterAction;
use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\User\LoginRequest;
use App\Http\Requests\Api\User\RegisterRequest;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterAction $registerAction)
    {
        $user = $registerAction->handel(...$request->validated());

        return ApiHelper::success(new UserResource($user), 'Registered Successfully');
    }

    public function login(LoginRequest $request)
    {
        if (! auth()->attempt(request()->only(['email', 'password']))) {
            return ApiHelper::failure([], "Invalid username or password", 422);
        }
        return ApiHelper::success(new UserResource($request->user()), "Logged in successfully", 422);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return ApiHelper::success([], "Logged out successfully");
    }
}
