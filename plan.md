# Plan Migrasi Database — Sistem Manajemen Risiko SPBE Desa

**Stack:** Laravel 13 · Blade + Livewire 3 · Tailwind CSS · MySQL 8
**Sumber acuan:** `Manajemen Resiko.sql` (skema awal), template Excel resmi, prototipe HTML modul 0.0–3.0

---

## 1. Tujuan Dokumen

Mengubah skema SQL awal (`Manajemen_Resiko.sql`) menjadi kumpulan **migration Laravel** yang:
- Konsisten dengan konvensi Laravel (naming, `id` bigint, `timestamps`, `softDeletes`)
- Mendukung multi-desa/multi-tahun (bukan cuma 1 instansi 1 kali isi)
- Mendukung pengisian bertahap (draft) — sesuai kebutuhan pengguna awam teknologi
- Siap untuk fitur **import/export Excel** di fase berikutnya (struktur kolom = struktur sheet)
- Punya jejak audit (siapa isi, kapan, lampiran bukti)

Perubahan utama dari skema awal ada di **§3 (Ringkasan Perubahan)** — baca dulu sebelum lihat detail tabel.

---

## 2. Urutan Migration (Dependency Order)

Laravel menjalankan migration sesuai urutan nama file (timestamp), maka urutannya harus mengikuti dependency FK:

| # | Nama File Migration | Tabel |
|---|---|---|
| 1 | `..._create_roles_table` | `roles` |
| 2 | `..._create_desa_table` | `desa` *(master wilayah/tenant)* |
| 3 | `0001_01_01_000000_create_users_table` *(bawaan Laravel, dimodifikasi)* | `users` |
| 4 | `..._create_ref_kategori_risiko_table` | `ref_kategori_risiko` |
| 5 | `..._create_mr_konteks_table` | `mr_konteks` |
| 6 | `..._create_mr_sasaran_table` | `mr_sasaran` |
| 7 | `..._create_mr_struktur_pelaksana_table` | `mr_struktur_pelaksana` |
| 8 | `..._create_mr_risiko_table` | `mr_risiko` *(gabungan identifikasi + analisis, lihat §3.2)* |
| 9 | `..._create_mr_risiko_perlakuan_table` | `mr_risiko_perlakuan` |
| 10 | `..._create_mr_risiko_residual_table` | `mr_risiko_residual` |
| 11 | `..._create_mr_kolom_tambahan_table` | `mr_kolom_tambahan` *(Bagian E — data SPBE Digital)* |
| 12 | `..._create_mr_layanan_digital_table` | `mr_layanan_digital` *(auto-create via Observer)* |
| 13 | `..._create_mr_pemantauan_risiko_table` | `mr_pemantauan_risiko` |
| 14 | `..._create_mr_lampiran_table` | `mr_lampiran` *(file bukti, polymorphic)* |
| 15 | `..._create_activity_log_table` *(pakai package `spatie/laravel-activitylog`)* | `activity_log` |

> Semua tabel `mr_*` berada di bawah `mr_konteks` (langsung/tidak langsung) — jadi hapus 1 konteks = cascade hapus semua turunannya (pakai **soft delete**, bukan hard delete, supaya data desa tidak hilang permanen kalau salah klik).

---

## 3. Ringkasan Perubahan dari Skema Awal

| Isu di Skema Awal | Solusi di Skema Baru |
|---|---|
| `SERIAL` (Postgres) dicampur `BIGINT UNSIGNED`/`ENUM` (MySQL) | Semua pakai konvensi Laravel: `$table->id()` dan `$table->foreignId()->constrained()` |
| Sasaran (`sasaran_upr`, dst) jadi kolom tunggal di `mr_penetapan_konteks` | Dipisah jadi tabel `mr_sasaran` (relasi 1 konteks → banyak sasaran), sesuai Formulir 2 Excel yang memang berupa baris berulang |
| `mr_identifikasi_risiko` menyalin ulang teks sasaran | Tetap disimpan sebagai **snapshot** (`sasaran_upr_snapshot`, dst) di `mr_risiko` + kolom `mr_sasaran_id` nullable sebagai referensi — supaya histori risiko tidak berubah kalau sasaran tahun berikutnya diedit |
| `kode_risiko` UNIQUE global | Diubah jadi UNIQUE **per `mr_konteks_id`** (composite unique), karena sistem akan dipakai lintas desa |
| ENUM untuk kategori risiko, area dampak, dll | Kategori risiko (10 nilai resmi) → tabel referensi `ref_kategori_risiko` (supaya admin bisa update tanpa migration baru). Field pilihan lain yang memang tetap (`area_dampak`, `layanan_prioritas`, dll) tetap pakai `ENUM`/`string` + validasi Livewire, karena jarang berubah dan diambil langsung dari pedoman resmi |
| Semua kolom `NOT NULL` | Mayoritas kolom **nullable**, validasi "wajib lengkap" dipindah ke level aplikasi via kolom `status` (`draft` / `final`) di `mr_risiko` |
| Tidak ada `timestamps`/`soft delete` | Semua tabel pakai `timestamps()` + `softDeletes()` |
| Tidak ada tahun eksplisit di tabel risiko | `mr_konteks` dibuat **per tahun** (1 desa bisa punya banyak baris `mr_konteks`, satu per tahun penilaian) — semua turunan otomatis ikut tahun tsb lewat FK |
| Tidak ada user/role/akses desa | Tambah `users`, `roles`, `desa`, dan `users.desa_id` — 1 user terikat 1 desa (kecuali role Admin Kabupaten) |
| `data_dukung` cuma teks | Tabel `mr_lampiran` terpisah (upload file/foto, relasi polymorphic ke `mr_pemantauan_risiko` atau `mr_risiko`) |
| `besaran_risiko` disimpan manual | Tetap disimpan (bukan dihitung on-the-fly) supaya laporan histori tidak berubah kalau rumus matriks direvisi di masa depan — tapi diisi otomatis dari service `RiskMatrixCalculator`, bukan input manual |
| `prioritas_risiko` disimpan per baris | Tetap disimpan, tapi di-generate ulang lewat **Job/Observer** setiap kali ada insert/update/delete risiko dalam 1 konteks (rank ulang semua baris) |

---

## 4. Detail Skema Tabel

### 4.1 `roles`

> **Keputusan grilling:** Hanya 2 role — `admin` (akses penuh) dan `operator` (input data, perlu di-approve admin). Role `kepala_desa` dihapus.

```
id                  bigint PK
name                string        // 'admin', 'operator'
label               string        // nama tampilan: 'Administrator', 'Operator Desa'
timestamps
```

### 4.2 `desa`
```
id                  bigint PK
kode_desa           string unique      // untuk prefix kode risiko, mis. "SKM"
nama_desa           string
kecamatan           string nullable
kabupaten           string nullable
provinsi            string nullable
timestamps
softDeletes
```

### 4.3 `users` (modifikasi bawaan Laravel)

> **Keputusan grilling:** Satu operator per desa untuk sekarang (via `desa_id`), tapi kolom dirancang agar mudah dimigrasi ke tabel pivot `user_desa` di masa depan jika perlu banyak operator per desa.

```
id                  bigint PK
name                string
email               string unique
password            string
role_id             foreignId -> roles.id
desa_id             foreignId nullable -> desa.id   // null untuk admin; siap dikembangkan ke pivot table
email_verified_at, remember_token, timestamps
softDeletes
```

### 4.4 `ref_kategori_risiko`  *(lookup, seed 10 data resmi)*
```
id                  bigint PK
nama_kategori       string unique
urutan              smallint
timestamps
```

### 4.5 `mr_konteks`  *(Formulir 1 & 4 — dulu `mr_penetapan_konteks`)*

> **Keputusan grilling:** Status berubah menjadi 4 nilai (approval flow). Data tahun lalu read-only bagi operator, hanya admin yang bisa ubah ke `archived`.

```
id                  bigint PK
desa_id             foreignId -> desa.id
nama_instansi       string
nama_upr            string
tugas_upr           text nullable
fungsi_upr          text nullable
tahun_penilaian     year               // integer 4 digit, bukan varchar
selera_risiko       unsignedTinyInteger default 16   // 1-25
status              enum('draft','submitted','approved','rejected','archived') default 'draft'
created_by          foreignId nullable -> users.id
timestamps
softDeletes

unique(['desa_id','tahun_penilaian'])   // 1 desa hanya 1 konteks per tahun
```

**Aturan akses berdasarkan status:**
- `draft` → Operator bisa edit bebas
- `submitted` → Operator tidak bisa edit, menunggu review Admin
- `approved` → Read-only semua pihak
- `rejected` → Operator bisa edit kembali (lihat catatan penolakan di `mr_risiko`)
- `archived` → Hanya Admin yang bisa set; data tahun sebelumnya yang sudah selesai

### 4.6 `mr_sasaran`  *(Formulir 2 — baru, dulu kolom tunggal)*
```
id                       bigint PK
mr_konteks_id            foreignId -> mr_konteks.id
sasaran_upr              text
indikator_kinerja        string nullable
target_kinerja           string nullable
sasaran_pembangunan_nasional  text nullable
urutan                   smallint default 0
timestamps
softDeletes
```

### 4.7 `mr_struktur_pelaksana`  *(Formulir 3 — baru, dulu kolom tunggal)*
```
id                  bigint PK
mr_konteks_id       foreignId -> mr_konteks.id unique   // 1:1 dgn konteks
pemilik_risiko      string nullable
koordinator_risiko  string nullable
pengelola_risiko    text nullable
timestamps
```
> Dipisah dari `mr_konteks` supaya tabel utama tidak terlalu lebar (opsional digabung kembali jika tim ingin lebih simpel).

### 4.8 `mr_risiko`  *(gabungan `mr_identifikasi_risiko` + `mr_analisis_dan_evaluasi_risiko`)*

> **Keputusan desain:** identifikasi & analisis digabung 1 tabel karena 1:1 dan selalu diisi bersamaan di 1 form (lihat prototipe). Ini mengurangi jumlah JOIN untuk query paling sering dipakai (tabel daftar risiko).
>
> **Keputusan grilling:** Reject granular per baris risiko. Tambah kolom `status` dengan 4 nilai + `catatan_penolakan` per baris.

```
id                     bigint PK
mr_konteks_id          foreignId -> mr_konteks.id
mr_sasaran_id          foreignId nullable -> mr_sasaran.id   // referensi, boleh null jika dihapus
sasaran_pembangunan_nasional_snapshot  text nullable
sasaran_upr_snapshot   text nullable
indikator_kinerja_snapshot  string nullable
kode_risiko            string
peristiwa_risiko       text
ref_kategori_risiko_id foreignId nullable -> ref_kategori_risiko.id
penyebab               text nullable
dampak                 text nullable
area_dampak            enum('Penurunan Reputasi','Keuangan','Gangguan Terhadap Layanan Organisasi','Penurunan Kinerja') nullable
level_kemungkinan      unsignedTinyInteger nullable   // 1-5
level_dampak           unsignedTinyInteger nullable   // 1-5
besaran_risiko         unsignedTinyInteger nullable   // hasil RiskMatrixCalculator, 1-25
prioritas_risiko       unsignedSmallInteger nullable  // hasil ranking otomatis
status                 enum('draft','submitted','approved','rejected') default 'draft'
catatan_penolakan      text nullable                  // diisi admin saat reject baris ini
created_by             foreignId nullable -> users.id
timestamps
softDeletes

unique(['mr_konteks_id','kode_risiko'])
index(['mr_konteks_id','besaran_risiko'])   // buat sort peta risiko & prioritas cepat
```

### 4.9 `mr_risiko_perlakuan`  *(Bagian C, 1:1 dengan `mr_risiko`)*
```
id                          bigint PK
mr_risiko_id                foreignId -> mr_risiko.id unique
keputusan_perlakuan         enum('Menerima risiko','Mengurangi risiko','Membagi risiko','Menghindari risiko') nullable
deskripsi_detail_perlakuan  text nullable
waktu_rencana_perlakuan     string nullable
penanggung_jawab            string nullable
timestamps
```

### 4.10 `mr_risiko_residual`  *(Bagian D, 1:1 dengan `mr_risiko`)*
```
id                  bigint PK
mr_risiko_id        foreignId -> mr_risiko.id unique
level_kemungkinan   unsignedTinyInteger nullable
level_dampak        unsignedTinyInteger nullable
besaran_risiko      unsignedTinyInteger nullable
keterangan_residual text nullable
timestamps
```

### 4.11 `mr_kolom_tambahan`  *(Bagian E, 1:1 dengan `mr_risiko` — dipertahankan sesuai skema awal)*
```
id                          bigint PK
mr_risiko_id                foreignId -> mr_risiko.id unique
layanan_pendukung           string nullable
layanan_prioritas           enum('Prioritas','Tematik','Instansional') nullable
pemilik_layanan             enum('Pusat','Instansi lain','Milik sendiri') nullable
strategis_atau_operasional  enum('Strategis','Operasional') nullable
lintas_sektor                boolean default false
ippd_terkait                 string nullable
membutuhkan_perubahan        boolean default false
timestamps
```

### 4.12 `mr_layanan_digital`  *(Modul 2.0 — otomatis + editable)*
```
id                          bigint PK
mr_risiko_id                foreignId -> mr_risiko.id
perlu_mkb                   boolean nullable            // null = belum ditentukan, beda dari false
pic                          string nullable
target_waktu_penyusunan      string nullable
timestamps
```
> Baris di tabel ini **dibuat otomatis** (via Observer) ketika `mr_kolom_tambahan.layanan_prioritas = 'Prioritas'` diset pada risiko terkait — user tinggal lengkapi `perlu_mkb`, `pic`, `target_waktu_penyusunan`.

### 4.13 `mr_pemantauan_risiko`  *(Modul 3.1)*
```
id                  bigint PK
mr_risiko_id        foreignId -> mr_risiko.id
periode              enum('semester_1','semester_2')
tahun                year
hasil_pelaksanaan    text nullable
data_dukung_catatan  text nullable   // catatan teks, file asli ada di mr_lampiran
created_by           foreignId nullable -> users.id
timestamps
softDeletes

unique(['mr_risiko_id','periode','tahun'])
```

### 4.14 `mr_lampiran`  *(baru — bukti dukung / file upload)*
```
id                  bigint PK
lampirable_id        bigint            // polymorphic
lampirable_type      string            // App\Models\MrPemantauanRisiko | App\Models\MrRisiko
nama_file            string
path_file            string
mime_type            string nullable
ukuran_kb            unsignedInteger nullable
uploaded_by           foreignId nullable -> users.id
timestamps
softDeletes

index(['lampirable_id','lampirable_type'])
```

---

## 5. Model & Relasi Eloquent (ringkas)

```
Desa            hasMany  MrKonteks, hasMany User
User             belongsTo Desa, belongsTo Role
MrKonteks         belongsTo Desa
                  hasMany  MrSasaran
                  hasOne   MrStrukturPelaksana
                  hasMany  MrRisiko
MrSasaran         belongsTo MrKonteks
                  hasMany  MrRisiko (nullable ref)
MrRisiko          belongsTo MrKonteks, belongsTo MrSasaran, belongsTo RefKategoriRisiko
                  hasOne   MrRisikoPerlakuan
                  hasOne   MrRisikoResidual
                  hasOne   MrKolomTambahan
                  hasOne   MrLayananDigital
                  hasMany  MrPemantauanRisiko
                  morphMany MrLampiran (lampirable)
MrPemantauanRisiko belongsTo MrRisiko
                    morphMany MrLampiran (lampirable)
```

**Service class penting (bukan tabel, tapi wajib direncanakan bareng migration):**
- `App\Services\RiskMatrixCalculator` — implementasi Tabel 5 resmi (lookup array `[kemungkinan][dampak] => besaran`), dipanggil dari Observer `MrRisiko` setiap kali `level_kemungkinan`/`level_dampak` berubah
- `App\Observers\MrRisikoObserver` — auto-hitung `besaran_risiko`, auto-hitung ulang `prioritas_risiko` seluruh baris dalam 1 `mr_konteks_id`, auto-create baris `mr_layanan_digital` jika `layanan_prioritas = 'Prioritas'`

---

## 6. Seeder yang Perlu Disiapkan

| Seeder | Isi |
|---|---|
| `RoleSeeder` | 2 role: `admin` (Administrator), `operator` (Operator Desa) |
| `RefKategoriRisikoSeeder` | 10 kategori risiko resmi (dari sheet "Keterangan Tambahan") |
| `DesaSeeder` *(dev only)* | 2-3 desa dummy untuk testing |
| `UserSeeder` *(dev only)* | 1 admin + 1 operator per desa dummy |
| `DatabaseSeeder` (demo) | 1 konteks + beberapa risiko contoh, untuk staging/demo ke stakeholder |

---

## 7. Indexing & Performa

- `mr_risiko(mr_konteks_id, besaran_risiko)` — untuk peta risiko & sort prioritas
- `mr_risiko(mr_konteks_id, kode_risiko)` unique — validasi & pencarian cepat
- `mr_konteks(desa_id, tahun_penilaian)` unique — cegah duplikat entri tahun yang sama
- `mr_lampiran(lampirable_type, lampirable_id)` — standar polymorphic index

---

## 8. Rencana Migration File (urutan commit)

```
database/migrations/
├── 2025_01_01_000001_create_roles_table.php
├── 2025_01_01_000002_create_desa_table.php
├── 2025_01_01_000003_add_role_and_desa_to_users_table.php   // modifikasi tabel users bawaan
├── 2025_01_01_000004_create_ref_kategori_risiko_table.php
├── 2025_01_01_000005_create_mr_konteks_table.php
├── 2025_01_01_000006_create_mr_sasaran_table.php
├── 2025_01_01_000007_create_mr_struktur_pelaksana_table.php
├── 2025_01_01_000008_create_mr_risiko_table.php
├── 2025_01_01_000009_create_mr_risiko_perlakuan_table.php
├── 2025_01_01_000010_create_mr_risiko_residual_table.php
├── 2025_01_01_000011_create_mr_kolom_tambahan_table.php
├── 2025_01_01_000012_create_mr_layanan_digital_table.php
├── 2025_01_01_000013_create_mr_pemantauan_risiko_table.php
└── 2025_01_01_000014_create_mr_lampiran_table.php
```

> **Catatan:** `mr_kolom_tambahan` (#11) harus sebelum `mr_layanan_digital` (#12) karena Observer `mr_layanan_digital` bergantung pada `layanan_prioritas` di `mr_kolom_tambahan`.

**Catatan implementasi:**
- Gunakan `php artisan make:migration` per file di atas, isi dengan `Schema::create` sesuai §4
- Semua FK: `->constrained()->cascadeOnDelete()` kecuali `created_by`/`uploaded_by` pakai `->nullOnDelete()`
- Jalankan `php artisan migrate:fresh --seed` di tiap environment dev untuk validasi urutan dependency

---

## 9. Checklist Sebelum Mulai Coding

- [x] Tim konfirmasi: **multi-desa dalam 1 database** — via kolom `desa_id` (multi-tenant sederhana)
- [x] Struktur role: **2 role saja** — `admin` (akses penuh) dan `operator` (input data, perlu approval)
- [x] Approval flow: `draft` → `submitted` → `approved`/`rejected`; reject granular per `mr_risiko` dengan kolom `catatan_penolakan`
- [x] Kebijakan retensi: data tahun lalu **read-only** untuk operator; hanya admin yang bisa set status `archived`
- [ ] Siapkan 1–2 file Excel isian **nyata** (bukan contoh) untuk validasi mapping kolom import di Fase 3

---

## 10. Langkah Berikutnya

Setelah plan ini disetujui tim, saya bisa lanjutkan ke:
1. **File migration Laravel lengkap** (`.php`, siap `php artisan migrate`) sesuai §4 & §8
2. **Model Eloquent + relasi** sesuai §5
3. **Observer + `RiskMatrixCalculator` service** (logika matriks besaran risiko resmi)
4. **Seeder** sesuai §6

Beri tahu saya mau mulai dari yang mana, atau kalau mau langsung semua sekaligus.
