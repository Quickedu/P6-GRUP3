<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Test extends Model
{
    protected $table = 'test_types';

    protected $fillable = [
        'name',
        'time',
    ];

    public function dates(): BelongsToMany
    {
        return $this->belongsToMany(Date::class);
    }
}
