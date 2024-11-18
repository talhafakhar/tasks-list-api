<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'task_list_id' => ['required', 'exists:task_lists'],
            'description' => ['required'],
            'status' => ['required'],//
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
