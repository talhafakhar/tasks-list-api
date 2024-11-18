<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Policies;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskListPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        //
    }

    public function view(User $user, TaskList $taskList): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, TaskList $taskList): bool
    {
    }

    public function delete(User $user, TaskList $taskList): bool
    {
    }

    public function restore(User $user, TaskList $taskList): bool
    {
    }

    public function forceDelete(User $user, TaskList $taskList): bool
    {
    }
}
