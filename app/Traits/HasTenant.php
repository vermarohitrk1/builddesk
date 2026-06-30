<?php

namespace App\Traits;

use App\Scopes\TenantScope;
use Illuminate\Support\Facades\Auth;

trait HasTenant
{
    /**
     * Boot the trait and apply the TenantScope.
     */
    protected static function bootHasTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        // Automatically set organisation_id when creating a new model
        static::creating(function ($model) {
            if (Auth::hasUser() && Auth::user()->organisation_id) {
                $model->organisation_id = Auth::user()->organisation_id;
            }
        });
    }
}
