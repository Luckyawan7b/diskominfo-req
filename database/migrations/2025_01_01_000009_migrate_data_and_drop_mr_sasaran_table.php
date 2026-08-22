<?php

use App\Models\MrIndikatorKinerja;
use App\Models\MrSasaranUpr;
use App\Models\RefSasaranNasional;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('mr_sasaran')) {
            return;
        }

        Schema::table('mr_risiko', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropForeign(['mr_sasaran_id']);
            $table->foreignId('mr_sasaran_upr_id')->nullable()->after('mr_konteks_id')->constrained('mr_sasaran_upr')->nullOnDelete();
        });

        DB::table('mr_sasaran')
            ->orderBy('mr_konteks_id')
            ->orderBy('urutan')
            ->get()
            ->each(function ($old) {
                $refId = null;

                if (filled($old->sasaran_pembangunan_nasional)) {
                    $ref = RefSasaranNasional::firstOrCreate([
                        // dipotong 500 karakter mengikuti batas kolom teks_sasaran
                        'teks_sasaran' => mb_substr(trim($old->sasaran_pembangunan_nasional), 0, 500),
                    ]);
                    $refId = $ref->id;
                }

                $upr = MrSasaranUpr::create([
                    'mr_konteks_id'           => $old->mr_konteks_id,
                    'ref_sasaran_nasional_id' => $refId,
                    'sasaran_upr'             => $old->sasaran_upr,
                    'urutan'                  => $old->urutan,
                ]);

                DB::table('mr_risiko')
                    ->where('mr_sasaran_id', $old->id)
                    ->update(['mr_sasaran_upr_id' => $upr->id]);

                if (filled($old->indikator_kinerja) || filled($old->target_kinerja)) {
                    MrIndikatorKinerja::create([
                        'mr_sasaran_upr_id' => $upr->id,
                        'indikator_kinerja' => $old->indikator_kinerja,
                        'target_kinerja'    => $old->target_kinerja,
                        'urutan'            => 0,
                    ]);
                }
            });

        Schema::table('mr_risiko', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn('mr_sasaran_id');
        });

        Schema::drop('mr_sasaran');
    }

    public function down(): void
    {
        // Migrasi data bersifat satu arah. Jika perlu rollback,
        // pulihkan tabel mr_sasaran dari backup database sebelum migrasi ini dijalankan.
    }
};
