<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class PasswordRecoverRequest extends PasswordResetRequest
{
    /**
     * Apply a global scope so this model only returns
     * rows where type = 'recover'.
     */
    protected static function booted()
    {
        static::addGlobalScope('recover', function (Builder $builder) {
            $builder->where('type', 'recover');
        });
    }
}
