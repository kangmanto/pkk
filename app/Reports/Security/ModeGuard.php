<?php

declare(strict_types=1);

namespace App\Reports\Security;

use App\Reports\Data\ReportContext;
use App\Reports\Exceptions\ReportAccessDeniedException;

final class ModeGuard
{
    public function assertCanGenerate(ReportContext $context): void
    {
        $mode = strtolower($context->mode);
        $canGenerate = (bool) data_get(config('reports.mode_permissions', []), "{$mode}.generate", false);

        if (!$canGenerate) {
            throw new ReportAccessDeniedException("Mode [{$mode}] is not allowed to generate report.");
        }
    }
}
