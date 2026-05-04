<?php

namespace App\Http\Requests\Worker\Secretary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'secretary';
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'regex:/^[0-9]{9}$/'],
            'email' => ['nullable', 'email'],
            'address' => ['required', 'string', 'max:255'],
        ];
    }
}
