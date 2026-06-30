<?php

namespace App\Models;

use App\Traits\HasAudit;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes, HasTenant, HasAudit;

    protected $fillable = [
        'organisation_id',
        'customer_id',
        'contact_name',
        'contact_mobile',
        'alternate_mobile',
        'contact_email',
        'address',
        'city',
        'state',
        'pincode',
        'source',
        'lead_type',
        'project_type',
        'expected_budget',
        'project_address',
        'status',
        'notes',
        'assigned_to',
    ];

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function measurements()
    {
        return $this->hasMany(Measurement::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function followups()
    {
        return $this->hasMany(LeadFollowup::class)->latest('followup_date');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
