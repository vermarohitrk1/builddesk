<?php

namespace App\Models;

use App\Traits\HasAudit;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, SoftDeletes, HasTenant, HasAudit;

    protected $fillable = [
        'organisation_id',
        'customer_id',
        'lead_id',
        'quotation_number',
        'sub_total',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total_amount',
        'terms',
        'footer_text',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
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
        return $this->hasMany(QuotationItem::class);
    }
}
