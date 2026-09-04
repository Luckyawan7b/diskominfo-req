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
        Schema::create('mpn_alih_pengetahuan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mpn_pengetahuan_id')->constrained('mpn_pengetahuan')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            
            $table->boolean('metode_pelatihan')->default(false);
            $table->boolean('metode_workshop')->default(false);
            $table->boolean('metode_sosialisasi')->default(false);
            $table->boolean('metode_mentoring')->default(false);
            $table->boolean('metode_sharing')->default(false);
            $table->boolean('metode_lainnya')->default(false);
            
            $table->string('keterangan_lainnya')->nullable();
            $table->text('penerima_pengetahuan');
            $table->text('hasil_evaluasi');
            
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpn_alih_pengetahuan');
    }
};
