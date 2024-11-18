<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListShareRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'task_list_id' => ['required', 'exists:task_lists'],
            'permission_type' => ['required'],
            'user_id' => ['required', 'exists:users'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
