<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Traits;


use Ramsey\Uuid\Uuid;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    public static function find($id): null|self
    {
        if (self::isUuid($id)) {
            return self::findByUuid($id);
        }

        return self::where('id', $id)->first() ?? null;
    }

    protected static function isUuid($id): bool
    {
        if (is_string($id)) {
            return true;
        }
        return false;
    }

    public static function findByUuid(string $uuid): self
    {
        return static::where('uuid', $uuid)
            ->whereNull('deleted_at')
            ->firstOrFail();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
