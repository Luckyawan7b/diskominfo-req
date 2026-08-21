<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mr_risiko', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mr_konteks_id')->constrained('mr_konteks')->cascadeOnDelete();

            // Referensi ke sasaran (nullable: baris risiko tetap ada walau sasaran terkait dihapus)
            $table->foreignId('mr_sasaran_id')->nullable()->constrained('mr_sasaran')->nullOnDelete();

            // Snapshot teks sasaran saat risiko dibuat — histori tidak berubah walau sasaran diedit
            $table->text('sasaran_pembangunan_nasional_snapshot')->nullable();
            $table->text('sasaran_upr_snapshot')->nullable();
            $table->string('indikator_kinerja_snapshot')->nullable();

            $table->string('kode_risiko'); // unik per konteks, mis. "R-001"
            $table->text('peristiwa_risiko');

            $table->foreignId('ref_kategori_risiko_id')
                ->nullable()
                ->constrained('ref_kategori_risiko')
                ->nullOnDelete();

            $table->text('penyebab')->nullable();
            $table->text('dampak')->nullable();
            $table->enum('area_dampak', [
                'Penurunan Reputasi',
                'Keuangan',
                'Gangguan Terhadap Layanan Organisasi',
                'Penurunan Kinerja',
            ])->nullable();

            // Skala 1-5, diisi operator; besaran_risiko dihitung otomatis oleh RiskMatrixCalculator
            $table->unsignedTinyInteger('level_kemungkinan')->nullable();
            $table->unsignedTinyInteger('level_dampak')->nullable();

            // Disimpan (bukan dihitung on-the-fly) agar laporan historis tidak berubah
            // bila rumus matriks direvisi di masa depan — diisi oleh MrRisikoObserver
            $table->unsignedTinyInteger('besaran_risiko')->nullable();

            // Di-generate ulang via Observer setiap ada insert/update/delete risiko dalam 1 konteks
            $table->unsignedSmallInteger('prioritas_risiko')->nullable();

            /**
             * Approval flow per baris risiko (dikonfirmasi saat grilling):
             *   draft      → Operator bisa edit
             *   submitted  → Menunggu review admin (bersamaan dengan submit konteks)
             *   approved   → Disetujui admin
             *   rejected   → Admin menolak; operator baca catatan_penolakan dan revisi
             */
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected'])->default('draft');

            // Diisi admin saat reject; operator membaca ini untuk tahu apa yang perlu direvisi
            $table->text('catatan_penolakan')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // kode_risiko unik hanya dalam 1 konteks (lintas desa boleh sama)
            $table->unique(['mr_konteks_id', 'kode_risiko']);

            // Index untuk peta risiko dan sort prioritas (query paling sering)
            $table->index(['mr_konteks_id', 'besaran_risiko']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_risiko');
    }
};
