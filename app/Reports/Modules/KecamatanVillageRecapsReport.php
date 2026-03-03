<?php

declare(strict_types=1);

namespace App\Reports\Modules;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class KecamatanVillageRecapsReport extends BaseReport
{
    public function code(): string
    {
        return 'kecamatan.village_recaps';
    }

    public function scope(): string
    {
        return 'kecamatan';
    }

    public function data(Authenticatable $user, array $filter): array
    {
        $areasTable = (string) config('reports.tables.areas', 'areas');
        $householdsTable = (string) config('reports.tables.households', 'households');

        if (!Schema::hasTable($areasTable) || !Schema::hasTable($householdsTable)) {
            return [];
        }

        /** @var Collection<int, object> $rows */
        $rows = DB::table($areasTable . ' as a')
            ->leftJoin($householdsTable . ' as h', function ($join): void {
                $join->on('h.area_id', '=', 'a.id')
                    ->where('h.level', '=', 'desa');
            })
            ->where('a.parent_id', '=', $filter['area_id'])
            ->where('a.level', '=', 'desa')
            ->selectRaw(
                "a.id as desa_id,
                a.name as desa_name,
                COUNT(h.id) as household_count,
                SUM(COALESCE(h.member_count, 0)) as resident_count"
            )
            ->groupBy('a.id', 'a.name')
            ->orderBy('a.name')
            ->get();

        return $rows->map(static fn (object $row): array => [
            'desa_id' => (int) ($row->desa_id ?? 0),
            'desa_name' => (string) ($row->desa_name ?? '-'),
            'household_count' => (int) ($row->household_count ?? 0),
            'resident_count' => (int) ($row->resident_count ?? 0),
        ])->all();
    }

    public function view(): string
    {
        return 'reports.kecamatan-village-recaps';
    }
}
