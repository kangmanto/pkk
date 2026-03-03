# Backlog Implementasi - Modular Report Engine

Tanggal: 2026-03-03
Referensi: PRD.md
Status: Ready for execution

## Konfirmasi Baseline

1. Scope valid: `desa`, `kecamatan`.
2. Mode `RO` boleh generate report read-only.
3. Semua query wajib filter `level + area_id`.
4. Orientation default: `landscape`.
5. Header metadata wajib global dan seragam.
6. Endpoint legacy diabaikan (out of scope implementasi ini).
7. Unknown report code -> `404`.
8. Validasi scope-role-area terpusat di engine.
9. Phase 1 migrasi 3 report prioritas tinggi.
10. Snapshot/golden test wajib sebelum rilis endpoint engine ke produksi.

## Work Breakdown Structure

### WS0 - Discovery dan Lock Scope

ID: `WS0-T01`  
Task: Inventaris seluruh endpoint report lama (PDF/DOCX), mapping ke `report_code`, scope, owner query.  
Acceptance Criteria:
1. Daftar endpoint existing 100% tercatat.
2. Mapping endpoint -> calon `report_code` terdokumentasi.
3. Scope tiap report terverifikasi (`desa`/`kecamatan`).

ID: `WS0-T02`  
Task: Tentukan 3 report prioritas tinggi untuk pilot Phase 1.  
Acceptance Criteria:
1. Tiga report dipilih dan disetujui.
2. Query complexity dan risiko tiap report didokumentasikan.
3. Baseline output lama tersimpan untuk golden comparison.

Pilihan pilot yang dieksekusi pada scaffold ini:
1. `desa.population_summary` (kompleksitas rendah, agregasi tunggal residents).
2. `desa.household_welfare` (kompleksitas sedang, agregasi grouped households).
3. `kecamatan.village_recaps` (kompleksitas sedang, join areas-households per desa).

### WS1 - Fondasi Engine

ID: `WS1-T01`  
Task: Buat `ReportContract` dan `ReportContext` DTO.  
Acceptance Criteria:
1. Contract memuat `code/scope/orientation/data/view`.
2. Orientation default ke `landscape` bila tidak override.
3. DTO memuat user, area, mode, filter canonical.

ID: `WS1-T02`  
Task: Buat `ReportRegistry` untuk `code -> module`.  
Acceptance Criteria:
1. Resolve code valid menghasilkan class modul.
2. Unknown code melempar exception terkontrol.
3. Registry dapat diuji via unit test.

ID: `WS1-T03`  
Task: Implement `ScopeGuard` dan `ModeGuard` di engine.  
Acceptance Criteria:
1. Validasi role-scope-area jalan sebelum query.
2. User desa tidak bisa akses report kecamatan.
3. Pelanggaran invariant menghasilkan `403`.

ID: `WS1-T04`  
Task: Implement `ReportEngine` pipeline end-to-end.  
Acceptance Criteria:
1. Urutan: authorize -> normalize filter -> inject metadata -> `data()` -> render.
2. Engine tidak bergantung ke domain spesifik report.
3. Semua report berjalan via engine API tunggal.

ID: `WS1-T05`  
Task: Buat single entry `ReportController` + route generik.  
Acceptance Criteria:
1. Route `/report/{code}/pdf` aktif.
2. Route `/report/{code}/docx` aktif.
3. Error mapping konsisten: `403`, `404`, `422`.

### WS2 - Renderer dan Standardisasi Output

ID: `WS2-T01`  
Task: Implement `RendererInterface`, `PdfRenderer`, `DocxRenderer`.  
Acceptance Criteria:
1. Dua renderer mengikuti interface yang sama.
2. Orientation mengikuti contract.
3. Streaming file konsisten (nama file, mime type).

ID: `WS2-T02`  
Task: Implement metadata canonical + global header/footer template.  
Acceptance Criteria:
1. Field wajib muncul: wilayah, level, tanggal cetak, user pencetak.
2. Struktur header seragam lintas report.
3. Tidak ada metadata diisi dari input frontend mentah.

### WS3 - Migrasi Pilot (3 Report)

ID: `WS3-T01`  
Task: Migrasi report prioritas #1 ke modul baru.  
Acceptance Criteria:
1. Modul implement `ReportContract`.
2. Query 100% enforce `level + area_id`.
3. Parity output lolos golden/snapshot test.

ID: `WS3-T02`  
Task: Migrasi report prioritas #2 ke modul baru.  
Acceptance Criteria:
1. Modul implement `ReportContract`.
2. Query 100% enforce `level + area_id`.
3. Parity output lolos golden/snapshot test.

ID: `WS3-T03`  
Task: Migrasi report prioritas #3 ke modul baru.  
Acceptance Criteria:
1. Modul implement `ReportContract`.
2. Query 100% enforce `level + area_id`.
3. Parity output lolos golden/snapshot test.

### WS5 - Quality Gate dan Security Gate

ID: `WS5-T01`  
Task: Unit test untuk contract, registry, engine guard.  
Acceptance Criteria:
1. Test registry resolve/unknown code lulus.
2. Test role-scope-area matrix inti lulus.
3. Test default orientation lulus.

ID: `WS5-T02`  
Task: Feature test matrix `role x scope x area x report_code`.  
Acceptance Criteria:
1. Kasus lintas area ditolak `403`.
2. Unknown code `404`.
3. Filter invalid `422`.

ID: `WS5-T03`  
Task: Snapshot/golden test header PDF dan parity pilot.  
Acceptance Criteria:
1. Header seragam antar report.
2. Selisih output kritikal di luar threshold = fail.
3. Gate ini wajib hijau sebelum rilis ke produksi.

ID: `WS5-T04`  
Task: Guard rail CI untuk query tanpa filter area.  
Acceptance Criteria:
1. Rule CI mendeteksi query report tanpa `level + area_id`.
2. Pelanggaran rule memblok merge.
3. Dokumentasi pengecualian tidak diperlukan karena policy tanpa pengecualian.

## Urutan Eksekusi

1. `WS0` -> lock daftar report dan pilih 3 pilot.
2. `WS1` -> rilis fondasi engine + route/controller tunggal.
3. `WS2` -> rilis renderer + metadata global.
4. `WS3` -> migrasi 3 report pilot hingga parity.
5. `WS5` -> quality gate wajib hijau.
6. Rilis endpoint engine baru langsung ke produksi (tanpa adapter legacy).

## Milestone Delivery

M1: Fondasi siap (`WS0-WS1`)  
Exit Criteria:
1. Route generik aktif.
2. Engine dan guard aktif.
3. Registry berjalan.

M2: Standardized rendering siap (`WS2`)  
Exit Criteria:
1. PDF/DOCX renderer aktif.
2. Header/footer global aktif.
3. Orientation by contract aktif.

M3: Pilot parity selesai (`WS3 + WS5`)  
Exit Criteria:
1. 3 report pilot live via engine.
2. Golden/snapshot test hijau.
3. Tidak ada leak lintas area.

M4: Production rollout (No legacy adapter)  
Exit Criteria:
1. Endpoint engine baru aktif di produksi.
2. Monitoring tidak menunjukkan pelanggaran scope-role-area.
3. Golden/snapshot gate tetap hijau setelah rilis.

## Definition of Ready per Task

1. Scope report jelas (`desa`/`kecamatan`).
2. Sumber query dan tabel teridentifikasi.
3. Expected output contoh tersedia.
4. Risiko akses lintas area sudah dipetakan.

## Definition of Done per Task

1. Kode merge ke branch utama dengan test relevan.
2. Tidak ada bypass engine pada jalur report.
3. Audit log/error handling sesuai standar.
4. Dokumentasi singkat task ditambahkan.
