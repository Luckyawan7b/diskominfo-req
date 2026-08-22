# Konteks Perubahan: Refactor Struktur Data Sasaran UPR

**Untuk:** Coding agent yang melanjutkan pekerjaan di repo `diskominfo-req`
**Area terdampak:** Modul 0.0 Penetapan Konteks — bagian Formulir 2 (Sasaran UPR)
**Status:** Kode sudah dibuat dan siap disalin dari folder hasil ekstrak, belum di-commit ke repo

---

## 1. Masalah yang Diperbaiki

Implementasi awal `mr_sasaran` menyimpan data sebagai **tabel datar (flat)**: 1 baris = 1 kombinasi lengkap `sasaran_upr` + `indikator_kinerja` + `target_kinerja` + `sasaran_pembangunan_nasional`.

Masalahnya, data asli di template Excel resmi **bertingkat 3 level**, bukan datar:

```
Sasaran Pembangunan Nasional (1 tujuan besar, dari RPJMD/Renstra)
 └─ Sasaran UPR (bisa lebih dari satu per tujuan nasional)
     └─ Indikator + Target Kinerja (bisa lebih dari satu per Sasaran UPR)
```

Akibat dari struktur datar yang lama:
- Kalimat panjang "Sasaran Pembangunan Nasional" harus diketik ulang di tiap baris → rawan typo/inkonsistensi
- UI tabel spreadsheet (kolom sempit, textarea kecil bisa di-scroll) sulit dipahami operator desa yang awam teknologi
- Tidak ada cara menampilkan bahwa beberapa Sasaran UPR sebenarnya menaungi 1 tujuan nasional yang sama

Target pengguna adalah **perangkat desa awam teknologi**, jadi UX form harus berupa kartu/wizard yang jelas, bukan grid data-entry.

## 2. Solusi yang Diimplementasikan

Tabel `mr_sasaran` dipecah menjadi 3 tabel baru:

| Tabel Lama | Tabel Baru | Keterangan |
|---|---|---|
| `mr_sasaran` (flat) | `ref_sasaran_nasional` | Tabel referensi, 1 baris = 1 kalimat tujuan nasional unik. Tumbuh otomatis (`firstOrCreate`) saat operator mengetik kalimat baru — tidak perlu diisi admin dulu |
| | `mr_sasaran_upr` | 1 baris = 1 Sasaran UPR milik 1 `mr_konteks`, menunjuk ke `ref_sasaran_nasional_id` (nullable) |
| | `mr_indikator_kinerja` | 1 baris = 1 pasang Indikator+Target, menunjuk ke `mr_sasaran_upr_id`. Bisa lebih dari 1 baris per Sasaran UPR |

UI diubah dari tabel ke **card per Sasaran UPR**, tiap card berisi dropdown/textarea Sasaran Nasional + textarea Sasaran UPR + daftar Indikator/Target yang bisa ditambah lebih dari satu, dengan help text & placeholder contoh nyata di tiap field (bukan cuma label kosong).

Tema warna & pola kode (dark slate + emerald accent, `wire:model`, `wire:confirm`) **sengaja dipertahankan sama** dengan kode yang sudah ada di repo — tidak ada rebranding warna di perubahan ini.

## 3. Daftar File & Lokasi Tujuan

Salin file dari folder hasil ekstrak ke path berikut di dalam repo Laravel (path tujuan sudah mengikuti struktur folder Laravel standar):

| File di Folder Ekstrak | Path Tujuan di Repo | Aksi |
|---|---|---|
| `2025_01_01_000006_create_ref_sasaran_nasional_table.php` | `database/migrations/2025_01_01_000006_create_ref_sasaran_nasional_table.php` | Tambah baru |
| `2025_01_01_000007_create_mr_sasaran_upr_table.php` | `database/migrations/2025_01_01_000007_create_mr_sasaran_upr_table.php` | Tambah baru |
| `2025_01_01_000008_create_mr_indikator_kinerja_table.php` | `database/migrations/2025_01_01_000008_create_mr_indikator_kinerja_table.php` | Tambah baru |
| `2025_01_01_000009_migrate_data_and_drop_mr_sasaran_table.php` | `database/migrations/2025_01_01_000009_migrate_data_and_drop_mr_sasaran_table.php` | Tambah baru — **memindahkan data lama lalu drop tabel `mr_sasaran`** |
| `RefSasaranNasional.php` | `app/Models/RefSasaranNasional.php` | Tambah baru |
| `MrSasaranUpr.php` | `app/Models/MrSasaranUpr.php` | Tambah baru |
| `MrIndikatorKinerja.php` | `app/Models/MrIndikatorKinerja.php` | Tambah baru |
| `SasaranForm.php` | `app/Livewire/Sasaran/SasaranForm.php` | **Timpa (replace)** file lama |
| `form.blade.php` | `resources/views/livewire/sasaran/form.blade.php` | **Timpa (replace)** file lama |

> Jika ada file `Migration for mr_sasaran` versi lama (`2025_01_01_000006_create_mr_sasaran_table.php`) yang **sudah pernah dijalankan di database dev/staging**, **jangan dihapus filenya** — biarkan tetap ada di folder migrations sebagai riwayat, karena migration `000009` di atas butuh tabel `mr_sasaran` masih ada saat dijalankan untuk memindahkan datanya.

## 4. Tugas Tambahan yang Belum Otomatis (Wajib Dikerjakan Manual)

### 4.1 Update model `MrKonteks`
Tambahkan relasi baru di `app/Models/MrKonteks.php`:
```php
public function sasaranUpr()
{
    return $this->hasMany(MrSasaranUpr::class, 'mr_konteks_id')->orderBy('urutan');
}
```
Relasi lama `sasaran()` (yang mengarah ke `MrSasaran`) boleh dihapus **setelah** migration `000009` berhasil dijalankan dan diverifikasi datanya pindah dengan benar.

### 4.2 Cek referensi lain ke model/tabel lama
Cari di seluruh codebase apakah ada bagian lain yang memanggil:
- Model `App\Models\MrSasaran`
- Relasi `$konteks->sasaran`
- Kolom `sasaran_pembangunan_nasional`, `indikator_kinerja`, `target_kinerja` langsung dari tabel `mr_sasaran`

Kemungkinan tempat: fitur export/rekap Excel, dashboard ringkasan, atau modul 1.0 Daftar Risiko (jika ada dropdown "pilih sasaran terkait" yang mengacu ke sasaran).

### 4.3 `AppServiceProvider`
Pastikan `Schema::defaultStringLength(191)` sudah di-set di `boot()` — dibutuhkan supaya migration `ref_sasaran_nasional` (kolom `string(500)->unique()`) tidak gagal dengan error `Specified key was too long` di MySQL versi lama (< 8.0 / charset utf8mb4 dengan index prefix terbatas).

## 5. Urutan Eksekusi

1. `git checkout -b refactor/sasaran-upr-bertingkat` (branch terpisah, jangan langsung ke `main`)
2. **Backup database dev/staging** (migration `000009` men-drop tabel `mr_sasaran` — tidak reversible via rollback)
3. Salin 9 file sesuai tabel §3
4. Tambahkan relasi `sasaranUpr()` di `MrKonteks` (§4.1)
5. Jalankan `php artisan migrate`
6. Verifikasi: buka halaman Sasaran UPR untuk 1 konteks yang sebelumnya sudah ada datanya → pastikan data lama muncul benar sebagai card, indikator+target tidak hilang
7. Uji alur baru: tambah Sasaran UPR baru, pilih Sasaran Nasional dari dropdown vs ketik baru, tambah lebih dari 1 Indikator per Sasaran UPR, hapus salah satu indikator, hapus seluruh card
8. Cek §4.2 (referensi lain ke tabel/model lama) sebelum merge

## 6. Checklist Definition of Done

- [ ] Migration berhasil jalan tanpa error di environment dev
- [ ] Data lama (jika ada) berhasil termigrasi, tidak ada yang hilang
- [ ] Tabel `mr_sasaran` sudah tidak ada lagi di database (drop berhasil)
- [ ] Halaman Sasaran UPR tampil sebagai card, bukan tabel grid
- [ ] Bisa tambah/hapus Sasaran UPR
- [ ] Bisa tambah/hapus lebih dari 1 Indikator+Target dalam 1 Sasaran UPR
- [ ] Dropdown Sasaran Nasional menampilkan opsi yang sudah pernah diketik sebelumnya
- [ ] Mengetik Sasaran Nasional baru otomatis tersimpan ke `ref_sasaran_nasional` dan muncul di dropdown setelah simpan
- [ ] Tidak ada error di halaman lain yang sebelumnya bergantung pada tabel/model `mr_sasaran` lama (§4.2)
- [ ] Style/tema visual (dark slate + emerald) tidak berubah dari sebelumnya

## 7. Yang Sengaja Belum Dikerjakan (Future Scope)

- Pengelompokan visual (accordion) antar-card yang berbagi `ref_sasaran_nasional_id` yang sama — untuk v1 ini tiap Sasaran UPR tetap tampil sebagai card terpisah meskipun Sasaran Nasionalnya sama
- Rebranding warna ke palet Diskominfo (`#2196F3` / `#E3F2FD`) — belum diterapkan di perubahan ini, direncanakan sebagai pekerjaan terpisah yang mencakup seluruh aplikasi, bukan hanya modul ini
- Halaman admin untuk mengelola/menggabungkan `ref_sasaran_nasional` yang duplikat/typo (mis. 2 kalimat yang maksudnya sama tapi beda kata)
