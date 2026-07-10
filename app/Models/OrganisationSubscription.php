<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'start_date',
        'end_date',
        'grace_until',
        'status',
        'type',
        'remarks',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'grace_until' => 'datetime',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function usageHistories()
    {
        return $this->hasMany(OrganisationModuleUsageHistory::class, 'subscription_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'subscription_id');
    }
}
