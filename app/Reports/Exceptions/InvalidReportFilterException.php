<?php

declare(strict_types=1);

namespace App\Reports\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class InvalidReportFilterException extends UnprocessableEntityHttpException
{
    public static function because(string $reason): self
    {
        return new self("Invalid report filter: {$reason}");
    }
}
