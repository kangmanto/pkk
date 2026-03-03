<?php

declare(strict_types=1);

namespace App\Reports\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface ReportContract
{
    public function code(): string;

    public function scope(): string;

    public function orientation(): string;

    public function data(Authenticatable $user, array $filter): array;

    public function view(): string;
}
