<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MrLayananDigital extends Model
{
    protected $table = 'mr_layanan_digital';

    protected $fillable = [
        'mr_risiko_id',
        'perlu_mkb',
        'pic',
        'target_waktu_penyusunan',
    ];

    protected $casts = [
        'perlu_mkb' => 'boolean',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function risiko(): BelongsTo
    {
        return $this->belongsTo(MrRisiko::class, 'mr_risiko_id');
    }
}
