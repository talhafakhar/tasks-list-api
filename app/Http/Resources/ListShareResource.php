<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Http\Resources;

use App\Models\ListShare;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ListShare */
class ListShareResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'permission_type' => $this->permission_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'task_list_id' => $this->task_list_id,
            'user_id' => $this->user_id,

            'taskList' => new TaskListResource($this->whenLoaded('taskList')),
        ];
    }
}
