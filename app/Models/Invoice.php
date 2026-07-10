<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'subscription_id',
        'invoice_number',
        'billing_start_date',
        'billing_end_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'due_date',
        'generated_at',
        'paid_at',
        'remarks',
    ];

    protected $casts = [
        'billing_start_date' => 'date',
        'billing_end_date' => 'date',
        'due_date' => 'date',
        'generated_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function subscription()
    {
        return $this->belongsTo(OrganisationSubscription::class, 'subscription_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
