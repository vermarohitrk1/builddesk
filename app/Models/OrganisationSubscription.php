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

    public function getCurrentCycleStart()
    {
        $startDateDay = $this->start_date->day;
        $currentDate = now();
        $cycleStart = $currentDate->copy();
        
        if ($currentDate->day >= $startDateDay) {
            $cycleStart->day($startDateDay)->startOfDay();
        } else {
            $cycleStart->subMonth()->day($startDateDay)->startOfDay();
        }
        
        return $cycleStart;
    }

    public function getCurrentCycleEnd()
    {
        return $this->getCurrentCycleStart()->addMonth()->subDay()->endOfDay();
    }
}
