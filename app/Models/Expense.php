<?php

namespace App\Models;

use App\Traits\HasAudit;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, HasTenant, HasAudit;

    protected $table = 'expenses';

    protected $fillable = [
        'organisation_id',
        'expense_category_id',
        'supplier_id',
        'expense_date',
        'amount',
        'payment_method',
        'description',
        'attachment',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'expense_date' => 'date',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
