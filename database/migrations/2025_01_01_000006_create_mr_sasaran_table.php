<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mr_sasaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mr_konteks_id')->constrained('mr_konteks')->cascadeOnDelete();
            $table->text('sasaran_upr');
            $table->string('indikator_kinerja')->nullable();
            $table->string('target_kinerja')->nullable();
            $table->text('sasaran_pembangunan_nasional')->nullable();
            $table->smallInteger('urutan')->default(0); // urutan baris di Formulir 2
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mr_sasaran');
    }
};
