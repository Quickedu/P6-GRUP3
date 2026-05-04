<?php

namespace App\Actions\Workers\Doctor;

use App\Models\ImageReport;
use App\Models\Report;
use App\Models\Worker;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GeneratePatientReportPdf
{
    public function pdf(array $data): StreamedResponse
    {
        $report = $this->createReport($data);

        $data['nreport'] = $report->id;

        $this->createImageReport($data, $report);

        $data['license_number'] = Worker::query()
            ->whereKey($data['worker_id'])
            ->value('license_number');

        $Code = $this->generateCode($data);

        $htmlContent = $this->generateHtml($data, $Code);

        return $this->streamPdf($htmlContent, $report);
    }

    private function createImageReport(array $data, Report $report): void
    {
        $images = $data['images'] ?? [];

        foreach ($images as $image) {
            $path = Storage::disk('public')->put('report_images', $image);

            ImageReport::create([
                'reports_id' => $report->id,
                'image_path' => Storage::url($path),
            ]);
        }
    }

    private function createReport(array $data): Report
    {
        $report = Report::create([
            'patient_id' => $data['patient_id'],
            'worker_id' => $data['worker_id'],
            'pdf_path' => '',
        ]);

        $filename = sprintf(
            '%s_%s_report.pdf',
            $report->created_at->format('Ymd-His'),
            $data['nts']
        );

        $report->update(['pdf_path' => $filename]);

        return $report;
    }

    private function generateCode(array $data): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);

        $CodeData = [
            // 'report_id' => $reportId,
            'patient_id' => $data['patient_id'],
            'worker_id' => $data['worker_id'],
            // 'nhc' => $data['nhc'],
            'name' => $data['name'],
            'address' => $data['address'],
            'birth_date' => $data['birth_date'],
            'nts' => $data['nts'],
            'center_requested' => $data['center_requested'],
            'center_destination' => $data['center_destination'],
            'doctor_name' => $data['doctor_name'],
            'license_number' => $data['license_number'] ?? null,
            'data_request' => $data['data_request'],
            'data_exploration' => $data['data_exploration'],
            'reason' => $data['reason'],
            'exploration' => $data['exploration'],
            'images' => $data['images'] ?? [],
            'created_at' => 'default:'.now()->toDateTimeString(),

        ];

        return $writer->writeString(json_encode($CodeData));
    }

    private function generateHtml(array $data, string $Code): string
    {
        return View::make('workers.doctor.report-pdf-template', [
            'data' => $data,
            'Code' => $Code,
        ])->render();
    }

    private function streamPdf(string $htmlContent, Report $report): StreamedResponse
    {
        $options = new Options;
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfOutput = $dompdf->output();

        $path = 'reports/'.$report->pdf_path;
        Storage::disk('public')->put($path, $pdfOutput);

        $report->update(['pdf_path' => $path]);

        return response()->streamDownload(
            static function () use ($pdfOutput): void {
                echo $pdfOutput;
            },
            basename($path),
            ['Content-Type' => 'application/pdf']
        );
    }
}
