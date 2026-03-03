<?php

declare(strict_types=1);

namespace App\Reports\Engine;

use App\Reports\Contracts\UserContextResolverContract;
use App\Reports\Registry\ReportRegistry;
use App\Reports\Renderers\RendererManager;
use App\Reports\Security\ModeGuard;
use App\Reports\Security\ScopeGuard;
use App\Reports\Support\FilterNormalizer;
use App\Reports\Support\MetadataFactory;
use App\Reports\Support\ReportAuditLogger;
use Illuminate\Contracts\Auth\Authenticatable;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use LogicException;
use Symfony\Component\HttpFoundation\Response;

final class ReportEngine
{
    public function __construct(
        private readonly ReportRegistry $registry,
        private readonly RendererManager $rendererManager,
        private readonly ScopeGuard $scopeGuard,
        private readonly ModeGuard $modeGuard,
        private readonly FilterNormalizer $filterNormalizer,
        private readonly MetadataFactory $metadataFactory,
        private readonly ReportAuditLogger $auditLogger,
        private readonly UserContextResolverContract $userContextResolver,
    ) {
    }

    public function generate(string $code, string $format, Authenticatable $user, array $filter = []): Response
    {
        $context = null;

        try {
            $normalizedFilter = $this->filterNormalizer->normalize($filter);
            $context = $this->userContextResolver->resolve($user, $normalizedFilter);
            $report = $this->registry->resolve($code);

            $this->scopeGuard->assertCanAccess($report, $context);
            $this->modeGuard->assertCanGenerate($context);

            $enforcedFilter = $this->enforceScopeFilter($normalizedFilter, $context->areaId, $context->areaLevel);
            $result = $report->data($user, $enforcedFilter);
            if (!is_array($result)) {
                throw new LogicException("Report [{$report->code()}] must return array payload from data().");
            }

            $metadata = $this->metadataFactory->make($report, $context, $user);
            $context = $context
                ->withFilter($enforcedFilter)
                ->withMetadata($metadata);

            $response = $this->rendererManager
                ->forFormat($format)
                ->render($report, [
                    'meta' => $metadata,
                    'rows' => $result,
                ], $context);

            $this->auditLogger->generated($code, $format, $user, $context);

            return $response;
        } catch (Throwable $exception) {
            if ($this->isDenied($exception)) {
                $this->auditLogger->denied($code, $format, $user, $context, $exception);
            } else {
                $this->auditLogger->failed($code, $format, $user, $context, $exception);
            }

            throw $exception;
        }
    }

    private function enforceScopeFilter(array $filter, int|string $areaId, string $areaLevel): array
    {
        $filter['area_id'] = $areaId;
        $filter['level'] = strtolower($areaLevel);

        return $filter;
    }

    private function isDenied(Throwable $exception): bool
    {
        if ($exception instanceof AccessDeniedHttpException) {
            return true;
        }

        return $exception instanceof HttpExceptionInterface
            && $exception->getStatusCode() === 403;
    }
}
