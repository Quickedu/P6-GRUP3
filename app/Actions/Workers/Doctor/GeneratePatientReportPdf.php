<?php

namespace App\Actions\Workers\Doctor;

use App\Models\Report;
use App\Models\ImageReport;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;


class GeneratePatientReportPdf
{
    public function pdf(array $data): void
    {
        $report = $this->createReport($data);

        $this->createImageReport($data, $report);

        $Code = $this->generateCode($data);

        $htmlContent = $this->generateHtml($data, $Code);

        $this->streamPdf($htmlContent, $report);
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
            'data_request' => $data['data_request'],
            'data_exploration' => $data['data_exploration'],
            'reason' => $data['reason'],
            'exploration' => $data['exploration'],
            'images' => $data['images'] ?? [],
            'created_at' => 'default:' . now()->toDateTimeString(),

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

    private function streamPdf(string $htmlContent, Report $report)
    {
        $options = new Options;
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream($report->pdf_path, ['Attachment' => true]);
    }
}
