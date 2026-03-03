<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Reports\Data\ReportContext;
use App\Reports\Exceptions\ReportAccessDeniedException;
use App\Reports\Modules\BaseReport;
use App\Reports\Security\ScopeGuard;
use Illuminate\Contracts\Auth\Authenticatable;
use Tests\TestCase;

final class ScopeGuardTest extends TestCase
{
    public function test_allows_access_for_matching_role_scope_area(): void
    {
        $guard = new ScopeGuard();
        $report = new ScopeGuardDesaReport();

        $context = new ReportContext(
            role: 'desa_admin',
            areaId: 10,
            areaLevel: 'desa',
            mode: 'rw',
            areaName: 'Desa A',
            filter: [],
        );

        $guard->assertCanAccess($report, $context);
        $this->assertTrue(true);
    }

    public function test_denies_access_for_scope_mismatch(): void
    {
        $guard = new ScopeGuard();
        $report = new ScopeGuardKecamatanReport();

        $context = new ReportContext(
            role: 'desa_admin',
            areaId: 10,
            areaLevel: 'desa',
            mode: 'rw',
            areaName: 'Desa A',
            filter: [],
        );

        $this->expectException(ReportAccessDeniedException::class);
        $this->expectExceptionMessage('Role-scope-area invariant violated.');

        $guard->assertCanAccess($report, $context);
    }
}

final class ScopeGuardDesaReport extends BaseReport
{
    public function code(): string
    {
        return 'test.scope.desa';
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

final class ScopeGuardKecamatanReport extends BaseReport
{
    public function code(): string
    {
        return 'test.scope.kecamatan';
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
