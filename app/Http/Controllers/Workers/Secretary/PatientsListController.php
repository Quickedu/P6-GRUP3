<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\Secretary\UpdateDataRequest;
use App\Http\Requests\Worker\Secretary\UpdateNeedsRequest;
use App\Models\Patient;
use App\Models\Test;
use App\Models\Need;
use App\Models\User;
use App\Models\Worker;
use App\Models\Report;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientsListController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $patients = Patient::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%")
                    ->orWhere('nts', 'like', "%{$search}%");
            })->get();
        
        $needs = Need::get();

        return Inertia::render('Workers/Secretary/PatientsList', [
            'patients' => $patients,
            'needs' => $needs,
        ]);
    }

    public function patientDetail(Patient $patient)
    {
        return Inertia::render('Workers/PatientDetail', [
            'patient' => $patient,
            'needs'   => $patient->needs()->get(),
            'reports' => $patient->reports()->with('worker.user')->get(),
            'availableNeeds' => Need::all(),
        ]);
    }

    public function update(UpdateDataRequest $request, $id){
        
        $patient = Patient::findOrFail($id);
        
        $data = $request->validated();

        $patient->update($data);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Dades modificades correctament']);
    }

    public function addPatientNeed(UpdateNeedsRequest $request, Patient $patient)
    {
        $data = $request->validated();
        
        //Add need to patient without duplicates
        $patient->needs()->syncWithoutDetaching([$data['need_id']]);

        $need = Need::find($data['need_id']);

        return response()->json([
            'status' => 'success',
            'message' => 'Necessitat assignada correctament',
            'data' => $need
        ]);
    }

    public function deletePatientNeed(Patient $patient, Need $need)
    {
        $patient->needs()->detach($need->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Necessitat eliminada'
        ]);
    }
}