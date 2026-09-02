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
        Schema::create('ref_aspek_pemdi', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aspek');
            $table->smallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('ref_indikator_pemdi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ref_aspek_pemdi_id')->constrained('ref_aspek_pemdi')->cascadeOnDelete();
            $table->string('nama_indikator');
            $table->smallInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_indikator_pemdi');
        Schema::dropIfExists('ref_aspek_pemdi');
    }
};
