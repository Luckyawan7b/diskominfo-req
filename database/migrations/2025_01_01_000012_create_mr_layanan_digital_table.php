<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mr_layanan_digital', function (Blueprint $table) {
            $table->id();
            // Bisa lebih dari 1 baris per risiko (walaupun dalam praktik biasanya 1:1)
            $table->foreignId('mr_risiko_id')->constrained('mr_risiko')->cascadeOnDelete();
            // null = belum ditentukan; berbeda maknanya dari false (tidak perlu MKB)
            $table->boolean('perlu_mkb')->nullable();
            $table->string('pic')->nullable();
            $table->string('target_waktu_penyusunan')->nullable();
            $table->timestamps();

            // Baris ini dibuat otomatis oleh MrKolomTambahanObserver
            // saat layanan_prioritas = 'Prioritas' diset di mr_kolom_tambahan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_layanan_digital');
    }
};
