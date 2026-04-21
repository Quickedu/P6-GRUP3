<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientNeed extends Model
{
    protected $table = 'patient_needs';

    public $timestamps = false;

    protected $fillable = [
        'patient_id',
        'need_id',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function need(): BelongsTo
    {
        return $this->belongsTo(Need::class);
    }
}
