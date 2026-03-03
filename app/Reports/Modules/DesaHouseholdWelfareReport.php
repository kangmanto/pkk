<?php

declare(strict_types=1);

namespace App\Reports\Modules;

use App\Reports\Support\AreaScopedQueryFactory;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

final class DesaHouseholdWelfareReport extends BaseReport
{
    public function __construct(
        private readonly AreaScopedQueryFactory $queryFactory
    ) {
    }

    public function code(): string
    {
        return 'desa.household_welfare';
    }

    public function scope(): string
    {
        return 'desa';
    }

    public function data(Authenticatable $user, array $filter): array
    {
        $table = (string) config('reports.tables.households', 'households');
        if (!$this->queryFactory->hasTable($table)) {
            return [];
        }

        /** @var Collection<int, object> $rows */
        $rows = $this->queryFactory
            ->scoped($table, $filter)
            ->selectRaw(
                "welfare_status,
                COUNT(*) as household_count,
                SUM(COALESCE(member_count, 0)) as total_members"
            )
            ->groupBy('welfare_status')
            ->orderBy('welfare_status')
            ->get();

        return $rows->map(static fn (object $row): array => [
            'welfare_status' => (string) ($row->welfare_status ?? 'unknown'),
            'household_count' => (int) ($row->household_count ?? 0),
            'total_members' => (int) ($row->total_members ?? 0),
        ])->all();
    }

    public function view(): string
    {
        return 'reports.desa-household-welfare';
    }
}
