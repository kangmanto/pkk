<?php

declare(strict_types=1);

namespace App\Reports\Contracts;

use App\Reports\Data\ReportContext;
use Illuminate\Contracts\Auth\Authenticatable;

interface UserContextResolverContract
{
    public function resolve(Authenticatable $user, array $filter): ReportContext;
}
