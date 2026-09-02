<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MpnLayanan extends Model
{
    use SoftDeletes;

    protected $table = 'mpn_layanan';
    protected $fillable = ['mpn_konteks_id', 'nama_layanan', 'termasuk_layanan_prioritas', 'urutan'];

    protected $casts = [
        'termasuk_layanan_prioritas' => 'boolean',
    ];

    public function konteks(): BelongsTo
    {
        return $this->belongsTo(MpnKonteks::class, 'mpn_konteks_id');
    }

    public function pengetahuan(): HasMany
    {
        return $this->hasMany(MpnPengetahuan::class)->orderBy('urutan');
    }
}
