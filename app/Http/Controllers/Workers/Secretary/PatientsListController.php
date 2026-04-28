<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\Secretary\UpdateDataRequest;
use App\Models\Date;
use App\Models\Patient;
use App\Models\Test;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PatientsListController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $patients = Patient::query()->when($search, function ($query, $search){
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('dni', 'like', "%{$search}%")
                  ->orWhere('nts', 'like', "%{$search}%");
        })->get();

        return Inertia::render('Workers/Secretary/PatientsList', [
            'patients' => $patients,
        ]);
    }

    public function update(UpdateDataRequest $request, $id){
        $patient = Patient::findOrFail($id);

        $data = $request->validated();

        $patient->update($data);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Dades modificades correctament']);
    }
}