<?php

declare(strict_types=1);

namespace App\Reports\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class InvalidReportFormatException extends NotFoundHttpException
{
    public static function forFormat(string $format): self
    {
        return new self("Unsupported report format [{$format}].");
    }
}
