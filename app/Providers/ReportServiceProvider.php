<?php

declare(strict_types=1);

namespace App\Providers;

use App\Reports\Contracts\RendererContract;
use App\Reports\Contracts\UserContextResolverContract;
use App\Reports\Engine\ReportEngine;
use App\Reports\Registry\ReportRegistry;
use App\Reports\Renderers\RendererManager;
use App\Reports\Security\ModeGuard;
use App\Reports\Security\ScopeGuard;
use App\Reports\Support\DefaultUserContextResolver;
use App\Reports\Support\FilterNormalizer;
use App\Reports\Support\MetadataFactory;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

final class ReportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/reports.php', 'reports');

        $this->app->singleton(UserContextResolverContract::class, function ($app): UserContextResolverContract {
            $resolverClass = (string) config('reports.user_context_resolver', DefaultUserContextResolver::class);
            $resolver = $app->make($resolverClass);
            if (!$resolver instanceof UserContextResolverContract) {
                throw new InvalidArgumentException("Resolver [{$resolverClass}] must implement UserContextResolverContract.");
            }

            return $resolver;
        });

        $this->app->singleton(ReportRegistry::class, fn (): ReportRegistry => new ReportRegistry(
            modules: (array) config('reports.modules', [])
        ));

        $this->app->singleton(RendererManager::class, function ($app): RendererManager {
            $rendererMap = [];
            foreach ((array) config('reports.renderers', []) as $format => $rendererClass) {
                $renderer = $app->make($rendererClass);
                if (!$renderer instanceof RendererContract) {
                    throw new InvalidArgumentException("Renderer [{$rendererClass}] must implement RendererContract.");
                }

                $rendererMap[strtolower((string) $format)] = $renderer;
            }

            return new RendererManager($rendererMap);
        });

        $this->app->singleton(ScopeGuard::class);
        $this->app->singleton(ModeGuard::class);
        $this->app->singleton(FilterNormalizer::class);
        $this->app->singleton(MetadataFactory::class);
        $this->app->singleton(ReportEngine::class);
    }

    public function boot(): void
    {
        if (file_exists(base_path('routes/report.php'))) {
            $this->loadRoutesFrom(base_path('routes/report.php'));
        }

        $this->publishes([
            __DIR__ . '/../../config/reports.php' => config_path('reports.php'),
        ], 'reports-config');
    }
}
