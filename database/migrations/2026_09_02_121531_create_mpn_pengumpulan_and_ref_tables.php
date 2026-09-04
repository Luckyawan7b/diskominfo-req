<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabel referensi metode pengolahan
        Schema::create('ref_metode_pengolahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_metode');
            $table->text('deskripsi_mekanisme');
            $table->text('output_contoh');
            $table->timestamps();
        });

        // Tabel Form 2: Pengumpulan & Pengelolaan Pengetahuan
        Schema::create('mpn_pengumpulan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mpn_pengetahuan_id')->constrained('mpn_pengetahuan')->onDelete('cascade');
            
            // Kolom wajib
            $table->date('tanggal_pengumpulan');
            $table->string('unit_pengumpulan');
            $table->string('lokasi_penyimpanan'); // Pilihan: Manajemen Risiko, Manajemen Pengetahuan, Manajemen Perubahan, Manajemen Keberlangsungan, Manajemen Relasi, Lainnya
            $table->date('tanggal_terakhir_update');
            $table->tinyInteger('rating_pengetahuan'); // 1-5
            
            // Kolom kondisional (kuning) -> jika status_dokumentasi 'belum'
            $table->string('keterangan_lokasi_lainnya')->nullable();
            $table->enum('status_publikasi_simpan', ['belum_dipublikasikan', 'ditolak', 'dipublikasikan', 'arsip'])->nullable();
            $table->foreignId('ref_metode_pengolahan_id')->nullable()->constrained('ref_metode_pengolahan')->onDelete('set null');
            $table->text('deskripsi_pengolahan')->nullable();
            $table->string('kode_pengetahuan_baru')->nullable(); // format ID-REV
            $table->text('nama_pengetahuan_baru')->nullable();
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // 1 Pengetahuan hanya bisa punya 1 form pengumpulan
            $table->unique('mpn_pengetahuan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpn_pengumpulan');
        Schema::dropIfExists('ref_metode_pengolahan');
    }
};
