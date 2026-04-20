<?php

namespace App\Http\Controllers;
use App\Http\Requests\Worker\StoreDateRequest;
use Inertia\Inertia;
use App\Models\Date;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardPatientController extends Controller
{
  public function index(){
    $dates = Date::get();/*where('user_id', Auth::user()->id);*/

    return Inertia::render('patientDashboard', [
      'dates' => $dates,
    ]);
  }
}