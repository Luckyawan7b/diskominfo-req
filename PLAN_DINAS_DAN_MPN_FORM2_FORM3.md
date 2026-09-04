# Rencana Perubahan #2 — Rename `desa`→`dinas` & Digitalisasi Form 2 + Form 3 (MPN)

**Untuk:** Coding agent, lanjutan dari `PLAN_MPN_FORM1_PERENCANAAN.md`
**Status:** Perencanaan — belum ada kode/migration
**Sumber acuan:** sheet `Form2 Database Pengumpulan&Peng` dan `Form 3. Penggunaan & Alih Penge`

> Dokumen ini **mengubah keputusan §4.2 dan §4.3** di `PLAN_MPN_FORM1_PERENCANAAN.md` — lihat §3 di bawah.

---

## 1. Perubahan Tabel `desa` → `dinas`

### 1.1 Alasan
Aplikasi ini diperuntukkan untuk **Dinas**, bukan Desa. Struktur "1 akun = 1 entitas" tetap dipertahankan, hanya konsepnya berubah dari desa ke dinas.

### 1.2 Struktur tabel baru

| Sebelum (`desa`) | Sesudah (`dinas`) |
|---|---|
| `kode_desa` (unique) | `alias` (unique) — dipakai sebagai kode/prefix ID Pengetahuan (Form 2), dan tetap dipakai untuk `kode_risiko` (Modul Risiko) |
| `nama_desa` | `nama_dinas` |
| `kecamatan`, `kabupaten`, `provinsi` | **Dihapus** — tidak relevan untuk konteks dinas. *(Perlu konfirmasi: jika data alamat/wilayah dinas tetap ingin disimpan, beri tahu kolom apa yang perlu menggantikannya — misal `alamat` bebas satu baris)* |

Constraint yang **tetap dipertahankan** (sudah ada, tinggal rename FK):
- `users.dinas_id` (dulu `desa_id`) tetap **unique** → 1 dinas = 1 akun operator (aturan ini sudah diimplementasikan di migration `2026_08_27_072355_add_unique_desa_id_to_users_table.php`, tidak perlu logika baru).

### 1.3 Dampak ke seluruh sistem (rename menyeluruh, bukan tabel baru terpisah)

Karena `desa` sudah dipakai luas di Modul Manajemen Risiko yang sudah berjalan, ini **rename in-place**, bukan membuat tabel paralel:

| Area | Perubahan |
|---|---|
| Migration | 1 migration baru: rename table `desa`→`dinas`, rename kolom `nama_desa`→`nama_dinas`, `kode_desa`→`alias`, drop `kecamatan`/`kabupaten`/`provinsi`. Lalu rename semua kolom FK `desa_id`→`dinas_id` di tabel: `users`, `mr_konteks`, `layanans`, (dan `mpn_konteks` yang akan dibuat di Form 1) |
| Model | `App\Models\Desa` → `App\Models\Dinas` (rename class + `$table = 'dinas'`), relasi `desa()`/`hasDesa` di `User`, `MrKonteks`, `Layanan` → `dinas()` |
| Kode risiko | `kode_risiko` di `MrRisiko` sudah pakai `$desa->kode_desa` (lihat `RisikoForm::mount()`) → ganti jadi `$dinas->alias` |
| Seeder | `DesaSeeder` → `DinasSeeder` (dan hapus field kecamatan/kabupaten/provinsi dari data dummy) |
| UI / Blade | Semua label "Desa" → "Dinas" di: `admin/desa/index.blade.php` (rename jadi `admin/dinas/index.blade.php`), placeholder email login (`nama@desa.go.id` → `nama@dinas.go.id`), teks di `layouts/app.blade.php` ("Kelola Desa" → "Kelola Dinas"), `UserIndex` (pesan error "OPD/Desa" → "Dinas"), dsb. |
| Route & middleware | `admin.desa.index` → `admin.dinas.index`, dan seterusnya konsisten |
| Test | `ManajemenRisikoWorkflowTest`, `SasaranFormTest` — ganti semua `Desa::create(...)` jadi `Dinas::create(...)` dengan field baru |

**Rekomendasi urutan kerja:** Selesaikan rename ini **sebagai task tersendiri lebih dulu**, sebelum mulai membangun Form 2/3 MPN — supaya `mpn_konteks` dan `kode_pengetahuan` dari awal langsung memakai `dinas_id`/`alias`, tidak perlu migrasi ulang.

---

## 2. Format `kode_pengetahuan` (Form 1) — Update

Dari contoh Excel Form 2, format ID Pengetahuan yang dipakai adalah:

```
MRP-DUKCAPIL-2026-001
```

Pola: `{PREFIX}-{ALIAS_DINAS}-{TAHUN}-{URUTAN 3 digit}`

Ini sedikit berbeda dari asumsi awal saya di dokumen Form 1 (`{kode_desa}-P-{urutan}`, mengikuti pola `kode_risiko`). **Rekomendasi: samakan dengan contoh Excel**, jadi:

```php
$kode = "MPN-{$dinas->alias}-{$tahun}-" . str_pad($urutan, 3, '0', STR_PAD_LEFT);
// contoh: MPN-DUKCAPIL-2026-001
```

> Catatan: contoh Excel di sheet Form 2 memakai prefix `MRP-` pada beberapa baris (kemungkinan sisa salin-tempel dari modul lain / typo di file contoh — bandingkan dengan `L3` yang konsisten pakai `MPN-...-REV`). **Rencana ini memakai prefix `MPN-` untuk seluruh ID Pengetahuan** karena itu yang konsisten dengan nama modul (Manajemen Pengetahuan) dan dengan `MPN-DUKCAPIL-2026-001-REV` yang muncul di Form 2 & Form 3.

Field `kode_pengetahuan` di `mpn_pengetahuan` (dari dokumen Form 1) tetap dibuat otomatis & read-only, hanya formatnya yang diperbarui.

---

## 3. Update Keputusan dari Dokumen Form 1 (§4.2, §4.3)

Dengan info baru dari Anda:

- **§4.2 (kode instansi/dinas)** — **Terjawab.** "Instansi/dinas" = tabel `dinas` baru ini. `kode_pengetahuan` pakai `dinas.alias`.
- **§4.1 (sumber "Nama Layanan")** — masih menunggu jawaban Anda (Opsi A vs B).
- **§4.3 (alur approval)** — masih menunggu jawaban Anda.
- **§4.4 (Indikator Capaian As-Is/To-Be)** — masih menunggu jawaban Anda.

### 3.1 Temuan penting: konfirmasi arah logika "Sudah Terdokumentasi?" di Form 1

Setelah membaca Form 2 & Form 3, saya menemukan **bukti kuat** yang menjawab pertanyaan saya sebelumnya (§7 poin 1 di dokumen Form 1):

- Form 2 kolom kuning (Metode Pengolahan, Deskripsi Pengolahan, **ID/Nama Pengetahuan Baru**) — sesuai catatan di Excel: *"Berupa isian opsional, hanya diisi apabila pengetahuan **belum** terdokumentasi"*. Kolom-kolom ini **persis** sama temanya dengan kolom yang menurut instruksi Anda muncul saat Form 1 dijawab **"Ya"** (Tipe Dokumentasi, Penanggung Jawab, Target Waktu) — sama-sama soal *proses menyusun/mengolah dokumentasi yang belum ada*.
- Form 3 (Penggunaan & Alih Pengetahuan) hanya relevan untuk pengetahuan yang **sudah benar-benar terdokumentasi** (sudah bisa dipakai/dibagikan) — sesuai instruksi baru Anda: *"Form 3 hanya diisi ketika pengetahuan sudah terdokumentasi"*.

Ini mengonfirmasi bahwa nilai **"Ya"** pada field "Sudah Terdokumentasi?" di Form 1 (yang men-trigger isian rencana dokumentasi) secara data sebenarnya merepresentasikan kondisi **"belum ada dokumentasi, perlu direncanakan"** — bukan "sudah ada dokumentasi". Supaya operator desa tidak salah paham, saya usulkan:

- **Rename label field** di Form 1 dari *"Sudah Terdokumentasi?"* menjadi **"Perlu Direncanakan Dokumentasinya?"** (Ya/Tidak) — perilaku Ya/Tidak-nya **tetap sama persis** seperti yang sudah Anda tentukan (Ya → isi Tipe/PIC/Target; Tidak → isi Pemilik Pengetahuan saja), hanya labelnya diperjelas.
- Field ini akan dipakai sebagai **status awal dokumentasi** (`status_dokumentasi`: `belum` jika Ya/perlu rencana, `sudah` jika Tidak) yang menentukan **gating Form 2 & Form 3** (lihat §4.2 dan §5.1).

**Mohon konfirmasi apakah rename label ini disetujui**, atau Anda ingin label persis "Sudah Terdokumentasi?" tetap dipakai walau berlawanan arah dengan isinya.

---

## 4. Rencana Digitalisasi Form 2 — Database Pengumpulan & Pengelolaan Pengetahuan

### 4.1 Relasi ke Form 1
Form 2 **tidak berdiri sendiri** — setiap baris Form 2 merujuk 1 Pengetahuan yang sudah didaftarkan di Form 1 (kolom Nama Layanan, ID Pengetahuan, Nama Pengetahuan di Form 2 sama persis dengan Form 1, ditampilkan read-only, bukan diketik ulang). Jadi relasinya **1:1** — 1 `mpn_pengetahuan` punya paling banyak 1 baris data pengumpulan.

Tabel baru: **`mpn_pengumpulan`**

| Kolom | Tipe | Wajib? | Catatan |
|---|---|---|---|
| id | bigint | | |
| mpn_pengetahuan_id | FK → mpn_pengetahuan, unique, cascade | | 1:1, pola sama seperti `mr_struktur_pelaksana` |
| tanggal_pengumpulan | date | **Wajib (abu-abu)** | |
| unit_pengumpulan | string | **Wajib** | |
| lokasi_penyimpanan | string nullable | **Wajib** | Contoh data merujuk nama database modul lain ("Database Manajemen Relasi Pengguna", dst) — **usul: select dari daftar 5 modul suite** (Manajemen Risiko, Manajemen Pengetahuan, Manajemen Perubahan, Manajemen Keberlangsungan, Manajemen Relasi) + opsi "Lainnya". *(perlu konfirmasi, lihat §4.3)* |
| tanggal_terakhir_update | date | **Wajib** | |
| rating_pengetahuan | tinyint (1-5) | **Wajib** | Skala sama seperti "Tabel 1a. Sistem Rating Pengetahuan" di Form 1 |
| keterangan_lokasi_lainnya | string nullable | Kondisional (kuning) | Hanya tampil jika `status_dokumentasi` pengetahuan induk = `belum` |
| status_publikasi_simpan | enum(dipublikasikan, belum_dipublikasikan) nullable | Kondisional (kuning) | idem |
| ref_metode_pengolahan_id | FK nullable → ref_metode_pengolahan | Kondisional (kuning) | idem |
| deskripsi_pengolahan | text nullable | Kondisional (kuning) | idem |
| kode_pengetahuan_baru | string nullable, **read-only, auto-generate** | Kondisional (kuning) | = `{kode_pengetahuan induk}-REV` (lihat §4.2), sesuai instruksi Anda |
| nama_pengetahuan_baru | text nullable | Kondisional (kuning) | idem |
| created_by | FK → users | | |
| timestamps | | | |

Tabel referensi baru **`ref_metode_pengolahan`** (seed dari "Tabel 2a. Deskripsi Metode Pengolahan Pengetahuan"):

| nama_metode | deskripsi_mekanisme | output_contoh |
|---|---|---|
| Penyimpulan Otomatis (Automated Summarization) | Pemanfaatan AI untuk memadatkan dokumen panjang (regulasi, laporan, SOP) tanpa menghilangkan esensi informasi. | Ringkasan eksekutif, poin-poin penting (bullet points). |
| Sintesis Pengetahuan Mutakhir (Knowledge Synthesis) | Pemanfaatan AI untuk memadatkan menggabungkan informasi dari beberapa dokumen terpisah untuk menjawab satu isu spesifik. | Jawaban komprehensif berbasis arsitektur RAG (Retrieval-Augmented Generation). |
| Anotasi Gambar & Diagram Fisik | Pemanfaatan AI untuk menerjemahkan aset visual (diagram alir, foto fasilitas) menjadi deskripsi tekstual. | Dokumentasi proses bisnis (BPMN) atau manual teknis yang dapat dicari. |
| Narasi Data (Data Storytelling) | Pemanfaatan AI untuk mengubah angka dan tren dari dasbor analitik menjadi laporan berbasis narasi bahasa alami. | Laporan perkembangan bulanan otomatis, analisis tren performa. |
| Penyusunan Materi Pembelajaran | Mengolah dokumen teknis internal menjadi materi edukasi yang siap pakai. | Modul pelatihan karyawan, kurikulum onboarding, soal kuis evaluasi. |
| Digitalisasi Tacit Knowledge | Mengolah hasil wawancara informal dengan pakar senior menjadi aset pengetahuan formal. | Dokumen FAQ (Tanya-Jawab), panduan troubleshooting mandiri. |
| Dedupikasi & Rekonsiliasi Konseptual | Mendeteksi dan menyatukan dokumen yang memiliki kemiripan makna meskipun redaksi katanya berbeda. | Repositori bersih dari dokumen ganda/redundan. |

UI: dropdown Metode Pengolahan menampilkan tooltip/keterangan singkat (deskripsi + contoh) supaya operator tidak bingung memilih.

### 4.2 Aturan "ID Pengetahuan Baru" (`-REV`)

Sesuai instruksi Anda:

```php
$kodePengetahuanBaru = $pengetahuanInduk->kode_pengetahuan . '-REV';
// MPN-DUKCAPIL-2026-001  →  MPN-DUKCAPIL-2026-001-REV
```

Field ini **read-only**, di-generate otomatis oleh sistem saat Form 2 baris tersebut disimpan (bukan diketik manual oleh operator) — konsisten dengan cara `kode_risiko` dan `kode_pengetahuan` sudah dibuat read-only/auto di Form 1.

### 4.3 Pertanyaan tambahan untuk Form 2

| # | Pertanyaan |
|---|---|
| A | Field "Lokasi Penyimpanan selain SIMPAN..." — apakah benar berupa pilihan dari 5 modul suite (Risiko/Pengetahuan/Perubahan/Keberlangsungan/Relasi) seperti dugaan saya, atau bebas teks? |
| B | "Status Publikasi di SIMPAN PemDi" — apakah hanya 2 opsi (Dipublikasikan / Belum Dipublikasikan), atau ada status lain (mis. "Dalam Proses Review")? |

---

## 5. Rencana Digitalisasi Form 3 — Penggunaan & Alih Pengetahuan

### 5.1 Gating: hanya untuk pengetahuan yang sudah terdokumentasi

Sesuai instruksi Anda: **Form 3 hanya bisa diisi jika `status_dokumentasi` pengetahuan tersebut = `sudah`.** Kondisi ini terpenuhi jika salah satu:
- Sejak Form 1, cabangnya "Tidak" (tidak perlu rencana — sudah terdokumentasi dari awal), **atau**
- Awalnya "Ya" (perlu rencana) di Form 1, tapi kemudian **Form 2 sudah diisi lengkap** (proses pengolahan sudah dilakukan) → status berubah otomatis jadi `sudah` (menghasilkan `kode_pengetahuan_baru` yang -REV, yang jadi acuan ID di Form 3 — lihat contoh Excel: Form 3 baris 5 pakai ID `MPN-DUKCAPIL-2026-001-REV`, bukan ID original).

**Perilaku UI:** di halaman daftar Pengetahuan, tombol/link ke Form 3 untuk 1 pengetahuan **disabled** (atau disembunyikan) selama `status_dokumentasi` masih `belum` dan Form 2 belum lengkap.

### 5.2 Struktur data — 2 sub-formulir, masing-masing bisa berulang (1-ke-banyak)

Dari Excel, baik "Pemanfaatan" maupun "Alih Pengetahuan" **bisa punya beberapa baris log untuk 1 Pengetahuan yang sama** (contoh: `MPN-DUKCAPIL-2026-001-REV` dipakai 2x di tanggal berbeda). Jadi bukan 1:1, tapi **hasMany**.

**Tabel baru: `mpn_pemanfaatan`** (log Pemanfaatan/Penggunaan Pengetahuan)

| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint | |
| mpn_pengetahuan_id | FK → mpn_pengetahuan, cascade | |
| tanggal_pemanfaatan | date | Sesuai instruksi Anda: **diisi sebagai tanggal saat aktivitas/pekerjaan tsb dilakukan** (bukan tanggal input form) |
| tipe_pengguna | enum(publik, internal) | dari "Apakah Anda Pengguna Publik atau Internal?" |
| unit_pengguna | string | |
| tujuan_pemanfaatan | text | |
| rating_pengetahuan | tinyint (1-5) | Skala sama dengan Tabel 1a/3a |
| created_by | FK → users | |
| timestamps | | |

**Tabel baru: `mpn_alih_pengetahuan`** (log Transfer/Alih Pengetahuan)

| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint | |
| mpn_pengetahuan_id | FK → mpn_pengetahuan, cascade | |
| tanggal_mulai | date | Excel contoh pakai rentang tanggal ("01-02 Oktober 2026") → dipecah jadi 2 kolom tanggal supaya bisa divalidasi & diurutkan |
| tanggal_selesai | date nullable | kosong jika kegiatan 1 hari |
| metode_pelatihan | boolean default false | checkbox multi-pilih |
| metode_workshop | boolean default false | |
| metode_sosialisasi | boolean default false | |
| metode_mentoring | boolean default false | |
| metode_sharing | boolean default false | |
| metode_lainnya | boolean default false | jika dicentang → wajib isi `keterangan_lainnya` |
| keterangan_lainnya | string nullable | kondisional, hanya jika `metode_lainnya` = true |
| penerima_pengetahuan | text | |
| hasil_evaluasi | text | |
| created_by | FK → users | |
| timestamps | | |

### 5.3 UI

Halaman Form 3 per Pengetahuan (mirip pola `pemantauan/form.blade.php` yang sudah ada di Modul Risiko — list pilih Pengetahuan di kiri, form tambah + riwayat log di kanan):
- Tab/section "Pemanfaatan": form tambah entri baru + tabel riwayat pemanfaatan.
- Tab/section "Alih Pengetahuan": form tambah entri baru + tabel riwayat transfer.

Ini konsisten dengan pola `MrPemantauanRisiko` (hasMany log per risiko) yang sudah battle-tested di modul Risiko.

---

## 6. Ringkasan Tabel Baru (Fase 2 lengkap: Form 1 + Form 2 + Form 3)

```
dinas (rename dari desa)
 └─ mpn_konteks (per dinas per tahun)
     └─ mpn_layanan
         └─ mpn_pengetahuan  (kode_pengetahuan = MPN-{alias}-{tahun}-{urutan})
             ├─ mpn_pengumpulan       (1:1 — Form 2)
             ├─ mpn_pemanfaatan       (1:banyak — Form 3, bagian Pemanfaatan)
             └─ mpn_alih_pengetahuan  (1:banyak — Form 3, bagian Alih Pengetahuan)

ref_aspek_pemdi / ref_indikator_pemdi   (Form 1)
ref_metode_pengolahan                    (Form 2)
```

---

## 7. Ringkasan Pertanyaan Terbuka (gabungan Form 1 + Form 2 + Form 3)

| # | Pertanyaan | Dari dokumen |
|---|---|---|
| 1 | Sumber data "Nama Layanan" — reuse dari Modul Risiko atau daftar independen MPN? | Form 1 §4.1 |
| 2 | Form 1 MPN perlu alur approval Admin seperti Modul Risiko, atau draft/final sederhana? | Form 1 §4.3 |
| 3 | Bagian "Indikator Capaian Manajemen Pengetahuan" (As-Is/To-Be) termasuk cakupan sekarang? | Form 1 §4.4 |
| 4 | Setuju rename label Form 1 dari "Sudah Terdokumentasi?" jadi "Perlu Direncanakan Dokumentasinya?" (perilaku tetap sama)? | §3.1 dokumen ini |
| 5 | Kolom `dinas` — field alamat (kecamatan/kabupaten/provinsi) dihapus total, atau diganti 1 kolom alamat bebas? | §1.2 dokumen ini |
| 6 | "Lokasi Penyimpanan" di Form 2 — pilihan dari 5 modul suite, atau teks bebas? | §4.3 dokumen ini |
| 7 | "Status Publikasi di SIMPAN PemDi" — hanya 2 opsi atau lebih? | §4.3 dokumen ini |

**Rekomendasi urutan pengerjaan:** (1) rename desa→dinas dulu, (2) baru mulai coding Form 1 MPN dengan `dinas.alias` sudah tersedia, (3) Form 2, (4) Form 3 — supaya `kode_pengetahuan`/`-REV` konsisten sejak awal tanpa perlu migrasi ulang data.
