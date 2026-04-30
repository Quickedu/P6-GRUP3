<?php

namespace App\Http\Requests\Worker\Doctor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class DownloadPdfRequest extends FormRequest
{
  public function authorize(): bool
  {
    return Auth::check() && (Auth::user()->role === 'doctor' || Auth::user()->role === 'admin');
  }

  public function rules(): array
  {
    return [
      'patient_id' => 'required|integer|min:1|exists:patients,id',
      'worker_id' => 'required|integer|min:1|exists:workers,id',
      'nreport' => 'required|integer',
      // 'nhc' => 'required|string|max:255',
      'name' => 'required|string|max:255',
      'address' => 'required|string|max:255',
      'birth_date' => 'required|date',
      'nts' => 'required|string|max:255',
      'center_requested' => 'required|string|max:255',
      'center_destination' => 'required|string|max:255',
      'doctor_name' => 'required|string|max:255',
      'data_request' => 'required|date',
      'data_exploration' => 'required|date',
      'reason' => 'required|string|max:255',
      'exploration' => 'required|string|max:255',
      'report' => 'required|string|max:255',
      'images' => ['nullable', 'array'],
      'images.*' => ['image', 'max:2048'],
      'created_at' => 'default:' . now()->toDateTimeString(),
    ];
  }
}
