<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Reports\Contracts\RendererContract;
use App\Reports\Contracts\ReportContract;
use App\Reports\Data\ReportContext;
use App\Reports\Engine\ReportEngine;
use App\Reports\Modules\BaseReport;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpFoundation\Response;
use Tests\Fakes\FakeUser;
use Tests\TestCase;

final class ReportEngineAuthorizationMatrixTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('reports.modules', [
            'test.desa.allowed' => MatrixDesaReport::class,
            'test.kecamatan.allowed' => MatrixKecamatanReport::class,
        ]);

        config()->set('reports.renderers', [
            'pdf' => MatrixFakeRenderer::class,
            'docx' => MatrixFakeRenderer::class,
        ]);
    }

    public function test_desa_user_can_access_desa_report(): void
    {
        $engine = app(ReportEngine::class);
        $user = new FakeUser(role: 'desa_admin', area_level: 'desa');

        $response = $engine->generate('test.desa.allowed', 'pdf', $user, [
            'status' => 'active',
        ]);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_kecamatan_user_can_access_kecamatan_report(): void
    {
        $engine = app(ReportEngine::class);
        $user = new FakeUser(role: 'kecamatan_admin', area_level: 'kecamatan');

        $response = $engine->generate('test.kecamatan.allowed', 'pdf', $user, [
            'status' => 'active',
        ]);

        $this->assertSame(200, $response->getStatusCode());
    }
}

final class MatrixDesaReport extends BaseReport
{
    public function code(): string
    {
        return 'test.desa.allowed';
    }

    public function scope(): string
    {
        return 'desa';
    }

    public function data(Authenticatable $user, array $filter): array
    {
        return [['ok' => true, 'level' => $filter['level'] ?? null, 'area_id' => $filter['area_id'] ?? null]];
    }

    public function view(): string
    {
        return 'reports.sample-village-profile';
    }
}

final class MatrixKecamatanReport extends BaseReport
{
    public function code(): string
    {
        return 'test.kecamatan.allowed';
    }

    public function scope(): string
    {
        return 'kecamatan';
    }

    public function data(Authenticatable $user, array $filter): array
    {
        return [['ok' => true, 'level' => $filter['level'] ?? null, 'area_id' => $filter['area_id'] ?? null]];
    }

    public function view(): string
    {
        return 'reports.sample-village-profile';
    }
}

final class MatrixFakeRenderer implements RendererContract
{
    public function render(ReportContract $report, array $data, ReportContext $context): Response
    {
        return response()->json([
            'code' => $report->code(),
            'scope' => $report->scope(),
            'meta' => $data['meta'] ?? [],
            'rows' => $data['rows'] ?? [],
        ]);
    }
}
