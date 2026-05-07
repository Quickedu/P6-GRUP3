<?php

namespace App\Http\Requests\Worker;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FilterPatientByNtsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && (Auth::user()->role === 'secretary' || Auth::user()->role === 'admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nts' => ['required', 'string', 'regex:/^[A-Z]{4}\d{10}$/'],
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
            'nts.required' => 'El camp NTS és obligatori',
            'nts.string' => 'El valor NTS ha de ser una cadena de lletres i números',
            'nts.regex' => 'El NTS ha de tenir 4 lletres majúscules i 10 números',
        ];
    }
}
