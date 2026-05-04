<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageReport extends Model
{
    protected $table = 'image_report';

    protected $fillable = [
        'reports_id',
        'image_path',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class, 'reports_id');
    }
}
