<?php

declare(strict_types=1);

namespace App\Reports\Data;

final class ReportContext
{
    public function __construct(
        public readonly string $role,
        public readonly int|string $areaId,
        public readonly string $areaLevel,
        public readonly string $mode,
        public readonly string $areaName,
        public readonly array $filter,
        public readonly array $metadata = [],
    ) {
    }

    public function withFilter(array $filter): self
    {
        return new self(
            role: $this->role,
            areaId: $this->areaId,
            areaLevel: $this->areaLevel,
            mode: $this->mode,
            areaName: $this->areaName,
            filter: $filter,
            metadata: $this->metadata,
        );
    }

    public function withMetadata(array $metadata): self
    {
        return new self(
            role: $this->role,
            areaId: $this->areaId,
            areaLevel: $this->areaLevel,
            mode: $this->mode,
            areaName: $this->areaName,
            filter: $this->filter,
            metadata: $metadata,
        );
    }
}
