<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organisation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'logo',
        'gst_number',
        'address',
        'city',
        'state',
        'country',
        'phone',
        'email',
        'website',
        'terms_and_conditions',
        'quotation_footer',
        'pdf_branding',
        'subscription_plan_id',
        'subscription_start_date',
        'subscription_end_date',
        'active_status',
        'payment_status',
    ];

    protected $casts = [
        'pdf_branding' => 'array',
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function modules()
    {
        return $this->hasMany(OrganisationModule::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(OrganisationSubscription::class);
    }

    public function currentSubscription()
    {
        // Simple helper to fetch the latest subscription
        return $this->hasOne(OrganisationSubscription::class)->latestOfMany();
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    protected static function booted()
    {
        static::created(function ($organisation) {
            $trialDays = config('billing.trial_days', 14);
            
            OrganisationSubscription::create([
                'organisation_id' => $organisation->id,
                'start_date' => now(),
                'end_date' => now()->addDays($trialDays),
                'status' => 'Trial',
                'type' => 'Trial',
            ]);
        });
    }
}
