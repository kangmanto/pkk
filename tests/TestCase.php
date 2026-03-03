<?php

declare(strict_types=1);

namespace Tests;

use App\Providers\ReportServiceProvider;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['view']->addLocation(base_path('resources/views'));
    }

    protected function getPackageProviders($app): array
    {
        return [
            ReportServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:' . base64_encode(str_repeat('a', 32)));
        $app['config']->set('view.paths', [realpath(__DIR__ . '/../resources/views') ?: __DIR__ . '/../resources/views']);
        $app['config']->set('reports.default_orientation', 'landscape');
        $app['config']->set('reports.supported_scopes', ['desa', 'kecamatan']);
        $app['config']->set('reports.role_scope_map', [
            'desa_admin' => 'desa',
            'kecamatan_admin' => 'kecamatan',
        ]);
        $app['config']->set('reports.mode_permissions', [
            'ro' => ['generate' => true],
            'rw' => ['generate' => true],
        ]);
        $app['config']->set('reports.allowed_filters', [
            'status',
            'start_date',
            'end_date',
        ]);
        $app['config']->set('reports.user_context_resolver', \App\Reports\Support\DefaultUserContextResolver::class);
    }

    protected function defineRoutes($router): void
    {
        Route::middleware([])->prefix('report')->group(function (): void {
            Route::get('{code}/pdf', [\App\Http\Controllers\ReportController::class, 'pdf']);
            Route::get('{code}/docx', [\App\Http\Controllers\ReportController::class, 'docx']);
        });
    }
}
