<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Need extends Model
{
    protected $table = 'needs';

    protected $fillable = [
        'name',
        'time',
    ];

    public function dates(): BelongsToMany
    {
        return $this->belongsToMany(Patient::class);
    }
}
