<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Reports\Modules\BaseReport;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\Fakes\FakeUser;
use Tests\TestCase;

final class ReportEndpointResponseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('reports.modules', [
            'test.http.desa' => HttpDesaReport::class,
            'test.http.kecamatan' => HttpKecamatanReport::class,
        ]);
    }

    public function test_requires_authenticated_user(): void
    {
        $response = $this->get('/report/test.http.desa/pdf');

        $response->assertStatus(401);
    }

    public function test_pdf_endpoint_returns_pdf_binary(): void
    {
        $this->actingAs(new FakeUser(role: 'desa_admin', area_level: 'desa'));

        $response = $this->get('/report/test.http.desa/pdf?status=active');

        $response->assertStatus(200);
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_docx_endpoint_returns_docx_binary(): void
    {
        $this->actingAs(new FakeUser(role: 'desa_admin', area_level: 'desa'));

        $response = $this->get('/report/test.http.desa/docx?status=active');

        $response->assertStatus(200);
        $this->assertSame(
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            $response->headers->get('content-type')
        );
        $this->assertStringStartsWith('PK', $response->getContent());
    }

    public function test_unknown_report_code_returns_404(): void
    {
        $this->actingAs(new FakeUser(role: 'desa_admin', area_level: 'desa'));

        $response = $this->get('/report/unknown.code/pdf?status=active');

        $response->assertStatus(404);
    }

    public function test_scope_mismatch_returns_403(): void
    {
        $this->actingAs(new FakeUser(role: 'desa_admin', area_level: 'desa'));

        $response = $this->get('/report/test.http.kecamatan/pdf?status=active');

        $response->assertStatus(403);
    }

    public function test_invalid_filter_returns_422(): void
    {
        $this->actingAs(new FakeUser(role: 'desa_admin', area_level: 'desa'));

        $response = $this->get('/report/test.http.desa/pdf?unknown_filter=x');

        $response->assertStatus(422);
    }
}

final class HttpDesaReport extends BaseReport
{
    public function code(): string
    {
        return 'test.http.desa';
    }

    public function scope(): string
    {
        return 'desa';
    }

    public function data(Authenticatable $user, array $filter): array
    {
        return [[
            'title' => 'HTTP Desa Report',
            'area_id' => $filter['area_id'] ?? null,
            'level' => $filter['level'] ?? null,
            'generated_at' => '2026-03-04 10:00:00',
            'requested_by' => data_get($user, 'name', 'unknown'),
        ]];
    }

    public function view(): string
    {
        return 'reports.sample-village-profile';
    }
}

final class HttpKecamatanReport extends BaseReport
{
    public function code(): string
    {
        return 'test.http.kecamatan';
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
