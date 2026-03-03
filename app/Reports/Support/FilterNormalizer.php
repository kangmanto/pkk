<?php

declare(strict_types=1);

namespace App\Reports\Support;

use App\Reports\Exceptions\InvalidReportFilterException;

final class FilterNormalizer
{
    public function normalize(array $filter): array
    {
        $normalized = [];
        foreach ($filter as $key => $value) {
            if (!is_string($key) || $key === '') {
                throw InvalidReportFilterException::because('filter key must be non-empty string');
            }

            $this->assertFilterValue($value, $key);
            $normalized[$key] = $value;
        }

        $allowed = (array) config('reports.allowed_filters', []);
        if ($allowed !== []) {
            $unknown = array_diff(array_keys($normalized), $allowed);
            if ($unknown !== []) {
                throw InvalidReportFilterException::because('unknown filter keys: ' . implode(', ', $unknown));
            }
        }

        return $normalized;
    }

    private function assertFilterValue(mixed $value, string $key): void
    {
        if (is_scalar($value) || $value === null) {
            return;
        }

        if (!is_array($value)) {
            throw InvalidReportFilterException::because("filter [{$key}] must be scalar, null, or array");
        }

        foreach ($value as $item) {
            if (!is_scalar($item) && $item !== null) {
                throw InvalidReportFilterException::because("filter [{$key}] contains invalid nested value");
            }
        }
    }
}
