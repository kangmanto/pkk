<?php

declare(strict_types=1);

namespace App\Reports\Modules;

use App\Reports\Contracts\ReportContract;

abstract class BaseReport implements ReportContract
{
    public function orientation(): string
    {
        return (string) config('reports.default_orientation', 'landscape');
    }
}
