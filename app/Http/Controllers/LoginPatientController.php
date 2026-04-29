<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginPatientController extends Controller
{
    public function show(Request $request)
    {
        return Inertia::render('auth/Login', [
            'status' => $request->session()->get('status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'nts' => ['nullable', 'string'],
            'dni' => ['nullable', 'string'],
        ]);

        $patient = null;
        if (! empty($credentials['nts'])) {
            $patient = Patient::where('nts', $credentials['nts'])->first();
        }
        if (! $patient && ! empty($credentials['dni'])) {
            $patient = Patient::where('dni', $credentials['dni'])->first();
        }

        if (! $patient) {
            throw ValidationException::withMessages([
                'nts' => [trans('auth.failed')],
            ]);
        }
        // dd($patient);
        Auth::guard('patient')->login($patient, $request->boolean('remember'));

        $request->session()->regenerate();
        $request->session()->put('role', 'patient');

        return redirect()->intended(route('patientDashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('patient')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
