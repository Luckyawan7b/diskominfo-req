<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefMetodePengolahan extends Model
{
    protected $table = 'ref_metode_pengolahan';

    protected $fillable = [
        'nama_metode',
        'deskripsi_mekanisme',
        'output_contoh',
    ];

    public function mpnPengumpulan()
    {
        return $this->hasMany(MpnPengumpulan::class, 'ref_metode_pengolahan_id');
    }
}
