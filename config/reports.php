<?php

declare(strict_types=1);

return [
    'default_orientation' => 'landscape',
    'supported_scopes' => [
        'desa',
        'kecamatan',
    ],
    'modules' => [
        'desa.population_summary' => \App\Reports\Modules\DesaPopulationSummaryReport::class,
        'desa.household_welfare' => \App\Reports\Modules\DesaHouseholdWelfareReport::class,
        'kecamatan.village_recaps' => \App\Reports\Modules\KecamatanVillageRecapsReport::class,
        'sample.village_profile' => \App\Reports\Modules\SampleVillageProfileReport::class,
    ],
    'renderers' => [
        'pdf' => \App\Reports\Renderers\PdfRenderer::class,
        'docx' => \App\Reports\Renderers\DocxRenderer::class,
    ],
    'role_scope_map' => [
        'desa_admin' => 'desa',
        'kecamatan_admin' => 'kecamatan',
    ],
    'mode_permissions' => [
        'ro' => ['generate' => true],
        'rw' => ['generate' => true],
    ],
    'user_context_resolver' => \App\Reports\Support\DefaultUserContextResolver::class,
    'allowed_filters' => [
        'start_date',
        'end_date',
        'status',
        'search',
        'category',
        'page',
        'per_page',
    ],
    'tables' => [
        'residents' => 'residents',
        'households' => 'households',
        'areas' => 'areas',
    ],
];
