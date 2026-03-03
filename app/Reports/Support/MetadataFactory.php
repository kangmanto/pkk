<?php

declare(strict_types=1);

namespace App\Reports\Support;

use App\Reports\Contracts\ReportContract;
use App\Reports\Data\ReportContext;
use Illuminate\Contracts\Auth\Authenticatable;

final class MetadataFactory
{
    public function make(ReportContract $report, ReportContext $context, Authenticatable $user): array
    {
        return [
            'report_code' => $report->code(),
            'orientation' => $report->orientation(),
            'area_name' => $context->areaName,
            'area_level' => $context->areaLevel,
            'area_id' => $context->areaId,
            'printed_at' => now()->toDateTimeString(),
            'printed_by' => (string) data_get($user, 'name', (string) $user->getAuthIdentifier()),
        ];
    }
}
