# Rencana Digitalisasi — Modul Manajemen Pengetahuan (MPN), Form 1 Perencanaan

**Untuk:** Coding agent yang akan melanjutkan pekerjaan di repo `diskominfo-req`
**Fase:** 2 — Modul Manajemen Pengetahuan (setelah Modul Manajemen Risiko selesai)
**Cakupan dokumen ini:** HANYA Form 1 Perencanaan (belum termasuk Form 2, 3, 4, Format Metadata, Knowledge Repository)
**Status:** Perencanaan — belum ada kode/migration yang dibuat
**Sumber acuan:** `24072026_Pemda_MPN-Manajemen_Pengetahuan_Contoh_Pengisian.xlsx`, sheet `Form 1 Perencanaan`

---

## 1. Apa yang Ada di Excel Asli vs Apa yang Diminta Berubah

### 1.1 Struktur asli (sheet `Form 1 Perencanaan`, baris 8–18)

Tabel utama "Critical Knowledge" + "Perencanaan Pengumpulan Pengetahuan" punya kolom (grup warna asli):

| Kolom | Nama | Warna asli | Catatan |
|---|---|---|---|
| B | No | Abu-abu | Nomor urut Layanan |
| C | Nama Layanan | Abu-abu | 1 layanan bisa punya >1 baris Pengetahuan (merged cell) |
| D | Termasuk Layanan Prioritas? | Abu-abu | Ya/Tidak, atribut per **Layanan** (bukan per pengetahuan) |
| E | Nama Pengetahuan | Abu-abu | Bisa >1 per layanan |
| F | Pengetahuan sudah terdokumentasi? | Abu-abu | Sudah/Belum |
| G | Aspek PemDi | Abu-abu | **Di contoh terisi 2 baris untuk 1 pengetahuan** (mis. baris 11 & 12 sama-sama untuk pengetahuan "Prosedur percepatan perekaman e-KTP...") |
| H | Indikator PemDi | Abu-abu | Sama, 2 baris per pengetahuan |
| I | Ditargetkan terdokumentasi tahun ini? | Kuning | Ya/Tidak — ini yang jadi trigger isi K–P |
| J | Pemilik Pengetahuan (Unit Kerja/Instansi Terkait)* | Kuning | Selalu ada nilainya di contoh, terlepas dari I |
| K–N | Tipe Dokumentasi (Teks/Gambar/Audio/Video) | Kuning | Boolean TRUE/FALSE mentah |
| O | Penanggung Jawab Dokumentasi* | Kuning | |
| P | Target Waktu Dokumentasi* | Kuning | |

### 1.2 Perubahan yang diminta (rekap permintaan Anda)

1. **Nama Layanan** → merujuk ke nama menu/fitur yang ada di aplikasi (bukan teks bebas seperti sekarang).
2. **Kolom baru "Sudah Terdokumentasi?" (Ya/Tidak)** menggantikan alur lama (F + I digabung jadi satu pertanyaan trigger):
   - **Ya** → lanjut isi: *Tipe Dokumentasi Pengetahuan*, *Penanggung Jawab Dokumentasi*, *Target Waktu Dokumentasi*.
   - **Tidak** → cukup isi: *Pemilik Pengetahuan (Unit Kerja/Instansi Terkait)*.
3. Semua nilai **TRUE/FALSE mentah diganti tampilan Ya/Tidak** (termasuk checkbox tipe dokumentasi, "Termasuk Layanan Prioritas?", dst).
4. **Section 1 (abu-abu) = wajib diisi semua**, tanpa syarat.
5. **Section 2 (kuning) = kondisional**, mengikuti isian Ya/Tidak "Sudah Terdokumentasi?".
6. **Aspek PemDi + Indikator PemDi → hanya 1 baris per Pengetahuan**, tidak boleh 2 baris seperti contoh Excel (row 11 & 12).
7. **ID/Kode Pengetahuan** mengikuti kode instansi/dinas (UPR) — pola sama seperti `kode_risiko` di Modul Manajemen Risiko yang diprefix kode desa (`SKM-R-001`), untuk MPN jadi mis. `SKM-P-001`.

> ⚠️ **Catatan penting untuk dikonfirmasi** — poin 2 di atas: penamaan "Sudah Terdokumentasi = **Ya**" justru men-trigger pengisian rencana dokumentasi (Tipe/PIC/Target), padahal secara bahasa "sudah terdokumentasi" biasanya berarti *tidak perlu* rencana lagi. Dokumen ini mengikuti **persis instruksi Anda** (Ya → isi rencana, Tidak → cukup pemilik), tapi mohon dikonfirmasi ulang apakah label kolomnya sudah tepat, atau maksudnya kebalik (Tidak = belum terdokumentasi = perlu rencana). Ini akan menentukan copy/label di UI supaya operator desa tidak salah paham.

---

## 2. Struktur Formulir yang Diusulkan

Mengikuti pola hierarki 2 level yang sudah terbukti dipakai di Modul Manajemen Risiko (`MrSasaranUpr` → `MrIndikatorKinerja`), Form 1 Perencanaan MPN dipecah jadi:

```
Konteks MPN (per Instansi/Desa, per Tahun)
 └─ Layanan (Nama Layanan, Termasuk Layanan Prioritas?)
     └─ Pengetahuan (1..n per Layanan)
          ├─ Section 1 (wajib): Nama Pengetahuan, Sudah Terdokumentasi?,
          │                     Aspek PemDi, Indikator PemDi (1 pasang saja)
          └─ Section 2 (kondisional berdasarkan Sudah Terdokumentasi?):
                Ya  → Tipe Dokumentasi (multi), Penanggung Jawab, Target Waktu
                Tidak → Pemilik Pengetahuan (Unit Kerja/Instansi Terkait)
```

UI-nya akan berbentuk **card per Layanan**, dengan **card kecil per Pengetahuan** di dalamnya — mirip tampilan `resources/views/livewire/sasaran/form.blade.php`, bukan grid spreadsheet. Ini sudah terbukti ramah untuk operator desa yang awam teknologi.

---

## 3. Rencana Skema Database

### 3.1 Tabel referensi (master data, seed sekali di awal)

**`ref_aspek_pemdi`**
| Kolom | Tipe |
|---|---|
| id | bigint |
| nama_aspek | string |
| urutan | smallint |

**`ref_indikator_pemdi`**
| Kolom | Tipe |
|---|---|
| id | bigint |
| ref_aspek_pemdi_id | FK → ref_aspek_pemdi, cascade delete |
| nama_indikator | string |
| urutan | smallint |

Seed data (dari sheet, "Tabel 1a. Aspek dan Indikator Pemerintah Digital"):

| Aspek | Indikator |
|---|---|
| Tata Kelola dan Manajemen | Tata Kelola dan Manajemen |
| Tata Kelola dan Manajemen | Manajemen Layanan Digital |
| Penyelenggara | Sumber daya manusia |
| Penyelenggara | Kolaborasi Pemerintah Digital |
| Data | Tata kelola Data |
| Data | Pemanfaatan Informasi Geospasial |
| Data | Pembangunan Statistik |
| Data | Perlindungan data pribadi |
| Keamanan Siber | Pelaksanaan Audit Keamanan Siber |
| Keamanan Siber | Keamanan Siber |
| Keamanan Siber | Kriptografi untuk Keamanan Data |
| Keamanan Siber | Penanganan Insiden Siber |
| Teknologi Digital | Aplikasi Pemerintah Digital |
| Teknologi Digital | Infrastruktur Pemerintah Digital |
| Keterpaduan Layanan Digital Pemerintah | Keterpaduan proses bisnis |
| Keterpaduan Layanan Digital Pemerintah | Integrasi aplikasi |
| Keterpaduan Layanan Digital Pemerintah | Portal Layanan Digital Pemerintah |
| Keterpaduan Layanan Digital Pemerintah | Interoperabilitas data |
| Kepuasan Pengguna Layanan Digital Pemerintah | Fasilitas dukungan pengguna |
| Kepuasan Pengguna Layanan Digital Pemerintah | Tingkat kepuasan pengguna |

UI: dropdown Aspek → filter dropdown Indikator (cascading select), analog dengan pola `RefKategoriRisiko` tapi 2 level.

### 3.2 Tabel transaksional

**`mpn_konteks`** (identitas 1 dokumen MPN per desa per tahun — pola sama seperti `mr_konteks`)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint | |
| desa_id | FK → desa, cascade | |
| tahun_penilaian | year | |
| status | enum(draft, submitted, approved, rejected, archived) | default draft, alur approval sama seperti modul Risiko |
| created_by | FK → users, nullOnDelete | |
| timestamps, softDeletes | | |
| unique(desa_id, tahun_penilaian) | | |

**`mpn_layanan`**
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint | |
| mpn_konteks_id | FK → mpn_konteks, cascade | |
| nama_layanan | string | **lihat §4.1 — sumber data perlu keputusan** |
| termasuk_layanan_prioritas | boolean | tampil sebagai select Ya/Tidak |
| urutan | smallint | |
| timestamps, softDeletes | | |

**`mpn_pengetahuan`**
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint | |
| mpn_layanan_id | FK → mpn_layanan, cascade | |
| kode_pengetahuan | string | unik per `mpn_konteks_id` (dihitung via join layanan→konteks), format `{KODE_DESA}-P-{urutan}` — lihat §4.2 |
| nama_pengetahuan | text | Section 1, wajib |
| ref_aspek_pemdi_id | FK nullable → ref_aspek_pemdi | Section 1, wajib, **1 baris saja** |
| ref_indikator_pemdi_id | FK nullable → ref_indikator_pemdi | Section 1, wajib, **1 baris saja** |
| sudah_terdokumentasi | boolean | Section 1, wajib — trigger Section 2 |
| tipe_dok_teks | boolean default false | Section 2, hanya diisi jika sudah_terdokumentasi = true (sesuai §1.2 poin 2) |
| tipe_dok_gambar | boolean default false | idem |
| tipe_dok_audio | boolean default false | idem |
| tipe_dok_video | boolean default false | idem |
| penanggung_jawab_dokumentasi | string nullable | idem |
| target_waktu_dokumentasi | string nullable | idem |
| pemilik_pengetahuan | string nullable | Section 2, hanya diisi jika sudah_terdokumentasi = false |
| status | enum(draft, submitted, approved, rejected) | opsional — ikutkan hanya jika Form 1 MPN akan melalui alur review Admin sama seperti Risiko (perlu konfirmasi, lihat §4.3) |
| catatan_penolakan | text nullable | idem, hanya relevan jika status dipakai |
| created_by | FK → users, nullOnDelete | |
| timestamps, softDeletes | | |

Catatan desain: kolom Section 2 tetap disimpan sebagai satu tabel (bukan dipisah 2 tabel) supaya query & migrasi data lebih sederhana — mirip pola `mr_risiko` yang menyimpan field kolom tambahan langsung, bukan seperti pemisahan `mr_sasaran_upr`/`mr_indikator_kinerja`. Nullability field-nya sudah cukup untuk merepresentasikan "hanya salah satu grup yang wajib" tergantung `sudah_terdokumentasi`.

---

## 4. Keputusan yang Perlu Dikonfirmasi Sebelum Coding Dimulai

### 4.1 Sumber data "Nama Layanan"
Anda menyebut "kolom menu layanan: merujuk pada nama menu atau fitur yg ada di aplikasinya". ini punya 2 kemungkinan implementasi:

- **Opsi A — Reuse dari Modul Risiko.** "Nama Layanan" di MPN mengambil dari daftar `mr_kolom_tambahan.layanan_pendukung` (atau tabel `Layanan Digital Prioritas` — `mr_layanan_digital`) yang sudah diisi operator di Modul Manajemen Risiko. Keuntungan: satu sumber data layanan digital per desa, konsisten lintas modul. Kerugian: MPN jadi bergantung pada modul Risiko sudah diisi duluan; couple antar-modul.
- **Opsi B — Daftar layanan independen di MPN.** Buat tabel referensi sendiri, mis. `mpn_ref_layanan` (per desa, bisa tumbuh sendiri via `firstOrCreate` seperti pola `RefSasaranNasional`), tanpa bergantung pada modul lain.

**Rekomendasi:** Opsi B untuk iterasi pertama (lebih sederhana, tidak ada dependency antar-modul), dengan catatan Opsi A bisa jadi enhancement di fase berikutnya kalau memang layanan yang dimaksud sama persis dengan yang dikelola di modul Risiko. **Mohon konfirmasi mana yang dimaksud** — karena kalau Opsi A, field `nama_layanan` di atas berubah jadi FK, bukan string bebas.

### 4.2 Kode instansi/dinas untuk `kode_pengetahuan`
Anda menyebut "id pengetahuan ini diambil dari manajemen apa, alias (instansinya/dinasnya)". Modul Risiko sudah punya `desa.kode_desa` (mis. `SKM`) yang dipakai sebagai prefix `kode_risiko` (`SKM-R-001`). Rencana: pakai kolom yang sama untuk MPN → `kode_pengetahuan` = `{desa.kode_desa}-P-{urutan}`. **Mohon konfirmasi** apakah instansi/dinas yang dimaksud memang sama dengan `desa` yang sudah ada (1 akun = 1 desa/instansi), atau MPN perlu konsep instansi yang lebih luas dari sekadar "desa" (mengingat contoh data pakai "Dispendukcapil Jatim" sebagai level provinsi/kabupaten, bukan desa).

### 4.3 Alur approval (status draft/submitted/approved/rejected)
Modul Risiko punya alur Admin-review per baris risiko (Review & Approval). **Mohon konfirmasi**: apakah Form 1 Perencanaan MPN juga perlu alur approval yang sama (Admin approve/reject per Pengetahuan), atau untuk fase awal ini cukup disimpan sebagai draft/final tanpa proses review dari Admin? Ini menentukan apakah kolom `status` & `catatan_penolakan` di `mpn_pengetahuan` perlu dibuat sekarang atau belakangan.

### 4.4 Bagian "Indikator Capaian Manajemen Pengetahuan" (baris 2–5 di Excel)
Bagian ini (Persentase layanan yang sudah punya pengetahuan terdokumentasi, Kesesuaian pengetahuan dengan kebutuhan pengguna — As-Is vs To-Be) tidak disebutkan dalam permintaan Anda. **Dokumen ini mengasumsikan bagian tersebut di luar cakupan Form 1 Perencanaan tahap ini** (kemungkinan dihitung otomatis nanti dari data Form 3/4, atau jadi form terpisah). Mohon dikonfirmasi apakah perlu ikut direncanakan sekarang.

---

## 5. Rencana UI (mengacu pola Sasaran UPR yang sudah ada)

1. Halaman **Daftar Layanan** (mirip `sasaran/form.blade.php`): tombol "+ Tambah Layanan", tiap layanan jadi 1 card berisi:
   - Input/select Nama Layanan (tergantung §4.1)
   - Select Ya/Tidak untuk "Termasuk Layanan Prioritas?"
   - Daftar card Pengetahuan di dalamnya, tombol "+ Tambah Pengetahuan"
2. Tiap card Pengetahuan:
   - **Bagian atas (selalu tampil, wajib — styling netral/tanpa warna abu-abu literal, cukup ditandai `*` merah sesuai konvensi form yang sudah ada di app):** Nama Pengetahuan (textarea), select Aspek PemDi, select Indikator PemDi (terfilter oleh Aspek), select Ya/Tidak "Sudah Terdokumentasi?"
   - **Bagian bawah (muncul kondisional dengan animasi `x-show`, mirip pola custom input di `konteks/form.blade.php`):**
     - Jika Ya → checkbox Teks/Gambar/Audio/Video (label "Ya/Tidak" tersirat dari status checked, tanpa literal TRUE/FALSE), input Penanggung Jawab Dokumentasi, input Target Waktu Dokumentasi
     - Jika Tidak → input Pemilik Pengetahuan (Unit Kerja/Instansi Terkait)
3. Validasi Livewire: field Section 1 selalu `required`; field Section 2 divalidasi `required_if:sudah_terdokumentasi,...` sesuai cabang yang aktif.
4. Kode Pengetahuan (`kode_pengetahuan`) ditampilkan read-only (auto-generate saat pengetahuan pertama kali disimpan), sama seperti kode risiko yang read-only di form Risiko.

---

## 6. Urutan Eksekusi (setelah semua keputusan §4 dikonfirmasi)

1. Konfirmasi 4 keputusan di §4 bersama Anda.
2. Buat migration: `ref_aspek_pemdi`, `ref_indikator_pemdi`, `mpn_konteks`, `mpn_layanan`, `mpn_pengetahuan` (+ `mpn_ref_layanan` jika Opsi B dipilih di §4.1).
3. Buat seeder `RefAspekIndikatorPemdiSeeder` dengan 7 aspek / 20 indikator dari §3.1.
4. Buat model: `MpnKonteks`, `MpnLayanan`, `MpnPengetahuan`, `RefAspekPemdi`, `RefIndikatorPemdi`.
5. Buat Livewire component `App\Livewire\Mpn\PerencanaanForm` (pola sama seperti `SasaranForm`), + Blade view card-based.
6. Tambahkan route `manajemen-pengetahuan/konteks/{konteks}/perencanaan`.
7. Uji manual mengikuti alur data contoh Excel (Layanan Perekaman e-KTP → 2 pengetahuan → cabang Ya/Tidak keduanya).
8. Setelah Form 1 stabil, lanjut rencanakan Form 2 (Database Pengumpulan & Pengelolaan Pengetahuan).

---

## 7. Ringkasan Pertanyaan yang Menunggu Jawaban Anda

| # | Pertanyaan | Dampak jika tidak dijawab |
|---|---|---|
| 1 | Apakah logika "Sudah Terdokumentasi = Ya → isi rencana dokumentasi" sudah benar sesuai maksud Anda, atau terbalik? | Salah label UI, bisa membingungkan operator desa |
| 2 | "Nama Layanan" ambil dari daftar Layanan Digital modul Risiko (Opsi A), atau daftar independen milik MPN sendiri (Opsi B)? | Menentukan struktur tabel & apakah MPN bergantung pada modul Risiko |
| 3 | "Instansi/dinas" untuk kode pengetahuan = `desa` yang sudah ada di sistem, atau konsep baru yang lebih luas? | Menentukan apakah perlu tabel/konsep baru di luar `desa` |
| 4 | Form 1 MPN perlu alur approval Admin (draft/submitted/approved/rejected) seperti modul Risiko, atau cukup draft/final sederhana untuk sekarang? | Menentukan kolom `status` & halaman Review perlu dibuat sekarang atau tidak |
| 5 | Bagian "Indikator Capaian Manajemen Pengetahuan" (As-Is/To-Be) termasuk cakupan Form 1 tahap ini? | Menentukan apakah perlu tabel/form tambahan sekarang |
