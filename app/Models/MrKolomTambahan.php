<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MrKolomTambahan extends Model
{
    protected $table = 'mr_kolom_tambahan';

    protected $fillable = [
        'mr_risiko_id',
        'layanan_pendukung',
        'layanan_prioritas',
        'pemilik_layanan',
        'strategis_atau_operasional',
        'lintas_sektor',
        'ippd_terkait',
        'membutuhkan_perubahan',
    ];

    protected $casts = [
        'lintas_sektor'        => 'boolean',
        'membutuhkan_perubahan' => 'boolean',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function risiko(): BelongsTo
    {
        return $this->belongsTo(MrRisiko::class, 'mr_risiko_id');
    }

    public function layananDigital(): HasOne
    {
        return $this->hasOne(MrLayananDigital::class, 'mr_risiko_id', 'mr_risiko_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Apakah layanan ini termasuk Prioritas — trigger auto-create MrLayananDigital */
    public function isPrioritas(): bool
    {
        return $this->layanan_prioritas === 'Prioritas';
    }
}
