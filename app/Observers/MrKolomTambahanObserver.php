<?php

namespace App\Observers;

use App\Models\MrKolomTambahan;
use App\Models\MrLayananDigital;

/**
 * MrKolomTambahanObserver
 *
 * Auto-create/delete baris `mr_layanan_digital` saat `layanan_prioritas`
 * di `mr_kolom_tambahan` diset ke 'Prioritas' atau diubah dari 'Prioritas'.
 */
class MrKolomTambahanObserver
{
    public function saved(MrKolomTambahan $kolom): void
    {
        if (! $kolom->isDirty('layanan_prioritas')) {
            return;
        }

        if ($kolom->layanan_prioritas === 'Prioritas') {
            // Auto-create baris layanan digital jika belum ada
            MrLayananDigital::firstOrCreate(
                ['mr_risiko_id' => $kolom->mr_risiko_id]
            );
        } else {
            // Hapus baris layanan digital jika layanan_prioritas diubah dari 'Prioritas'
            MrLayananDigital::where('mr_risiko_id', $kolom->mr_risiko_id)->delete();
        }
    }
}
