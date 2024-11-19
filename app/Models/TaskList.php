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
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskList extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function sharedWith(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'list_shares', 'task_list_id', 'user_id', 'id', 'id')
            ->using(ListShare::class)
            ->withPivot(['permission_type', 'uuid']);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('ownOrShared', function ($query) {
            $userId = auth()->id();
            $query->where('user_id', $userId)
                ->orWhereHas('sharedWith', function ($subQuery) use ($userId) {
                    $subQuery->where('users.id', $userId);
                });
        });
    }
}
