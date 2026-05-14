<?php

namespace App\Http\Requests\Worker;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->role === 'secretary' || Auth::user()->role === 'admin');
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|integer|min:1|exists:patients,id',
            'worker_id' => 'required|integer|min:1|exists:workers,id',
            'test_id' => 'required|integer|min:1|exists:test_types,id',
            'date_time' => 'required|date',
            'time' => 'required|integer|min:1',
            'estat' => 'required|in:programada,cancel·lada,realitzada',
            'urgencia' => 'required|in:no urgent,preferent,urgent',
            'description' => 'nullable|string|max:255',
        ];
    }
}