<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom layanan_id nullable dulu
        Schema::table('mr_konteks', function (Blueprint $table) {
            $table->foreignId('layanan_id')->nullable()->constrained('layanans')->onDelete('cascade');
        });

        // 2. Buat Layanan placeholder untuk tiap mr_konteks yang ada
        $konteksRecords = DB::table('mr_konteks')->get();
        foreach ($konteksRecords as $konteks) {
            $layananId = DB::table('layanans')->insertGetId([
                'desa_id' => $konteks->desa_id,
                'nama_layanan' => 'Layanan belum diberi nama — migrasi otomatis (Tahun ' . $konteks->tahun_penilaian . ')',
                'deskripsi_layanan' => 'Layanan ini dibuat secara otomatis dari data Manajemen Risiko tahun ' . $konteks->tahun_penilaian . '. Silakan edit form ini untuk melengkapi data layanan yang sebenarnya.',
                'status_layanan' => 'berjalan',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Assign kembali id layanan ke mr_konteks
            DB::table('mr_konteks')
                ->where('id', $konteks->id)
                ->update(['layanan_id' => $layananId]);
        }

        // 3. Drop constraint unique lama (jika masih ada) dan buat unique baru
        Schema::table('mr_konteks', function (Blueprint $table) {
            // Guard: migration sebelumnya (2026_08_25) mungkin sudah drop unique ini
            if (Schema::hasIndex('mr_konteks', 'mr_konteks_desa_id_tahun_penilaian_unique')) {
                $table->dropUnique(['desa_id', 'tahun_penilaian']);
            }
            $table->unique('layanan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mr_konteks', function (Blueprint $table) {
            $table->dropUnique(['layanan_id']);
            $table->unique(['desa_id', 'tahun_penilaian']);
            $table->dropForeign(['layanan_id']);
            $table->dropColumn('layanan_id');
        });
    }
};
