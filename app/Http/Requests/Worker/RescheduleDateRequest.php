<?php

namespace App\Http\Requests\Worker;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RescheduleDateRequest extends FormRequest
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
            'date_time' => ['required', 'date_format:Y-m-d H:i:s'],
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
            'date_time.required' => 'La data i l\'hora són obligatòries',
            'date_time.date_format' => 'La data i l\'hora han de tenir el format AAAA-MM-DD HH:MM:SS',
        ];
    }
}
