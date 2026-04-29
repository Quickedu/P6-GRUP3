<?php

namespace App\Http\Controllers\Workers\Doctor;

use App\Actions\Workers\Doctor\GeneratePatientReportPdf;
use App\Http\Controllers\Controller;
use App\Http\Requests\Worker\DownloadPdfRequest;

class downloadpdfController extends Controller
{
    public function download(DownloadPdfRequest $request, GeneratePatientReportPdf $generatePdf)
    {
        return $generatePdf($request->validated());
    }
}
