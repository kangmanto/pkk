<?php

declare(strict_types=1);

namespace App\Reports\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UnknownReportCodeException extends NotFoundHttpException
{
    public static function forCode(string $code): self
    {
        return new self("Unknown report code [{$code}].");
    }
}
