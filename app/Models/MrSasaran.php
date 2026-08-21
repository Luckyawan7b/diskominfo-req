<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MrSasaran extends Model
{
    use SoftDeletes;

    protected $table = 'mr_sasaran';

    protected $fillable = [
        'mr_konteks_id',
        'sasaran_upr',
        'indikator_kinerja',
        'target_kinerja',
        'sasaran_pembangunan_nasional',
        'urutan',
    ];

    protected $casts = [
        'urutan' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function konteks(): BelongsTo
    {
        return $this->belongsTo(MrKonteks::class, 'mr_konteks_id');
    }

    /**
     * Nullable referensi dari mr_risiko — sasaran bisa dihapus
     * tapi risiko terkait tetap ada (dengan snapshot teks).
     */
    public function risiko(): HasMany
    {
        return $this->hasMany(MrRisiko::class, 'mr_sasaran_id');
    }
}
