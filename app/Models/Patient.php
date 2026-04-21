<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Patient extends Authenticatable
{
    protected $fillable = [
        'name',
        'nts',
        'address',
        'dni',
        'phone',
    ];

    public function getRoleAttribute(): string
    {
        return 'patient';
    }

    public function patientNeeds(): HasMany
    {
        return $this->hasMany(PatientNeed::class);
    }

    public function needs(): BelongsToMany
    {
        return $this->belongsToMany(Need::class, 'patient_needs');
    }
}
