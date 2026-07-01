<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTenant;

class Attendance extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'organisation_id',
        'employee_id',
        'attendance_date',
        'status',
        'remarks',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
