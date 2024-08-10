<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

trait UUIDAsPrimaryKey
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }


    public function getKeyType()
    {
        return 'string';
    }

    // method to parsing method find by UUID
    public static function findByUuid(string $uuid)
    {
        $model = static::where('id', $uuid)->first();

        if (!$model) {
            throw new ModelNotFoundException("Model not found with UUID: {$uuid}");
        }

        return $model;
    }
}
