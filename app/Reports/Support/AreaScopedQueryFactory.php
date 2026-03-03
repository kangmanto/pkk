<?php

declare(strict_types=1);

namespace App\Reports\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class AreaScopedQueryFactory
{
    public function hasTable(string $table): bool
    {
        return Schema::hasTable($table);
    }

    public function scoped(string $table, array $filter): Builder
    {
        $query = DB::table($table);

        return $query
            ->where('level', '=', (string) ($filter['level'] ?? ''))
            ->where('area_id', '=', $filter['area_id'] ?? null);
    }
}
