<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Enums;

enum ListSharePermission: string
{
    case VIEW = 'view';
    case EDIT = 'edit';

    public function is(self ...$permissions): bool
    {
        return in_array($this, $permissions);
    }
}
