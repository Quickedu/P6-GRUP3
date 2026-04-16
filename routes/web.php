<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function (){
        return Inertia::render('Dashboard', [
            'role' => Auth::user()->role
        ]);
    })->name('dashboard');
});

require __DIR__.'/settings.php';
