<?php

declare(strict_types=1);

namespace App\Reports\Modules;

use App\Reports\Support\AreaScopedQueryFactory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

final class DesaPopulationSummaryReport extends BaseReport
{
    public function __construct(
        private readonly AreaScopedQueryFactory $queryFactory
    ) {
    }

    public function code(): string
    {
        return 'desa.population_summary';
    }

    public function scope(): string
    {
        return 'desa';
    }

    public function data(Authenticatable $user, array $filter): array
    {
        $table = (string) config('reports.tables.residents', 'residents');
        if (!$this->queryFactory->hasTable($table)) {
            return [];
        }

        /** @var Collection<int, object> $rows */
        $rows = $this->queryFactory
            ->scoped($table, $filter)
            ->selectRaw(
                "COUNT(*) as total_residents,
                SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) as male_total,
                SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) as female_total"
            )
            ->get();

        return $rows->map(static fn (object $row): array => [
            'total_residents' => (int) ($row->total_residents ?? 0),
            'male_total' => (int) ($row->male_total ?? 0),
            'female_total' => (int) ($row->female_total ?? 0),
        ])->all();
    }

    public function view(): string
    {
        return 'reports.desa-population-summary';
    }
}
