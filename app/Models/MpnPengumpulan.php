<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpnPengumpulan extends Model
{
    protected $table = 'mpn_pengumpulan';

    protected $fillable = [
        'mpn_pengetahuan_id',
        'tanggal_pengumpulan',
        'unit_pengumpulan',
        'lokasi_penyimpanan',
        'tanggal_terakhir_update',
        'rating_pengetahuan',
        'keterangan_lokasi_lainnya',
        'status_publikasi_simpan',
        'ref_metode_pengolahan_id',
        'deskripsi_pengolahan',
        'kode_pengetahuan_baru',
        'nama_pengetahuan_baru',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pengumpulan' => 'date',
        'tanggal_terakhir_update' => 'date',
        'rating_pengetahuan' => 'integer',
    ];

    public function pengetahuan()
    {
        return $this->belongsTo(MpnPengetahuan::class, 'mpn_pengetahuan_id');
    }

    public function metodePengolahan()
    {
        return $this->belongsTo(RefMetodePengolahan::class, 'ref_metode_pengolahan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
