<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'organisation_id',
        'employee_id',
        'payroll_type',
        'payroll_month',
        'payroll_year',
        'payroll_date',
        'amount',
        'status',
        'remarks',
        'paid_by',
        'paid_at',
        'created_by',
    ];

    protected $casts = [
        'payroll_date' => 'date',
        'paid_at'      => 'datetime',
    ];

    const TYPES = ['salary', 'bonus', 'incentive', 'advance', 'reimbursement', 'other'];
    const STATUSES = ['generated', 'paid', 'cancelled'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Human-readable month name for display
     */
    public function getMonthNameAttribute()
    {
        return \Carbon\Carbon::create()->month($this->payroll_month)->format('F');
    }
}
