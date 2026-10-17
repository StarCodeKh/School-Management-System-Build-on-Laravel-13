<?php

namespace App\Models\Concerns;

/**
 * Fills a human-readable code column (e.g. "DEP-00001") from the primary key
 * right after insert, so codes are unique without a "read last row + 1" race.
 *
 * Models using this define the DISPLAY_CODE_COLUMN and DISPLAY_CODE_PREFIX constants.
 */
trait HasDisplayCode
{
    protected static function bootHasDisplayCode(): void
    {
        static::created(function (self $model) {
            $model->forceFill([
                static::DISPLAY_CODE_COLUMN => static::DISPLAY_CODE_PREFIX.str_pad((string) $model->getKey(), 5, '0', STR_PAD_LEFT),
            ])->saveQuietly();
        });
    }
}
