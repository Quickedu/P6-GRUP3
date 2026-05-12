<?php

namespace App\Http\Requests\Worker\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkerAdminRquest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::guard('admin')->check() && Auth::guard('admin')->user()?->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $options = [
            'address' => 'required|string|max:255',
            'phone' => 'required|regex:/^[0-9]{9}$/',
            'role' => 'required|string|in:admin,secretary,doctor,technician',
            'email' => 'required|string|email|max:255|unique:users,email',
        ];

        if ($this->isMethod('put')) {
            $options['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->worker->user->id),
            ];
        }

        if ($this->isMethod('post')) {
            $options['name'] = 'required|string|max:255';
            $options['password'] = 'required|string|min:8';
            $options['nss'] = 'required|string|max:255';
            $options['dni'] = 'required|string|max:255';
            $options['license_number'] = 'required|string|max:255';
        }

        return $options;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nom i cognoms és obligatori.',
            'email.required' => 'L\'email és obligatori.',
            'email.unique' => 'Aquest email ja està registrat.',
            'role.required' => 'El rol és obligatori.',
            'password.required' => 'La contrasenya és obligatòria.',
            'password.min' => 'La contrasenya ha de tenir com a mínim 8 caràcters.',
            'nss.required' => 'El NSS és obligatori.',
            'dni.required' => 'El DNI és obligatori.',
            'address.required' => 'L\'adreça és obligatòria.',
            'phone.required' => 'El número de telèfon és obligatori.',
            'phone.regex' => 'El número de telèfon ha de tenir 9 dígits.',
        ];
    }
}
