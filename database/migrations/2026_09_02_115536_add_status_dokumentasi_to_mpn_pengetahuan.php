<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom status_dokumentasi ke mpn_pengetahuan.
     * Kolom ini menjadi gating untuk Form 2 (pengumpulan) & Form 3 (pemanfaatan).
     *
     * - 'belum' → pengetahuan belum terdokumentasi, Form 2 harus diisi lebih dulu.
     * - 'sudah' → pengetahuan sudah terdokumentasi / Form 2 selesai → Form 3 bisa diisi.
     *
     * Nilai di-derive dari `sudah_terdokumentasi` (bool, Form 1):
     *   - sudah_terdokumentasi = TRUE  → pengetahuan SUDAH ada dokumennya → status_dokumentasi = 'sudah'
     *   - sudah_terdokumentasi = FALSE → pengetahuan BELUM ada → status_dokumentasi = 'belum'
     * Nilai bisa naik menjadi 'sudah' secara otomatis setelah Form 2 selesai diisi.
     */
    public function up(): void
    {
        Schema::table('mpn_pengetahuan', function (Blueprint $table) {
            $table->enum('status_dokumentasi', ['belum', 'sudah'])
                  ->default('belum')
                  ->after('pemilik_pengetahuan')
                  ->comment('Gating: belum → Form 2 wajib diisi; sudah → Form 3 bisa diisi');
        });
    }

    public function down(): void
    {
        Schema::table('mpn_pengetahuan', function (Blueprint $table) {
            $table->dropColumn('status_dokumentasi');
        });
    }
};
