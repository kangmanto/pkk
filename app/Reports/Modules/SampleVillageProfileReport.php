<?php

declare(strict_types=1);

namespace App\Reports\Modules;

use Illuminate\Contracts\Auth\Authenticatable;

final class SampleVillageProfileReport extends BaseReport
{
    public function code(): string
    {
        return 'sample.village_profile';
    }

    public function scope(): string
    {
        return 'desa';
    }

    public function data(Authenticatable $user, array $filter): array
    {
        return [
            [
                'title' => 'Sample Village Profile Report',
                'area_id' => $filter['area_id'] ?? null,
                'level' => $filter['level'] ?? null,
                'generated_at' => now()->toDateTimeString(),
                'requested_by' => data_get($user, 'name', 'unknown'),
            ],
        ];
    }

    public function view(): string
    {
        return 'reports.sample-village-profile';
    }
}
