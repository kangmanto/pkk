<?php

declare(strict_types=1);

namespace App\Reports\Renderers;

use App\Reports\Contracts\RendererContract;
use App\Reports\Contracts\ReportContract;
use App\Reports\Data\ReportContext;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class DocxRenderer implements RendererContract
{
    public function __construct(
        private readonly ViewFactory $view
    ) {
    }

    public function render(ReportContract $report, array $data, ReportContext $context): Response
    {
        // Placeholder renderer: replace with PHPWord export if strict DOCX binary is required.
        $content = $this->view->make($report->view(), [
            'report' => $report,
            'reportData' => $data,
            'context' => $context,
        ])->render();

        $filename = Str::slug($report->code()) . '-' . now()->format('Ymd_His') . '.docx';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
