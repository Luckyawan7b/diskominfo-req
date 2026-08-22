<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MrIndikatorKinerja extends Model
{
    use SoftDeletes;

    protected $table = 'mr_indikator_kinerja';

    protected $fillable = [
        'mr_sasaran_upr_id',
        'indikator_kinerja',
        'target_kinerja',
        'urutan',
    ];

    public function sasaranUpr()
    {
        return $this->belongsTo(MrSasaranUpr::class, 'mr_sasaran_upr_id');
    }
}
