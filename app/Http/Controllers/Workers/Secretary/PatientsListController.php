<?php

namespace App\Http\Controllers\Workers\Secretary;

use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\Secretary\UpdateDataRequest;
use App\Models\Patient;
use App\Models\Test;
use App\Models\Need;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientsListController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $patients = Patient::query()->when($search, function ($query, $search) {
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

    public function patientsNeedsList()
    { 
        $needs = Need::get();

        return Inertia::render('Workers/Secretary/PatientsList', [
            'needs' => $needs,
        ]);
    }

    public function update(UpdateDataRequest $request, $id){
        $patient = Patient::findOrFail($id);

        $data = $request->validated();

        $patient->update($data);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Dades modificades correctament']);
    }

    public function patientsNeeds($id)
    {
        $needs = Patient::with('needs')->where('patient_id', $id)->get();
        dd($needs);
        return Inertia::render('Workers/Secretary/PatientsList', [
            'need' => $needs,
        ]);
    }
    public function patientsNeedsUpdate(UpdateDataRequest $request, $id)
    {
        $patient = Patient::findOrFail($id);

        $data = $request->validated();

        $patient->update($data);

        return redirect()->back()->with(['status' => 'correcte', 'message' => 'Dades modificades correctament']);
    }
    public function patientsNeedsDelete($id)
    {
        $needs = Patient::with('needs')->where('patient_id', $id)->get();
        dd($needs);
        return Inertia::render('Workers/Secretary/PatientsList', [
            'need' => $needs,
        ]);
    }
}
