<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Policies;

use App\Models\ListShare;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ListSharePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, ListShare $listShare): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, ListShare $listShare): bool
    {
    }

    public function delete(User $user, ListShare $listShare): bool
    {
    }

    public function restore(User $user, ListShare $listShare): bool
    {
    }

    public function forceDelete(User $user, ListShare $listShare): bool
    {
    }
}
