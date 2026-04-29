<?php

namespace App\Actions\Workers\Doctor;

use App\Models\Report;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class GeneratePatientReportPdf
{
    public function pdf(array $data)
    {
        $report = $this->createReport($data);

        $Code = $this->generateCode($data, $report->id);
        dd($Code);

        $htmlContent = $this->generateHtml($data, $Code);

        return $this->streamPdf($htmlContent, $data);
    }

    private function createReport(array $data)
    {
        $report = Report::create([
            'patient_id' => $data['patient_id'],
            'worker_id' => $data['worker_id'],
            'pdf_path' => '',
        ]);

        $filename = sprintf(
            '%s_%s_report.pdf',
            $report->created_at->format('Ymd-His'),
            $data['name']
        );

        $report->update(['pdf_path' => $filename]);
    }

    private function generateCode(array $data, int $reportId): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd
        );

        $writer = new Writer($renderer);

        $CodeData = [
            'report_id' => $reportId,
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
            // 'created_at' => $data['created_at'],
            'created_at' => 'default:' . now()->toDateTimeString(),

        ];

        return $writer->writeString(json_encode($CodeData));
    }

    private function generateHtml(array $data, string $Code): string
    {

        ob_start();
        include __DIR__.'/resources/js/pages/Workers/Secretary';
        View::make('Workers/Doctor/ReportPdfTemplate', [
            'data' => $data,
            'Code' => $Code,
        ])->render();
        return ob_get_clean();
    }

    private function streamPdf(string $htmlContent, array $data)
    {
        $options = new Options;
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($htmlContent);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = sprintf(
            '%s_%s_report.pdf',
            $data['create_at'],
            $data['patient_id']
        );

        $dompdf->stream($filename, ['Attachment' => true]);
    }
}
