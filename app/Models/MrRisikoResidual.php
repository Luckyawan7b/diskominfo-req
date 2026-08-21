<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MrRisikoResidual extends Model
{
    protected $table = 'mr_risiko_residual';

    protected $fillable = [
        'mr_risiko_id',
        'level_kemungkinan',
        'level_dampak',
        'besaran_risiko',
        'keterangan_residual',
    ];

    protected $casts = [
        'level_kemungkinan' => 'integer',
        'level_dampak'      => 'integer',
        'besaran_risiko'    => 'integer',
    ];

    public function risiko(): BelongsTo
    {
        return $this->belongsTo(MrRisiko::class, 'mr_risiko_id');
    }
}
