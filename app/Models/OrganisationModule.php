<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'module_id',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}
