# Modular Report Engine (Scaffold)

Implementasi awal berdasarkan `PRD.md` untuk fondasi Report Engine modular.

Catatan keputusan saat ini: endpoint legacy diabaikan; implementasi difokuskan ke endpoint engine baru.

## Komponen yang sudah dibuat

1. `ReportContract`, `RendererContract`, `UserContextResolverContract`.
2. `ReportEngine` dengan pipeline:
   - normalize filter
   - resolve user context
   - scope + mode guard
   - enforce `level + area_id`
   - render via renderer manager
3. `ReportRegistry` untuk mapping `report_code -> module`.
4. `PdfRenderer` (Dompdf) dan `DocxRenderer` (PHPWord) untuk output file binary.
5. `ReportController` + route generik:
   - `/report/{code}/pdf`
   - `/report/{code}/docx`
6. Metadata canonical dan shared report header view.
7. Tiga modul pilot Phase 1:
   - `desa.population_summary`
   - `desa.household_welfare`
   - `kecamatan.village_recaps`
8. Audit logging report terstruktur (`report.generated`, `report.denied`, `report.failed`).

## Integrasi ke aplikasi Laravel

1. Register provider: `App\Providers\ReportServiceProvider::class`.
2. Pastikan model user memiliki atribut:
   - `role` atau `role_code`
   - `area_id`
   - `area_level`
   - optional: `mode`, `area_name`
3. Tambahkan modul report ke `config/reports.php`.
4. Pastikan dependency composer terpasang (`dompdf/dompdf`, `phpoffice/phpword`).

## Menjalankan Aplikasi

Repo ini sekarang sudah memiliki skeleton Laravel 12 lengkap (`artisan`, `bootstrap`, `public`, `config`, `storage`, `database`).

Langkah run lokal:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Endpoint report:

1. `/report/{code}/pdf`
2. `/report/{code}/docx`

## Pilot Phase 1

Endpoint baru yang siap dipakai:

1. `/report/desa.population_summary/pdf`
2. `/report/desa.household_welfare/pdf`
3. `/report/kecamatan.village_recaps/pdf`
4. Format `.docx` tersedia dengan pola endpoint yang sama.

## Quality Gate

Guard rail `WS5-T04` sudah ditambahkan untuk memastikan modul report tidak melanggar filter area wajib.

Jalankan:

```bash
bash scripts/ci/check_report_scope_filters.sh
```

CI GitHub Actions akan menjalankan guard yang sama pada `push` dan `pull_request`.
Workflow juga menjalankan `composer install` dan seluruh `phpunit` suite.

## Test Suite WS5

File test yang sudah disiapkan:

1. Unit:
   - `tests/Unit/ReportRegistryTest.php`
   - `tests/Unit/ScopeGuardTest.php`
   - `tests/Unit/ModeGuardTest.php`
2. Feature:
   - `tests/Feature/ReportEngineAuthorizationMatrixTest.php`
   - `tests/Feature/ReportEngineErrorMappingTest.php`
   - `tests/Feature/ReportHeaderSnapshotTest.php`
   - `tests/Feature/ReportEndpointResponseTest.php`
3. Snapshot fixture:
   - `tests/__snapshots__/report_header.snap`

Menjalankan test (setelah dependency PHPUnit + Testbench tersedia di composer):

```bash
vendor/bin/phpunit -c phpunit.xml.dist
```
