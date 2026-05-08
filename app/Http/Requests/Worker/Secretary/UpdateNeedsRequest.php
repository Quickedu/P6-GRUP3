<?php

namespace App\Http\Requests\Worker\Secretary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateNeedsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->role === 'secretary';
    }

    public function rules(): array
    {
        return [
            'need_id' => 'required|exists:needs,id'
        ];
    }
}
