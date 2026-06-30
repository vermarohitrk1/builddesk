<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, HasTenant;

    protected $table = 'projects';

    protected $fillable = [
        'organisation_id',
        'lead_id',
        'project_number',
        'started_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
