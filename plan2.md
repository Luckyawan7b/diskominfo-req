# Plan Implementasi — Modul 2.0 (Layanan Digital Prioritas) & 3.0 (Peta Risiko & Monitoring)

**Untuk:** Coding agent yang melanjutkan pekerjaan di repo Laravel "Manajemen Risiko SPBE Desa/Pemda"
**Status saat ini:** Formulir 0.0 (Penetapan Konteks) dan 1.0 (Profil & Penilaian Risiko) SUDAH SELESAI dan berjalan.
**Yang dikerjakan di plan ini:** Memecah Formulir 2.0 dan 3.0 menjadi halaman terpisah sesuai permintaan mitra (setiap tahap = 1 halaman sendiri), TANPA migration baru — skema database sudah mendukung semua kolom yang dibutuhkan.

---

## 1. Sumber Acuan

File Excel resmi: `24072026_Contoh_Pengisian_Daerah__07072026_MRBasic_Version_Revised.xlsx`, sheet:
- `2.0 Daftar Layanan Digital P`
- `3.0 Peta Risiko dan Monitori` (berisi 2 sub-formulir: **3.0 Peta Risiko** dan **3.1 Laporan Pemantauan Risiko**)

## 2. Pemetaan Kolom Excel → Kolom Database (sudah tersedia, tidak perlu migration)

### Formulir 2.0 — Daftar Layanan Digital Prioritas

| No | Kolom Excel | Sumber Data (read-only / auto) | Kolom Database (diisi user) |
|---|---|---|---|
| 1 | No | urutan tampil (index) | — |
| 2 | Layanan Prioritas (nama layanan vital) | `mr_kolom_tambahan.layanan_pendukung` (ditentukan di Formulir 1.0 Tab 5) | — |
| 3 | Kode Risiko | `mr_risiko.kode_risiko` (otomatis) | — |
| 4 | Besaran Risiko | `mr_risiko.besaran_risiko` (otomatis dari matriks) | — |
| 5 | Perlu MKB? (Ya/Tidak) | — | `mr_layanan_digital.perlu_mkb` |
| 6 | PIC | — | `mr_layanan_digital.pic` |
| 7 | Target Waktu Penyusunan | — | `mr_layanan_digital.target_waktu_penyusunan` |

**Sumber baris**: hanya risiko dalam 1 `mr_konteks` yang punya `mr_kolom_tambahan.layanan_prioritas = 'Prioritas'`. Baris `mr_layanan_digital` untuk risiko ini sudah otomatis dibuat oleh `MrKolomTambahanObserver` — jadi tinggal `updateOrCreate` di halaman baru ini.

### Formulir 3.0 — Peta Risiko

Sudah 100% diimplementasikan di `app/Livewire/Risiko/PetaRisiko.php` + `resources/views/livewire/risiko/peta.blade.php`, route `risiko.peta`. **Tidak perlu perubahan**, hanya perlu disatukan secara navigasi dengan 3.1 (lihat §4).

### Formulir 3.1 — Laporan Pemantauan Risiko (Semester I & II)

| Kolom Excel | Sumber Data |
|---|---|
| ID Risiko, Risiko | `mr_risiko.kode_risiko`, `peristiwa_risiko` |
| Besaran/Level Risiko Saat Ini | `mr_risiko.besaran_risiko` |
| **Proyeksi Risiko** | `mr_risiko_residual.besaran_risiko` — **sudah ada di DB tapi BELUM ditampilkan di halaman Pemantauan, perlu ditambahkan** |
| Perlakuan Risiko | `mr_risiko_perlakuan.keputusan_perlakuan` |
| Rencana Penanganan | `mr_risiko_perlakuan.deskripsi_detail_perlakuan` |
| Penanggung Jawab | `mr_risiko_perlakuan.penanggung_jawab` |
| Waktu Pelaksanaan | `mr_risiko_perlakuan.waktu_rencana_perlakuan` |
| Hasil Pelaksanaan | `mr_pemantauan_risiko.hasil_pelaksanaan` (diisi user, sudah ada formnya) |
| Data Dukung | `mr_pemantauan_risiko.data_dukung_catatan` + `mr_lampiran` (file upload, sudah ada) |
| Semester I / II | `mr_pemantauan_risiko.periode` (nilai saat ini `'Semester 1'/'Semester 2'/'Tahunan'` — cukup, tidak perlu diubah) |

---

## 3. Tugas A — Halaman Baru: Layanan Digital Prioritas (Modul 2.0)

### 3.1 Buat route baru
File: `routes/web.php`, tambahkan di dalam `Route::prefix('konteks/{konteks}')->group(...)`, setelah `risiko.index`:

```php
Route::get('/layanan-digital', LayananDigitalIndex::class)->name('layanan-digital.index');
```

Tambahkan `use App\Livewire\LayananDigital\LayananDigitalIndex;` di bagian import.

### 3.2 Buat Livewire Component
File baru: `app/Livewire/LayananDigital/LayananDigitalIndex.php`

Spesifikasi:
- `public MrKonteks $konteks;`
- `mount(MrKonteks $konteks)` — set `$this->konteks`.
- `render()`: ambil daftar risiko dengan:
  ```php
  $this->konteks->risiko()
      ->whereHas('kolomTambahan', fn ($q) => $q->where('layanan_prioritas', 'Prioritas'))
      ->with(['kolomTambahan', 'layananDigital'])
      ->orderBy('kode_risiko')
      ->get();
  ```
- Method `saveItem(int $risikoId, ?bool $perluMkb, ?string $pic, ?string $targetWaktu)` atau pola Livewire array-binding seperti `SasaranForm` (array `$items` yang di-loop di Blade, tiap baris punya tombol simpan) — ikuti pola `MrLayananDigital::updateOrCreate(['mr_risiko_id' => $risikoId], [...])`.
- `isEditable` — pakai aturan sama seperti modul lain: `$this->konteks->isEditableByOperator() || auth()->user()->isAdmin()`.
- Breadcrumb mengikuti pola modul lain (`Manajemen Risiko` → `Konteks {tahun}` → `Layanan Digital Prioritas`).
- Layout: `#[Layout('components.layouts.app')]`.

### 3.3 Buat View
File baru: `resources/views/livewire/layanan-digital/index.blade.php`

- Header halaman: "Daftar Layanan Digital Prioritas — Formulir 2.0".
- Tampilkan `<x-risk-wizard :konteks="$konteks" activeStep="Layanan Digital" />` (lihat §5 untuk update komponen wizard).
- Tabel dengan kolom: No, Layanan Prioritas (dari `kolomTambahan->layanan_pendukung`), Kode Risiko, Besaran Risiko (badge warna sama seperti di `risiko/index.blade.php`), Perlu MKB? (toggle/select Ya-Tidak), PIC (input text), Target Waktu Penyusunan (input text), tombol Simpan per baris (atau simpan semua).
- State kosong: jika tidak ada risiko dengan `layanan_prioritas = 'Prioritas'`, tampilkan pesan "Belum ada layanan digital prioritas. Tandai risiko sebagai Prioritas di Formulir 1.0 (Kolom Tambahan) terlebih dahulu." + link ke `risiko.index`.
- Ikuti gaya visual dark slate + emerald yang sudah konsisten di seluruh aplikasi (lihat `risiko/index.blade.php` sebagai referensi styling tabel).

### 3.4 Ubah RisikoForm (Tab 5) — pindahkan field MKB/PIC/Target keluar
File: `resources/views/livewire/risiko/form.blade.php`

- **Hapus** blok berikut dari Tab 5 (mulai `@if($layanan_prioritas === 'Prioritas')` sampai `@endif` yang berisi checkbox `perlu_mkb`, input `pic`, input `target_waktu_penyusunan`).
- **Pertahankan** field penentu (`layanan_pendukung`, `layanan_prioritas` select, `pemilik_layanan`, dst.) — field ini tetap di Formulir 1.0 karena itu yang men-trigger apakah risiko masuk ke daftar 2.0.
- Sebagai gantinya, jika `$layanan_prioritas === 'Prioritas'`, tampilkan info box kecil: "Risiko ini akan muncul di [Formulir 2.0 — Daftar Layanan Digital Prioritas](route ke layanan-digital.index). Lengkapi data MKB, PIC, dan target waktu di sana."

File: `app/Livewire/Risiko/RisikoForm.php`
- Hapus properti `$perlu_mkb`, `$pic`, `$target_waktu_penyusunan` beserta logic `updateOrCreate` ke `layananDigital()` di method `save()` (baris terkait `if ($this->layanan_prioritas === 'Prioritas') { $this->risikoModel->layananDigital()->updateOrCreate(...) }`) — pindahkan logic ini seluruhnya ke `LayananDigitalIndex`.
- Method `fillFromRisiko()` juga hapus baris yang mengisi `$this->perlu_mkb`, `$this->pic`, `$this->target_waktu_penyusunan`.

---

## 4. Tugas B — Lengkapi Formulir 3.1 (Pemantauan): tambahkan "Proyeksi Risiko"

File: `app/Livewire/Pemantauan/PemantauanForm.php`
- Tidak perlu properti baru — data sudah tersedia via `$selectedRisiko->residual`.

File: `resources/views/livewire/pemantauan/form.blade.php`
- Di blok "Detail Risiko Mini Card" (yang saat ini menampilkan kode risiko + rencana perlakuan), tambahkan baris baru menampilkan:
  - **Besaran/Level Saat Ini**: `$selectedRisiko->besaran_risiko` (sudah ada di card, pastikan tetap tampil).
  - **Proyeksi Risiko (setelah perlakuan)**: `$selectedRisiko->residual?->besaran_risiko` + label dari `RiskMatrixCalculator::label()`. Jika belum diisi di Formulir 1.0 Tab 4 (Residual), tampilkan "Belum diisi — lengkapi di Formulir 1.0 Tab Residual".

---

## 5. Tugas C — Update Wizard Navigasi

File: `resources/views/components/risk-wizard.blade.php`

Ubah array `$steps` dari:
```
Konteks → Sasaran → Struktur → Risiko → Peta Risiko
```
menjadi:
```php
$steps = [
    ['label' => 'Konteks',         'route' => route('konteks.form', $konteks)],
    ['label' => 'Sasaran',         'route' => route('sasaran.form', $konteks)],
    ['label' => 'Struktur',        'route' => route('struktur.form', $konteks)],
    ['label' => 'Risiko',          'route' => route('risiko.index', $konteks)],
    ['label' => 'Layanan Digital', 'route' => route('layanan-digital.index', $konteks)],
    ['label' => 'Peta Risiko',     'route' => route('risiko.peta', $konteks)],
    ['label' => 'Pemantauan',      'route' => route('pemantauan.form', $konteks)],
];
```

Tambahkan pemanggilan `<x-risk-wizard :konteks="$konteks" activeStep="Layanan Digital" />` di view baru §3.3, dan pastikan `activeStep="Pemantauan"` sudah benar dipakai di `pemantauan/form.blade.php` (cek existing — saat ini `pemantauan/form.blade.php` TIDAK memanggil `<x-risk-wizard>` sama sekali, tambahkan).

---

## 6. Urutan Eksekusi

1. `git checkout -b feature/modul-2-0-3-0-terpisah`
2. Buat route + Livewire component + view untuk `layanan-digital.index` (§3.1–3.3).
3. Ubah `RisikoForm.php` + `form.blade.php` — hapus field MKB/PIC/Target dari Tab 5 (§3.4).
4. Tambahkan tampilan "Proyeksi Risiko" di halaman Pemantauan (§4).
5. Update `risk-wizard.blade.php` jadi 7 step, tambahkan wizard ke halaman Pemantauan (§5).
6. Jalankan `php artisan route:list` untuk pastikan route baru terdaftar.
7. Test manual: buat/edit risiko → tandai `layanan_prioritas = 'Prioritas'` di Formulir 1.0 → buka Formulir 2.0 → pastikan risiko muncul di daftar → isi MKB/PIC/Target → simpan → refresh, pastikan data persist.
8. Test: buka Formulir 3.1 (Pemantauan) → pilih risiko yang sudah punya data Residual di Formulir 1.0 Tab 4 → pastikan "Proyeksi Risiko" tampil dengan benar.
9. Jalankan test suite yang ada (`php artisan test`) — pastikan `RisikoForm` test lama (jika ada assert terhadap `perlu_mkb`/`pic`) disesuaikan karena field ini pindah tanggung jawab.

## 7. Checklist Definition of Done

- [ ] Route `layanan-digital.index` terdaftar dan bisa diakses dari wizard
- [ ] Halaman 2.0 menampilkan hanya risiko dengan `layanan_prioritas = 'Prioritas'` dalam konteks aktif
- [ ] Kode Risiko & Besaran Risiko di halaman 2.0 tampil otomatis (read-only), sesuai data dari Formulir 1.0
- [ ] Perlu MKB / PIC / Target Waktu bisa diisi & tersimpan ke `mr_layanan_digital` dari halaman 2.0
- [ ] Field MKB/PIC/Target sudah tidak lagi muncul di Tab 5 Formulir 1.0 (RisikoForm)
- [ ] Halaman Peta Risiko (3.0) tidak ada perubahan fungsional, hanya masuk ke wizard yang sama
- [ ] Halaman Pemantauan (3.1) menampilkan "Proyeksi Risiko" dari `mr_risiko_residual`
- [ ] Wizard 7-step tampil konsisten di semua halaman modul Manajemen Risiko (termasuk Pemantauan yang sebelumnya belum punya wizard)
- [ ] Operator yang statusnya `submitted`/`approved` tidak bisa edit halaman 2.0 (ikuti aturan `isEditableByOperator()` yang sama seperti modul lain)
- [ ] Tidak ada migration baru yang dibutuhkan — konfirmasi tidak ada perubahan skema
- [ ] Style visual (dark slate + emerald) konsisten dengan halaman lain

## 8. Catatan Penting

- **Tidak ada perubahan skema database** untuk plan ini — semua tabel (`mr_layanan_digital`, `mr_risiko_residual`, `mr_risiko_perlakuan`, `mr_pemantauan_risiko`) sudah cukup.
- Observer `MrKolomTambahanObserver` sudah otomatis membuat baris `mr_layanan_digital` saat `layanan_prioritas` diset ke `'Prioritas'` — jangan buat logic duplikat di `LayananDigitalIndex`, cukup `updateOrCreate` untuk mengisi field yang kosong.
- Jangan hapus relasi `layananDigital()` di model `MrRisiko` / `MrKolomTambahan` — hanya UI-nya yang dipindah, bukan modelnya.
