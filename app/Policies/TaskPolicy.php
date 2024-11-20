<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Policies;

use App\Enums\ListSharePermission;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Task $task): bool
    {
        $taskList = $task->taskList;
        return $taskList->user_id === $user->id ||
            $taskList->sharedWith->contains($user);
    }

    public function create(User $user, TaskList $taskList): bool
    {
        $pivot = $taskList->sharedWith->find($user->id)?->pivot;
        return $taskList->user_id === $user->id ||
            (
                $taskList->sharedWith->contains($user) &&
                $pivot->permission_type?->is(ListSharePermission::EDIT)
            );
    }

    public function update(User $user, Task $task): bool
    {
        $taskList = $task->taskList;
        $pivot = $taskList->sharedWith->find($user->id)?->pivot;
        return $taskList->user_id === $user->id ||
            (
                $taskList->sharedWith->contains($user) &&
                $pivot->permission_type?->is(ListSharePermission::EDIT)
            );
    }

    public function delete(User $user, Task $task): bool
    {
        $taskList = $task->taskList;
        $pivot = $taskList->sharedWith->find($user->id)?->pivot;
        return $taskList->user_id === $user->id ||
            (
                $taskList->sharedWith->contains($user) &&
                $pivot->permission_type?->is(ListSharePermission::EDIT)
            );
    }

    public function restore(User $user, Task $task): bool
    {
        return false; // Not implemented
    }

    public function forceDelete(User $user, Task $task): bool
    {
        return false; // Not implemented
    }
}
