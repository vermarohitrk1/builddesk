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
}
