<?php

declare(strict_types=1);

namespace App\Reports\Renderers;

use App\Reports\Contracts\RendererContract;
use App\Reports\Contracts\ReportContract;
use App\Reports\Data\ReportContext;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class PdfRenderer implements RendererContract
{
    public function __construct(
        private readonly ViewFactory $view
    ) {
    }

    public function render(ReportContract $report, array $data, ReportContext $context): Response
    {
        $html = $this->view->make($report->view(), [
            'report' => $report,
            'reportData' => $data,
            'context' => $context,
        ])->render();

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', $this->normalizeOrientation($report->orientation()));
        $dompdf->render();

        $content = $dompdf->output();
        $filename = Str::slug($report->code()) . '-' . now()->format('Ymd_His') . '.pdf';

        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "inline; filename=\"{$filename}\"",
        ]);
    }

    private function normalizeOrientation(string $orientation): string
    {
        return strtolower($orientation) === 'portrait' ? 'portrait' : 'landscape';
    }
}
