<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Need extends Model
{
    protected $table = 'needs';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'time',
    ];

    public function patientNeeds(): HasMany
    {
        return $this->hasMany(PatientNeed::class);
    }

    public function patients(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class, 'patient_needs');
    }
}
