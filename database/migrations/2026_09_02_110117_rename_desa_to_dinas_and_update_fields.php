<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename tabel `desa` menjadi `dinas`
        Schema::rename('desa', 'dinas');

        // 2. Sesuaikan kolom di tabel `dinas`
        Schema::table('dinas', function (Blueprint $table) {
            $table->renameColumn('kode_desa', 'alias');
            $table->renameColumn('nama_desa', 'nama_dinas');
            $table->dropColumn(['kecamatan', 'kabupaten', 'provinsi']);
        });

        // 3. Update FK dan rename kolom `desa_id` di `users`
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['desa_id']);
            $table->renameColumn('desa_id', 'dinas_id');
            $table->foreign('dinas_id')->references('id')->on('dinas')->nullOnDelete();
        });

        // 4. Update FK dan rename kolom `desa_id` di `mr_konteks`
        Schema::table('mr_konteks', function (Blueprint $table) {
            $table->dropForeign(['desa_id']);
            $table->dropUnique(['desa_id', 'tahun_penilaian']);
            $table->renameColumn('desa_id', 'dinas_id');
            $table->foreign('dinas_id')->references('id')->on('dinas')->cascadeOnDelete();
            $table->unique(['dinas_id', 'tahun_penilaian']);
        });

        // 5. Update FK dan rename kolom `desa_id` di `mpn_konteks`
        Schema::table('mpn_konteks', function (Blueprint $table) {
            $table->dropForeign(['desa_id']);
            $table->dropUnique(['desa_id', 'tahun_penilaian']);
            $table->renameColumn('desa_id', 'dinas_id');
            $table->foreign('dinas_id')->references('id')->on('dinas')->cascadeOnDelete();
            $table->unique(['dinas_id', 'tahun_penilaian']);
        });
    }

    public function down(): void
    {
        // Rollback `mpn_konteks`
        Schema::table('mpn_konteks', function (Blueprint $table) {
            $table->dropForeign(['dinas_id']);
            $table->dropUnique(['dinas_id', 'tahun_penilaian']);
            $table->renameColumn('dinas_id', 'desa_id');
            // Referensikan kembali ke `dinas` karena tabel belum di-rename balik di titik ini
            $table->foreign('desa_id')->references('id')->on('dinas')->cascadeOnDelete();
            $table->unique(['desa_id', 'tahun_penilaian']);
        });

        // Rollback `mr_konteks`
        Schema::table('mr_konteks', function (Blueprint $table) {
            $table->dropForeign(['dinas_id']);
            $table->dropUnique(['dinas_id', 'tahun_penilaian']);
            $table->renameColumn('dinas_id', 'desa_id');
            $table->foreign('desa_id')->references('id')->on('dinas')->cascadeOnDelete();
            $table->unique(['desa_id', 'tahun_penilaian']);
        });

        // Rollback `users`
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['dinas_id']);
            $table->renameColumn('dinas_id', 'desa_id');
            $table->foreign('desa_id')->references('id')->on('dinas')->nullOnDelete();
        });

        // Rollback kolom tabel `dinas`
        Schema::table('dinas', function (Blueprint $table) {
            $table->renameColumn('alias', 'kode_desa');
            $table->renameColumn('nama_dinas', 'nama_desa');
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('provinsi')->nullable();
        });

        // Rename tabel `dinas` kembali ke `desa`
        Schema::rename('dinas', 'desa');
    }
};
