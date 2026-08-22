<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MrSasaranUpr extends Model
{
    use SoftDeletes;

    protected $table = 'mr_sasaran_upr';

    protected $fillable = [
        'mr_konteks_id',
        'ref_sasaran_nasional_id',
        'sasaran_upr',
        'urutan',
    ];

    public function konteks()
    {
        return $this->belongsTo(MrKonteks::class, 'mr_konteks_id');
    }

    public function sasaranNasional()
    {
        return $this->belongsTo(RefSasaranNasional::class, 'ref_sasaran_nasional_id');
    }

    public function indikator()
    {
        return $this->hasMany(MrIndikatorKinerja::class, 'mr_sasaran_upr_id')->orderBy('urutan');
    }
}
