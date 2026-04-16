<?php

namespace App\Http\Controllers;
use App\Http\Requests\Worker\StoreDateRequest;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Test;

class DatesController extends Controller
{
  public function index(){
    $query = User::with('role')->where('doctor');
    $query2 = Test::get();

    $doctors = $query->get();
    $testTypes = $query2->get();

    return Inertia::render('newDate', [
      'doctors' => $doctors,
      'testTypes' => $testTypes,
    ]);
  }

  public function store(StoreDateRequest $request){
    $user = Auth::user();
    abort_if(!$user, 403);

    $data = $request->validated();

    Date::create($data);

    return redirect()->back()->with('success', 'Cita creada correctamente');
  }
}
