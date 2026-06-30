<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'suppliers';

    protected $fillable = [
        'organisation_id',
        'name',
        'mobile',
        'gst_number',
        'address',
        'notes',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
