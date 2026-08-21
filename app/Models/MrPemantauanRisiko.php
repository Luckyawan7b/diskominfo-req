<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MrPemantauanRisiko extends Model
{
    use SoftDeletes;

    protected $table = 'mr_pemantauan_risiko';

    protected $fillable = [
        'mr_risiko_id',
        'periode',
        'tahun',
        'hasil_pelaksanaan',
        'data_dukung_catatan',
        'created_by',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function risiko(): BelongsTo
    {
        return $this->belongsTo(MrRisiko::class, 'mr_risiko_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** File bukti dukung untuk pemantauan ini */
    public function lampiran(): MorphMany
    {
        return $this->morphMany(MrLampiran::class, 'lampirable');
    }
}
