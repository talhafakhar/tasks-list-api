<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'login' => 'required|string',
            'password' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'The email/username is required.',
            'password.required' => 'The password is required.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->sometimes('login', 'email', function ($input) {
            return filter_var($input->login, FILTER_VALIDATE_EMAIL);
        });
    }
}

