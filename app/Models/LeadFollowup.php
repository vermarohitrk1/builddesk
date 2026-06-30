<?php

namespace App\Models;

use App\Traits\HasAudit;
use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadFollowup extends Model
{
    use HasFactory, HasTenant, HasAudit;

    protected $table = 'lead_followups';

    protected $fillable = [
        'organisation_id',
        'lead_id',
        'user_id',
        'followup_date',
        'note',
        'completed_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'followup_date' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
