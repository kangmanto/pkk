# PRD – Modular Report Engine

Tanggal: 2026-03-03\
Status: Draft Arsitektural\
Baseline: Laravel 12 + Inertia + Vue 3 + Tailwind + Vite

---

## 1. Latar Belakang

Sistem saat ini memiliki 100+ endpoint report (PDF/DOCX) lintas modul desa dan kecamatan. Struktur tersebut berfungsi, namun memiliki risiko:

- Duplikasi logic query dan metadata
- Drift format header/footer
- Potensi kebocoran scope wilayah
- Sulit regression test dan audit
- Kompleksitas controller yang meningkat

Dokumen ini mendefinisikan improvisasi arsitektur report menjadi modular engine berbasis kontrak.

---

## 2. Problem Statement

Report saat ini tersebar di banyak controller dan tidak memiliki abstraction layer terpusat.

Dampak:

- Scope enforcement tidak selalu konsisten
- Orientation (landscape/portrait) tidak terstandarisasi
- Metadata wilayah dapat tidak sinkron
- Refactor berisiko tinggi karena coupling tersebar

---

## 3. Tujuan Improvisasi

1. Menyatukan semua report dalam satu Report Engine.
2. Menjadikan setiap report sebagai modul berbasis kontrak.
3. Memusatkan enforcement scope-role-area.
4. Menstandarisasi metadata, orientation, dan header.
5. Menurunkan surface complexity endpoint report.

---

## 4. Prinsip Arsitektur

1. Backend tetap authority penuh.
2. `areas` tetap source of truth wilayah.
3. Scope enforcement dilakukan sebelum data projection.
4. Report dianggap sebagai projection layer, bukan logic domain.
5. Default orientation: landscape.

---

## 5. Arsitektur Target

### 5.1 Layer Diagram Konseptual

Route
→ ReportController (single entry)
→ ReportEngine
→ ReportModule (pluggable contract)
→ Renderer (PDF/DOCX)

---

## 6. Komponen Utama

### 6.1 ReportContract

Setiap report wajib mengimplementasikan kontrak berikut:

- code(): string
- scope(): string (desa/kecamatan)
- orientation(): string (default landscape)
- data(User \$user, array \$filter): array
- view(): string

### 6.2 ReportEngine

Tanggung jawab:

- Validasi scope-role-area
- Validasi mode RO/RW
- Inject metadata canonical
- Memanggil data() dari modul
- Delegasi rendering ke renderer

Engine tidak mengetahui detail domain spesifik modul.

### 6.3 Renderer Abstraction

Interface:

- render(ReportContract \$report, array \$data)

Implementasi:

- PDFRenderer
- DOCXRenderer

Renderer bertanggung jawab atas:

- Header/footer global
- Orientation
- Streaming file

---

## 7. Routing Strategy

Dari banyak endpoint menjadi pola generik:

/report/{code}/pdf\
/report/{code}/docx

Mapping dilakukan melalui ReportRegistry.

---

## 8. Scope Enforcement Model

Validasi dilakukan sebelum data diproses:

1. Role cocok dengan scope report.
2. area.level cocok dengan scope.
3. Mode RO tidak boleh mengakses write-related generation.
4. Data query selalu difilter level + area\_id.

---

## 9. User Stories

### Epic A – Engine Integration

Sebagai user scoped, saya hanya dapat mencetak report yang sesuai wilayah saya.

AC:

- Akses lintas area ditolak (403).
- Report kecamatan tidak dapat diakses user desa.
- Metadata wilayah pada header sesuai area user.

### Epic B – Modularization

Sebagai developer, saya dapat menambahkan report baru tanpa membuat controller baru.

AC:

- Report baru cukup membuat class implementasi kontrak.
- Tidak ada perubahan pada controller utama.

### Epic C – Standardization

Sebagai user, seluruh report memiliki format konsisten.

AC:

- Header dan metadata seragam.
- Orientation mengikuti kontrak.
- Tidak ada perbedaan format tidak terdefinisi.

---

## 10. Non-Functional Requirements

### Security

- 100% scope enforcement terpusat di engine.

### Testability

- Feature test matrix role × scope × area × report-code.
- Snapshot test PDF header consistency.

### Maintainability

- Tidak ada duplikasi logic metadata.
- Tidak ada query lintas area tanpa filter eksplisit.

### Performance

- Report berat dapat di-evolusi ke async generation (future scope).

---

## 11. Strategi Migrasi

### Phase 1

- Implementasi engine dan renderer.
- Migrasi 3 report prioritas tinggi.

### Phase 2

- Adapter endpoint lama memanggil engine baru.

### Phase 3

- Deprecate controller lama secara bertahap.

Tidak ada big-bang rewrite.

---

## 12. Risiko dan Mitigasi

Risiko: Drift data karena query lama tidak terstandarisasi.\
Mitigasi: Audit query + enforce filter level/area.

Risiko: Privilege override merusak invariant.\
Mitigasi: Engine tetap memvalidasi invariant sebelum render.

Risiko: Regression report lama.\
Mitigasi: Snapshot test sebelum dan sesudah migrasi.

---

## 13. KPI Refactor

1. Pengurangan jumlah controller report ≥ 70%.
2. 100% report melewati engine terpusat.
3. 0 data leak lintas wilayah.
4. 100% orientation mengikuti kontrak.

---

## 14. Definition of Done

1. Seluruh report prioritas tinggi berjalan via engine.
2. Test matrix hijau.
3. Tidak ada endpoint write lolos untuk mode RO.
4. Tidak ada pelanggaran invariant role-scope-area.

---

## 15. Outcome Strategis

Refactor ini menurunkan entropy sistem, memusatkan kontrol akses, dan menjadikan report sebagai projection layer yang deterministic dan terukur.

Dokumen ini tidak mengubah domain runtime, namun menguatkan fondasi arsitektur jangka panjang.

