<?php

namespace App\Http\Requests\Worker;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FilterDatesRequest extends FormRequest
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
            'date' => ['nullable', 'date_format:Y-m-d'],
            'doctor_id' => ['nullable', 'integer', 'exists:workers,id'],
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
            'date.date_format' => 'La data ha de tenir el format AAAA-MM-DD',
            'doctor_id.integer' => 'El doctor seleccionat no és vàlid',
            'doctor_id.exists' => 'El doctor seleccionat no existeix',
        ];
    }
}
