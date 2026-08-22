<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ref_sasaran_nasional', function (Blueprint $table) {
            $table->id();
            // string(500), bukan text(), supaya bisa diberi unique index (MySQL tidak
            // bisa unique index di kolom TEXT tanpa prefix length).
            $table->string('teks_sasaran', 500)->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ref_sasaran_nasional');
    }
};
