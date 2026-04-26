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
        'address' => 'nullable|string|max:255',
        'phone' => 'nullable|numeric',
        'email' => 'nullable|email|max:255',
      ];
    }
}