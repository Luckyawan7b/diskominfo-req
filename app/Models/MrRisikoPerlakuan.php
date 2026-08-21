<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MrRisikoPerlakuan extends Model
{
    protected $table = 'mr_risiko_perlakuan';

    protected $fillable = [
        'mr_risiko_id',
        'keputusan_perlakuan',
        'deskripsi_detail_perlakuan',
        'waktu_rencana_perlakuan',
        'penanggung_jawab',
    ];

    public function risiko(): BelongsTo
    {
        return $this->belongsTo(MrRisiko::class, 'mr_risiko_id');
    }
}
