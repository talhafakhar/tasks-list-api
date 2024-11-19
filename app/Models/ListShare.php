<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Models;

use App\Enums\ListSharePermission;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ListShare extends Pivot
{
    use HasFactory, HasUuid;

    public function taskList(): BelongsTo
    {
        return $this->belongsTo(TaskList::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'permission_type' => ListSharePermission::class,
        ];
    }
}
