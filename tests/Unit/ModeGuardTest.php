<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Reports\Data\ReportContext;
use App\Reports\Exceptions\ReportAccessDeniedException;
use App\Reports\Security\ModeGuard;
use Tests\TestCase;

final class ModeGuardTest extends TestCase
{
    public function test_allows_ro_when_generate_enabled(): void
    {
        config()->set('reports.mode_permissions', [
            'ro' => ['generate' => true],
            'rw' => ['generate' => true],
        ]);

        $guard = new ModeGuard();
        $context = new ReportContext(
            role: 'desa_admin',
            areaId: 10,
            areaLevel: 'desa',
            mode: 'ro',
            areaName: 'Desa A',
            filter: [],
        );

        $guard->assertCanGenerate($context);
        $this->assertTrue(true);
    }

    public function test_denies_mode_without_permission(): void
    {
        config()->set('reports.mode_permissions', [
            'ro' => ['generate' => false],
            'rw' => ['generate' => true],
        ]);

        $guard = new ModeGuard();
        $context = new ReportContext(
            role: 'desa_admin',
            areaId: 10,
            areaLevel: 'desa',
            mode: 'ro',
            areaName: 'Desa A',
            filter: [],
        );

        $this->expectException(ReportAccessDeniedException::class);
        $this->expectExceptionMessage('Mode [ro] is not allowed to generate report.');

        $guard->assertCanGenerate($context);
    }
}
