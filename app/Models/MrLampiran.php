<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MrLampiran extends Model
{
    use SoftDeletes;

    protected $table = 'mr_lampiran';

    protected $fillable = [
        'lampirable_id',
        'lampirable_type',
        'nama_file',
        'path_file',
        'mime_type',
        'ukuran_kb',
        'uploaded_by',
    ];

    protected $casts = [
        'ukuran_kb' => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    /** Polymorphic parent: bisa MrRisiko atau MrPemantauanRisiko */
    public function lampirable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
