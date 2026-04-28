<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'patient_id',
        'worker_id',
        'pdf_path',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }
}
