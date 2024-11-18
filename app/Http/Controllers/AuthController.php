<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Controllers;

use App\Exceptions\InvalidArgument;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'username' => $request->validated('username'),
            'password' => Hash::make($request->validated('password')),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    /**
     * @throws InvalidArgument
     */
    public function login(LoginRequest $request)
    {

        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (!Auth::attempt([$loginType => $request->login, 'password' => $request->password])) {
            throw new InvalidArgument('Provided credentials are invalid!');
        }

        $user = Auth::user();
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'user' => new UserResource($request->user()),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully!',
        ]);
    }

    /**
     * @throws InvalidArgument
     */
    public function sendPasswordResetLink(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => __($status)])
            : throw new InvalidArgument('Provided email is invalid!');
    }

    /**
     * @throws InvalidArgument
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json(['status' => __($status)], 200)
            : throw new InvalidArgument('Link is invalid or expired!');
    }

}
