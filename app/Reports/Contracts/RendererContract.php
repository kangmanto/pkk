<?php

declare(strict_types=1);

namespace App\Reports\Contracts;

use App\Reports\Data\ReportContext;
use Symfony\Component\HttpFoundation\Response;

interface RendererContract
{
    public function render(ReportContract $report, array $data, ReportContext $context): Response;
}
