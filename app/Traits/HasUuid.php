<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $column = $model->uuidColumn ?? 'uuid'; // default column is "uuid"

            if (empty($model->{$column})) {
                $model->{$column} = (string) Str::uuid();
            }
        });
    }
}
