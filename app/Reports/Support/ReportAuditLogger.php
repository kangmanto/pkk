<?php

declare(strict_types=1);

namespace App\Reports\Support;

use App\Reports\Data\ReportContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Throwable;

final class ReportAuditLogger
{
    public function generated(string $code, string $format, Authenticatable $user, ReportContext $context): void
    {
        if (!$this->enabled()) {
            return;
        }

        $this->logger()->info('report.generated', $this->payload($code, $format, $user, $context));
    }

    public function denied(string $code, string $format, Authenticatable $user, ?ReportContext $context, Throwable $exception): void
    {
        if (!$this->enabled()) {
            return;
        }

        $payload = $this->payload($code, $format, $user, $context);
        $payload['error'] = $exception->getMessage();
        $payload['exception'] = $exception::class;

        $this->logger()->warning('report.denied', $payload);
    }

    public function failed(string $code, string $format, Authenticatable $user, ?ReportContext $context, Throwable $exception): void
    {
        if (!$this->enabled()) {
            return;
        }

        $payload = $this->payload($code, $format, $user, $context);
        $payload['error'] = $exception->getMessage();
        $payload['exception'] = $exception::class;

        $this->logger()->error('report.failed', $payload);
    }

    private function payload(string $code, string $format, Authenticatable $user, ?ReportContext $context): array
    {
        return [
            'report_code' => $code,
            'format' => strtolower($format),
            'user_id' => $user->getAuthIdentifier(),
            'role' => $context?->role,
            'area_id' => $context?->areaId,
            'area_level' => $context?->areaLevel,
            'mode' => $context?->mode,
        ];
    }

    private function enabled(): bool
    {
        return (bool) config('reports.audit.enabled', true);
    }

    private function logger(): \Psr\Log\LoggerInterface
    {
        $channel = config('reports.audit.log_channel');
        if (is_string($channel) && $channel !== '') {
            return Log::channel($channel);
        }

        return Log::channel((string) config('logging.default', 'stack'));
    }
}
