<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Requests;

use App\Enums\ListSharePermission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateShareRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'permission' => [
                'required',
                new Enum(ListSharePermission::class)
            ],
            'user_id' => ['required', 'exists:users,uuid'],
        ];
    }
}
