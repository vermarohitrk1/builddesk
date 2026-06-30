<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait HasAudit
{
    /**
     * Boot the trait and register model events.
     */
    protected static function bootHasAudit(): void
    {
        static::created(fn ($model) => $model->logAudit('created'));
        static::updated(fn ($model) => $model->logAudit('updated'));
        static::deleted(fn ($model) => $model->logAudit('deleted'));

        // Automatically set created_by and updated_by
        static::creating(function ($model) {
            if (Auth::hasUser()) {
                $model->created_by = Auth::id();
                $model->updated_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            if (Auth::hasUser()) {
                $model->updated_by = Auth::id();
            }
        });
    }

    /**
     * Log the audit event.
     */
    protected function logAudit(string $event): void
    {
        // Skip audit table itself to avoid recursion
        if ($this->getTable() === 'audit_logs') return;

        $organisationId = $this->organisation_id ?? (Auth::hasUser() ? Auth::user()->organisation_id : null);

        AuditLog::create([
            'organisation_id' => $organisationId,
            'user_id' => Auth::id(),
            'event' => $event,
            'auditable_type' => get_class($this),
            'auditable_id' => $this->id,
            'old_values' => $event === 'updated' ? $this->getOriginal() : null,
            'new_values' => $event !== 'deleted' ? $this->getAttributes() : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
