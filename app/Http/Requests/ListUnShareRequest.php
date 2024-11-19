<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListUnShareRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'users' => ['required', 'array'],
            'users.*' => ['exists:users,uuid'],
        ];
    }
}
