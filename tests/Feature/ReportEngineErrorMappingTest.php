<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Reports\Contracts\RendererContract;
use App\Reports\Contracts\ReportContract;
use App\Reports\Data\ReportContext;
use App\Reports\Engine\ReportEngine;
use App\Reports\Exceptions\InvalidReportFilterException;
use App\Reports\Exceptions\ReportAccessDeniedException;
use App\Reports\Exceptions\UnknownReportCodeException;
use App\Reports\Modules\BaseReport;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpFoundation\Response;
use Tests\Fakes\FakeUser;
use Tests\TestCase;

final class ReportEngineErrorMappingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('reports.modules', [
            'test.error.desa' => ErrorDesaReport::class,
            'test.error.kecamatan' => ErrorKecamatanReport::class,
        ]);

        config()->set('reports.renderers', [
            'pdf' => ErrorFakeRenderer::class,
            'docx' => ErrorFakeRenderer::class,
        ]);
    }

    public function test_unknown_report_code_throws_404(): void
    {
        $engine = app(ReportEngine::class);
        $user = new FakeUser();

        try {
            $engine->generate('unknown.code', 'pdf', $user, ['status' => 'active']);
            $this->fail('Expected UnknownReportCodeException to be thrown.');
        } catch (UnknownReportCodeException $exception) {
            $this->assertSame(404, $exception->getStatusCode());
        }
    }

    public function test_invalid_filter_throws_422(): void
    {
        $engine = app(ReportEngine::class);
        $user = new FakeUser();

        try {
            $engine->generate('test.error.desa', 'pdf', $user, [
                'status' => [new \stdClass()],
            ]);
            $this->fail('Expected InvalidReportFilterException to be thrown.');
        } catch (InvalidReportFilterException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }
    }

    public function test_scope_mismatch_throws_403(): void
    {
        $engine = app(ReportEngine::class);
        $user = new FakeUser(role: 'desa_admin', area_level: 'desa');

        try {
            $engine->generate('test.error.kecamatan', 'pdf', $user, ['status' => 'active']);
            $this->fail('Expected ReportAccessDeniedException to be thrown.');
        } catch (ReportAccessDeniedException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }
}

final class ErrorDesaReport extends BaseReport
{
    public function code(): string
    {
        return 'test.error.desa';
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

final class ErrorKecamatanReport extends BaseReport
{
    public function code(): string
    {
        return 'test.error.kecamatan';
    }

    public function scope(): string
    {
        return 'kecamatan';
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

final class ErrorFakeRenderer implements RendererContract
{
    public function render(ReportContract $report, array $data, ReportContext $context): Response
    {
        return response()->json([
            'ok' => true,
        ]);
    }
}
