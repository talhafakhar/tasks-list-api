<?php
/*
 * Copyright (c) 2024.
 * Talha Fakhar
 *
 * https://github.com/talhafakhar
 */

namespace App\Traits;


use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function ($model) {
            $model->uuid = Uuid::uuid4()->toString();
        });
    }

    /**
     * Override the default route binding to validate UUIDs.
     * To avoid PostgresSQL UUID injection, we need to validate the UUID before querying the database.
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        if (!Uuid::isValid($value)) {
            throw new NotFoundHttpException();
        }
//        dd(parent::resolveRouteBinding($value, $field));
        return parent::resolveRouteBinding($value, $field);
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
        if (Uuid::isValid($id)) {
            return true;
        }
        return false;
    }

    public static function findByUuid(string $uuid): self
    {
        if (!static::isUuid($uuid)) {
            abort(404, 'Not Found');
        }


        $query = static::where('uuid', $uuid);

        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(static::class))) {
            $query->whereNull('deleted_at');
        }

        return $query->firstOrFail();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
