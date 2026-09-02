<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpnCapaian extends Model
{
    use SoftDeletes;

    protected $table = 'mpn_capaian';
    protected $fillable = ['mpn_konteks_id', 'nama_indikator', 'kondisi_as_is', 'target_to_be', 'urutan'];

    public function konteks(): BelongsTo
    {
        return $this->belongsTo(MpnKonteks::class, 'mpn_konteks_id');
    }
}
