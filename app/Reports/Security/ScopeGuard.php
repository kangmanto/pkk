<?php

declare(strict_types=1);

namespace App\Reports\Security;

use App\Reports\Contracts\ReportContract;
use App\Reports\Data\ReportContext;
use App\Reports\Exceptions\ReportAccessDeniedException;
use LogicException;

final class ScopeGuard
{
    public function assertCanAccess(ReportContract $report, ReportContext $context): void
    {
        $scope = strtolower($report->scope());
        $areaLevel = strtolower($context->areaLevel);
        $role = strtolower($context->role);

        $supportedScopes = (array) config('reports.supported_scopes', ['desa', 'kecamatan']);
        if (!in_array($scope, $supportedScopes, true)) {
            throw new LogicException("Report [{$report->code()}] has unsupported scope [{$scope}].");
        }

        if (!in_array($areaLevel, $supportedScopes, true)) {
            throw new ReportAccessDeniedException("Unsupported user area level [{$areaLevel}].");
        }

        if ($scope !== $areaLevel) {
            throw new ReportAccessDeniedException('Role-scope-area invariant violated.');
        }

        $roleScopeMap = (array) config('reports.role_scope_map', []);
        $roleScope = $roleScopeMap[$role] ?? null;
        if ($roleScope === null) {
            throw new ReportAccessDeniedException("Role [{$role}] is not allowed to access reports.");
        }

        if (strtolower((string) $roleScope) !== $scope) {
            throw new ReportAccessDeniedException('User role cannot access report with this scope.');
        }
    }
}
