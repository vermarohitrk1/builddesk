<?php

namespace App\Models;

use App\Traits\HasAudit;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    use HasFactory, HasTenant, HasAudit;

    protected $fillable = [
        'organisation_id',
        'lead_id',
        'customer_id',
        'title',
        'notes',
        'created_by',
        'updated_by',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(MeasurementItem::class);
    }
}
