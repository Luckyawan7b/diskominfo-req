<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpnAlihPengetahuan extends Model
{
    protected $table = 'mpn_alih_pengetahuan';

    protected $fillable = [
        'mpn_pengetahuan_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'metode_pelatihan',
        'metode_workshop',
        'metode_sosialisasi',
        'metode_mentoring',
        'metode_sharing',
        'metode_lainnya',
        'keterangan_lainnya',
        'penerima_pengetahuan',
        'hasil_evaluasi',
        'created_by',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'metode_pelatihan' => 'boolean',
        'metode_workshop' => 'boolean',
        'metode_sosialisasi' => 'boolean',
        'metode_mentoring' => 'boolean',
        'metode_sharing' => 'boolean',
        'metode_lainnya' => 'boolean',
    ];

    public function pengetahuan()
    {
        return $this->belongsTo(MpnPengetahuan::class, 'mpn_pengetahuan_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
