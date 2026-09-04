<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mr_konteks', function (Blueprint $table) {
            if (!Schema::hasColumn('mr_konteks', 'layanan_id')) {
                $table->foreignId('layanan_id')->nullable()->constrained('layanan')->cascadeOnDelete();
            }
            
            // Drop old unique constraint
            $indexes = Schema::getIndexes('mr_konteks');
            $hasIndex = false;
            foreach($indexes as $index) {
                if ($index['name'] === 'mr_konteks_dinas_id_tahun_penilaian_unique') {
                    $hasIndex = true;
                    break;
                }
            }
            if ($hasIndex) {
                if (DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign('mr_konteks_dinas_id_foreign');
                    $table->dropUnique('mr_konteks_dinas_id_tahun_penilaian_unique');
                    $table->foreign('dinas_id')->references('id')->on('dinas')->cascadeOnDelete();
                }
            }
        });

        // Data Migration: Buat layanan placeholder untuk setiap mr_konteks yang belum punya
        $konteksList = DB::table('mr_konteks')->get();
        foreach ($konteksList as $konteks) {
            $layananId = DB::table('layanan')->insertGetId([
                'dinas_id' => $konteks->dinas_id,
                'nama_layanan' => 'Layanan (Migrasi Otomatis) ' . $konteks->id,
                'deskripsi_layanan' => 'Layanan placeholder dari migrasi sistem lama.',
                'status_layanan' => 'berjalan',
                'target_pengguna' => 'Publik/Masyarakat',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('mr_konteks')->where('id', $konteks->id)->update(['layanan_id' => $layananId]);
        }

        Schema::table('mr_konteks', function (Blueprint $table) {
            $table->unique('layanan_id');
        });
    }

    public function down(): void
    {
        Schema::table('mr_konteks', function (Blueprint $table) {
            $table->dropUnique(['layanan_id']);
            $table->dropForeign(['layanan_id']);
            $table->dropColumn('layanan_id');
            // We cannot easily recreate the old unique constraint if we don't know the exact column name, but we assume dinas_id
            $table->unique(['dinas_id', 'tahun_penilaian']);
        });
    }
};
