<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationModuleUsageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'module_id',
        'enabled_at',
        'disabled_at',
        'is_chargeable',
        'subscription_id',
    ];

    protected $casts = [
        'enabled_at' => 'datetime',
        'disabled_at' => 'datetime',
        'is_chargeable' => 'boolean',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function subscription()
    {
        return $this->belongsTo(OrganisationSubscription::class, 'subscription_id');
    }
}
