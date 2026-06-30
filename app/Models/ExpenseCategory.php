<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'expense_categories';

    protected $fillable = [
        'organisation_id',
        'name',
        'slug',
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
