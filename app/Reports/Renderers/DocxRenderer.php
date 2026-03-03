<?php

declare(strict_types=1);

namespace App\Reports\Renderers;

use App\Reports\Contracts\RendererContract;
use App\Reports\Contracts\ReportContract;
use App\Reports\Data\ReportContext;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\Style\Section;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

final class DocxRenderer implements RendererContract
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

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection([
            'orientation' => $this->normalizeOrientation($report->orientation()),
        ]);
        Html::addHtml($section, $this->extractBodyHtml($html), false, false);

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();
        if ($content === false) {
            throw new RuntimeException('Failed to generate DOCX content.');
        }

        $filename = Str::slug($report->code()) . '-' . now()->format('Ymd_His') . '.docx';

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function normalizeOrientation(string $orientation): string
    {
        return strtolower($orientation) === 'portrait'
            ? Section::ORIENTATION_PORTRAIT
            : Section::ORIENTATION_LANDSCAPE;
    }

    private function extractBodyHtml(string $html): string
    {
        if (preg_match('/<body[^>]*>(.*)<\/body>/is', $html, $matches) === 1) {
            return (string) $matches[1];
        }

        return $html;
    }
}
