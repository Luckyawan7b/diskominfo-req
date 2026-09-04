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
        Schema::create('mpn_pemanfaatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mpn_pengetahuan_id')->constrained('mpn_pengetahuan')->onDelete('cascade');
            $table->date('tanggal_pemanfaatan');
            $table->enum('tipe_pengguna', ['publik', 'internal']);
            $table->string('unit_pengguna');
            $table->text('tujuan_pemanfaatan');
            $table->tinyInteger('rating_pengetahuan');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpn_pemanfaatan');
    }
};
