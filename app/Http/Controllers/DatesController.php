<?php

namespace App\Http\Controllers;
use App\Http\Requests\Worker\StoreDateRequest;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Test;
use App\Models\Date;
use Illuminate\Support\Facades\Auth;

class DatesController extends Controller
{
  public function index(){
    $doctors = User::where('role', '=', 'doctor')->get();
    $testTypes = Test::get();

    return Inertia::render('newDate', [
      'doctors' => $doctors,
      'testTypes' => $testTypes,
    ]);
  }

  public function store(StoreDateRequest $request){
    $data = $request->validated();

    Date::create($data);

    return redirect()->back()->with('success', 'Cita creada correctamente');
  }
}
