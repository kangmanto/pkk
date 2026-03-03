<?php

declare(strict_types=1);

namespace App\Reports\Renderers;

use App\Reports\Contracts\RendererContract;
use App\Reports\Exceptions\InvalidReportFormatException;

final class RendererManager
{
    /**
     * @param array<string, RendererContract> $renderers
     */
    public function __construct(
        private readonly array $renderers
    ) {
    }

    public function forFormat(string $format): RendererContract
    {
        $normalized = strtolower($format);
        $renderer = $this->renderers[$normalized] ?? null;
        if ($renderer === null) {
            throw InvalidReportFormatException::forFormat($format);
        }

        return $renderer;
    }
}
