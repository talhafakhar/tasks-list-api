<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Policies;

use App\Enums\ListSharePermission;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskListPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TaskList $taskList): bool
    {
        return $taskList->user_id === $user->id || $taskList->sharedWith->contains($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TaskList $taskList): bool
    {
        return $taskList->user_id === $user->id ||
            $taskList->sharedWith->contains(function (User $sharedWith) use ($user) {
                return $sharedWith->id === $user->id && $sharedWith->pivot->permission_type->is(ListSharePermission::EDIT);
            });
    }

    public function delete(User $user, TaskList $taskList): bool
    {
        return $taskList->user_id === $user->id;
    }

    public function restore(User $user, TaskList $taskList): bool
    {
        return false; // Not implemented
    }

    public function forceDelete(User $user, TaskList $taskList): bool
    {
        return false; // Not implemented
    }

    public function share(User $user, TaskList $taskList): bool
    {
        return $taskList->user_id === auth()->id();
    }

    public function unShare(User $user, TaskList $taskList): bool
    {
        return $taskList->user_id === auth()->id();
    }

    public function updatePermission(User $user, TaskList $taskList): bool
    {
        return $taskList->user_id === auth()->id();
    }

    public function shared(User $user, TaskList $taskList): bool
    {
        return $taskList->user_id === auth()->id();
    }
}
