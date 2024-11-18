<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListShare extends Model
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
}
