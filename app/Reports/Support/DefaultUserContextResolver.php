<?php

declare(strict_types=1);

namespace App\Reports\Support;

use App\Reports\Contracts\UserContextResolverContract;
use App\Reports\Data\ReportContext;
use App\Reports\Exceptions\ReportAccessDeniedException;
use Illuminate\Contracts\Auth\Authenticatable;

final class DefaultUserContextResolver implements UserContextResolverContract
{
    public function resolve(Authenticatable $user, array $filter): ReportContext
    {
        $role = $this->firstNonEmpty([
            data_get($user, 'role'),
            data_get($user, 'role_code'),
            data_get($user, 'role.name'),
        ]);

        $areaId = $this->firstNonEmpty([
            data_get($user, 'area_id'),
            data_get($user, 'area.id'),
        ]);

        $areaLevel = $this->firstNonEmpty([
            data_get($user, 'area_level'),
            data_get($user, 'level'),
            data_get($user, 'area.level'),
        ]);

        if ($role === null || $areaId === null || $areaLevel === null) {
            throw new ReportAccessDeniedException('User context is incomplete for report access.');
        }

        $mode = strtolower((string) ($this->firstNonEmpty([
            data_get($user, 'mode'),
            data_get($user, 'access_mode'),
        ]) ?? 'rw'));

        $areaName = (string) ($this->firstNonEmpty([
            data_get($user, 'area_name'),
            data_get($user, 'area.name'),
        ]) ?? "Area {$areaId}");

        return new ReportContext(
            role: strtolower((string) $role),
            areaId: is_numeric((string) $areaId) ? (int) $areaId : (string) $areaId,
            areaLevel: strtolower((string) $areaLevel),
            mode: $mode,
            areaName: $areaName,
            filter: $filter,
        );
    }

    private function firstNonEmpty(array $values): mixed
    {
        foreach ($values as $value) {
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }
}
