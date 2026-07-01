<?php

namespace App\Models;

use App\Traits\HasAudit;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes, HasTenant, HasAudit;

    protected $fillable = [
        'organisation_id',
        'name',
        'email',
        'phone',
        'address',
        'created_by',
        'updated_by',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}

