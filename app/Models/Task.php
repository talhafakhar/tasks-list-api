<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Models;

use App\Enums\TaskStatus;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = ['id'];

    public function taskList(): BelongsTo
    {
        return $this->belongsTo(TaskList::class);
    }

    protected function casts(): array
    {
        return [
            'status' => TaskStatus::class,
        ];
    }

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('ownOrShared', function ($query) {
            $userId = auth()->id();
            $query->whereHas('taskList', function ($subQuery) use ($userId) {
                $subQuery->where('user_id', $userId)
                    ->orWhereHas('sharedWith', function ($subQuery) use ($userId) {
                        $subQuery->where('users.id', $userId);
                    });
            });
        });
    }
}
