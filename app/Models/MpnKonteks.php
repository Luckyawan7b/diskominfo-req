<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MpnKonteks extends Model
{
    use SoftDeletes;

    protected $table = 'mpn_konteks';
    protected $fillable = ['dinas_id', 'tahun_penilaian', 'status', 'created_by'];

    public function dinas(): BelongsTo
    {
        return $this->belongsTo(Dinas::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function capaian(): HasMany
    {
        return $this->hasMany(MpnCapaian::class)->orderBy('urutan');
    }

    public function layanan(): HasMany
    {
        return $this->hasMany(MpnLayanan::class)->orderBy('urutan');
    }
}
