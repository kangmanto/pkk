<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Reports\Data\ReportContext;
use App\Reports\Modules\BaseReport;
use App\Reports\Renderers\DocxRenderer;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

final class DocxRendererTest extends TestCase
{
    public function test_generates_real_docx_binary(): void
    {
        $renderer = app(DocxRenderer::class);
        $report = new DocxRendererFakeReport();
        $context = new ReportContext(
            role: 'desa_admin',
            areaId: 10,
            areaLevel: 'desa',
            mode: 'rw',
            areaName: 'Desa A',
            filter: [],
            metadata: [],
        );

        $response = $renderer->render($report, [
            'meta' => [
                'report_code' => $report->code(),
                'orientation' => 'landscape',
                'area_name' => 'Desa A',
                'area_level' => 'desa',
                'area_id' => 10,
                'printed_by' => 'Tester',
                'printed_at' => '2026-03-04 10:00:00',
            ],
            'rows' => [],
        ], $context);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            $response->headers->get('content-type')
        );
        $this->assertStringStartsWith('PK', (string) $response->getContent());
    }
}

final class DocxRendererFakeReport extends BaseReport
{
    public function code(): string
    {
        return 'test.docx.renderer';
    }

    public function scope(): string
    {
        return 'desa';
    }

    public function data(Authenticatable $user, array $filter): array
    {
        return [];
    }

    public function view(): string
    {
        return 'reports.sample-village-profile';
    }
}
