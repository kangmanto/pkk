<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Reports\Exceptions\UnknownReportCodeException;
use App\Reports\Modules\BaseReport;
use App\Reports\Registry\ReportRegistry;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

final class ReportRegistryTest extends TestCase
{
    public function test_resolve_known_report_code(): void
    {
        $registry = new ReportRegistry([
            'test.registry' => RegistryFakeReport::class,
        ]);

        $report = $registry->resolve('test.registry');

        $this->assertSame('test.registry', $report->code());
        $this->assertSame('desa', $report->scope());
    }

    public function test_unknown_code_throws_not_found_exception(): void
    {
        $registry = new ReportRegistry([
            'test.registry' => RegistryFakeReport::class,
        ]);

        $this->expectException(UnknownReportCodeException::class);
        $this->expectExceptionMessage('Unknown report code [missing.code]');

        $registry->resolve('missing.code');
    }
}

final class RegistryFakeReport extends BaseReport
{
    public function code(): string
    {
        return 'test.registry';
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
