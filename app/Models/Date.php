<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];
}
