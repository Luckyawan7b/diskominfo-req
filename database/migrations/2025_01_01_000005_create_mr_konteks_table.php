<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mr_konteks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desa_id')->constrained('desa')->cascadeOnDelete();
            $table->string('nama_instansi');
            $table->string('nama_upr');
            $table->text('tugas_upr')->nullable();
            $table->text('fungsi_upr')->nullable();
            $table->year('tahun_penilaian');
            // Selera risiko: batas nilai besaran_risiko yang masih dapat diterima (1-25)
            $table->unsignedTinyInteger('selera_risiko')->default(16);

            /**
             * Approval flow (dikonfirmasi saat grilling):
             *   draft      → Operator bisa edit bebas
             *   submitted  → Menunggu review Admin; operator tidak bisa edit
             *   approved   → Read-only untuk semua pihak
             *   rejected   → Operator bisa edit kembali; lihat catatan_penolakan di mr_risiko
             *   archived   → Hanya Admin yang bisa set; data tahun sebelumnya yang sudah selesai
             */
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'archived'])
                ->default('draft');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            // 1 desa hanya boleh punya 1 konteks per tahun
            $table->unique(['desa_id', 'tahun_penilaian']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_konteks');
    }
};
