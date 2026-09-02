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
        Schema::table('mr_konteks', function (Blueprint $table) {
            $table->dropForeign(['desa_id']);
            $table->dropUnique(['desa_id', 'tahun_penilaian']);
            $table->foreign('desa_id')->references('id')->on('desa')->cascadeOnDelete();
            
            $table->year('tahun_pelaksanaan')->nullable()->after('tahun_penilaian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mr_konteks', function (Blueprint $table) {
            $table->dropColumn('tahun_pelaksanaan');
            
            $table->dropForeign(['desa_id']);
            $table->unique(['desa_id', 'tahun_penilaian']);
            $table->foreign('desa_id')->references('id')->on('desa')->cascadeOnDelete();
        });
    }
};
