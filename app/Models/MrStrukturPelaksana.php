<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MrStrukturPelaksana extends Model
{
    protected $table = 'mr_struktur_pelaksana';

    protected $fillable = [
        'mr_konteks_id',
        'pemilik_risiko',
        'koordinator_risiko',
        'pengelola_risiko',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function konteks(): BelongsTo
    {
        return $this->belongsTo(MrKonteks::class, 'mr_konteks_id');
    }
}
