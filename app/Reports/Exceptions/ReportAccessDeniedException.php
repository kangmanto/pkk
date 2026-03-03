<?php

declare(strict_types=1);

namespace App\Reports\Exceptions;

use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

final class ReportAccessDeniedException extends AccessDeniedHttpException
{
}
