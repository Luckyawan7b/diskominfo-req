<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom role_id dan desa_id ke tabel users bawaan Laravel.
     *
     * Catatan arsitektur: satu user terikat ke satu desa (via desa_id).
     * Untuk masa depan jika perlu banyak operator per desa, ganti ke tabel
     * pivot `user_desa` dan set desa_id di sini menjadi nullable/removed.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->after('id')
                ->constrained('roles')
                ->cascadeOnDelete();

            // null untuk admin (bisa akses semua desa)
            // tidak null untuk operator (terikat 1 desa)
            $table->foreignId('desa_id')
                ->after('role_id')
                ->nullable()
                ->constrained('desa')
                ->nullOnDelete();

            $table->softDeletes()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['desa_id']);
            $table->dropColumn(['role_id', 'desa_id', 'deleted_at']);
        });
    }
};
