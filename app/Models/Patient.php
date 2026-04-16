<?php

namespace App\Models;

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
}
