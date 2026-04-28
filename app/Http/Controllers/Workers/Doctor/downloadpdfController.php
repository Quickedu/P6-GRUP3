<?php

namespace App\Http\Controllers\Workers\Doctor;

use App\Actions\Workers\Doctor\GeneratePatientReportPdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\DownloadPdfRequest;
use App\Models\Patient;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class downloadpdfController extends Controller
{ 
    public function download(DownloadPdfRequest $request, GeneratePatientReportPdf $generatePdf)
    {
        return $generatePdf($request->validated());
    }
}
