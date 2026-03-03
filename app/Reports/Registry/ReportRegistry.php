<?php

declare(strict_types=1);

namespace App\Reports\Registry;

use App\Reports\Contracts\ReportContract;
use App\Reports\Exceptions\UnknownReportCodeException;
use InvalidArgumentException;

final class ReportRegistry
{
    /**
     * @param array<string, class-string<ReportContract>> $modules
     */
    public function __construct(
        private readonly array $modules
    ) {
    }

    public function resolve(string $code): ReportContract
    {
        $class = $this->modules[$code] ?? null;
        if ($class === null) {
            throw UnknownReportCodeException::forCode($code);
        }

        $report = app($class);
        if (!$report instanceof ReportContract) {
            throw new InvalidArgumentException("Configured report [{$class}] must implement ReportContract.");
        }

        return $report;
    }

    /**
     * @return string[]
     */
    public function allCodes(): array
    {
        return array_keys($this->modules);
    }
}
