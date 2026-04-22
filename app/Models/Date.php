<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Date extends Model
{
    protected $table = 'dates';

    protected $fillable = [
        'patient_id',
        'worker_id',
        'test_id',
        'date_time',
        'time',
        'estat',
        'urgencia',
        'description',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class, 'test_id');
    }
}
