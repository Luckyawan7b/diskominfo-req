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
        Schema::create('mpn_konteks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa')->cascadeOnDelete();
            $table->year('tahun_penilaian');
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['desa_id', 'tahun_penilaian']);
        });

        Schema::create('mpn_capaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mpn_konteks_id')->constrained('mpn_konteks')->cascadeOnDelete();
            $table->string('nama_indikator');
            $table->text('kondisi_as_is')->nullable();
            $table->text('target_to_be')->nullable();
            $table->smallInteger('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mpn_layanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mpn_konteks_id')->constrained('mpn_konteks')->cascadeOnDelete();
            $table->string('nama_layanan');
            $table->boolean('termasuk_layanan_prioritas')->default(false);
            $table->smallInteger('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('mpn_pengetahuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mpn_layanan_id')->constrained('mpn_layanan')->cascadeOnDelete();
            $table->string('kode_pengetahuan')->nullable(); // Set null first, then calculated
            $table->text('nama_pengetahuan');
            
            $table->foreignId('ref_aspek_pemdi_id')->nullable()->constrained('ref_aspek_pemdi')->nullOnDelete();
            $table->foreignId('ref_indikator_pemdi_id')->nullable()->constrained('ref_indikator_pemdi')->nullOnDelete();
            
            $table->boolean('sudah_terdokumentasi')->default(false);
            
            // Kolom untuk 'Ya'
            $table->boolean('tipe_dok_teks')->default(false);
            $table->boolean('tipe_dok_gambar')->default(false);
            $table->boolean('tipe_dok_audio')->default(false);
            $table->boolean('tipe_dok_video')->default(false);
            $table->string('penanggung_jawab_dokumentasi')->nullable();
            $table->string('target_waktu_dokumentasi')->nullable();
            
            // Kolom untuk 'Tidak'
            $table->string('pemilik_pengetahuan')->nullable();
            
            $table->smallInteger('urutan')->default(0);
            
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpn_pengetahuan');
        Schema::dropIfExists('mpn_layanan');
        Schema::dropIfExists('mpn_capaian');
        Schema::dropIfExists('mpn_konteks');
    }
};
