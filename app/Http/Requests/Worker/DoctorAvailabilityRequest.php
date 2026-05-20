<?php

namespace App\Http\Requests\Worker;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DoctorAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::guard('admin')->user();

        return $user !== null && ($user->role === 'secretary' || $user->role === 'admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'integer', 'min:1'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.required' => 'La data és obligatòria',
            'date.date_format' => 'La data ha de tenir el format AAAA-MM-DD',
            'time.required' => 'El temps és obligatori',
            'time.integer' => 'El temps ha de ser un número enter',
            'time.min' => 'El temps ha de ser com a mínim 1 minut',
        ];
    }
}
