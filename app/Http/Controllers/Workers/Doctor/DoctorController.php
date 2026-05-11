<?php

namespace App\Http\Controllers\Workers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Date;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Patient;
use App\Models\Report;
use App\Models\User;

class DoctorController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $worker = User::find($userId)?->worker;

        $dates = Date::with(['test', 'patient'])
            ->where('worker_id', $worker?->id)
            ->get();

        return Inertia::render('Workers/Dashboard', [
            'doctorDates' => $dates,
        ]);
    }
}