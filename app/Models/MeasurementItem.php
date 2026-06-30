<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeasurementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'measurement_id',
        'title',
        'description',
    ];

    public function measurement()
    {
        return $this->belongsTo(Measurement::class);
    }
}
