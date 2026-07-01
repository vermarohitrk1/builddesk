<?php

namespace App\Models;

use App\Traits\HasAudit;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes, HasTenant, HasAudit;

    protected $fillable = [
        'organisation_id',
        'user_id',
        'employee_code',
        'designation',
        'joining_date',
        'salary',
        'auto_generate_salary',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'joining_date'          => 'date',
        'auto_generate_salary'  => 'boolean',
        'salary'                => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
