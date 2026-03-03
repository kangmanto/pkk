<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

final class ReportHeaderSnapshotTest extends TestCase
{
    public function test_header_snapshot_matches_expected_template(): void
    {
        $meta = [
            'report_code' => 'desa.population_summary',
            'orientation' => 'landscape',
            'area_name' => 'Desa A',
            'area_level' => 'desa',
            'area_id' => 10,
            'printed_by' => 'Tester',
            'printed_at' => '2026-03-04 10:00:00',
        ];

        $rendered = View::make('reports.partials.header', [
            'meta' => $meta,
        ])->render();

        $normalized = $this->normalize($rendered);
        $snapshot = trim((string) file_get_contents(__DIR__ . '/../__snapshots__/report_header.snap'));

        $this->assertSame($snapshot, $normalized);
    }

    private function normalize(string $html): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $html));
    }
}
