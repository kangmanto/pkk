<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Reports\Data\ReportContext;
use App\Reports\Support\ReportAuditLogger;
use Illuminate\Support\Facades\Log;
use Mockery;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Tests\Fakes\FakeUser;
use Tests\TestCase;

final class ReportAuditLoggerTest extends TestCase
{
    public function test_logs_generated_event(): void
    {
        config()->set('reports.audit.enabled', true);
        config()->set('reports.audit.log_channel', 'stack');

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('info')
            ->once()
            ->with('report.generated', Mockery::on(static fn (array $payload): bool => ($payload['report_code'] ?? null) === 'desa.population_summary'));

        Log::shouldReceive('channel')
            ->once()
            ->with('stack')
            ->andReturn($logger);

        app(ReportAuditLogger::class)->generated(
            'desa.population_summary',
            'pdf',
            new FakeUser(),
            $this->context()
        );
    }

    public function test_logs_denied_event(): void
    {
        config()->set('reports.audit.enabled', true);
        config()->set('reports.audit.log_channel', 'stack');

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('warning')
            ->once()
            ->with('report.denied', Mockery::on(static fn (array $payload): bool => ($payload['exception'] ?? null) === AccessDeniedHttpException::class));

        Log::shouldReceive('channel')
            ->once()
            ->with('stack')
            ->andReturn($logger);

        app(ReportAuditLogger::class)->denied(
            'kecamatan.village_recaps',
            'pdf',
            new FakeUser(),
            $this->context(),
            new AccessDeniedHttpException('forbidden')
        );
    }

    public function test_logs_failed_event(): void
    {
        config()->set('reports.audit.enabled', true);
        config()->set('reports.audit.log_channel', 'stack');

        $logger = Mockery::mock(LoggerInterface::class);
        $logger->shouldReceive('error')
            ->once()
            ->with('report.failed', Mockery::on(static fn (array $payload): bool => ($payload['exception'] ?? null) === RuntimeException::class));

        Log::shouldReceive('channel')
            ->once()
            ->with('stack')
            ->andReturn($logger);

        app(ReportAuditLogger::class)->failed(
            'desa.household_welfare',
            'docx',
            new FakeUser(),
            $this->context(),
            new RuntimeException('unexpected error')
        );
    }

    private function context(): ReportContext
    {
        return new ReportContext(
            role: 'desa_admin',
            areaId: 10,
            areaLevel: 'desa',
            mode: 'rw',
            areaName: 'Desa A',
            filter: []
        );
    }
}
