<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Layanan extends Model
{
    use SoftDeletes;

    protected $table = 'layanan';

    protected $fillable = [
        'dinas_id',
        'created_by',
        'nama_layanan',
        'deskripsi_layanan',
        'bidang_bagian',
        'status_layanan',
        'target_pengguna',
        'is_prioritas',
        'kl_terkait',
        'supplier_data',
        'nama_data_input',
        'nama_data_output',
        'sifat_data',
        'jenis_data',
        'validitas_data',
        'interoperabilitas',
        'tujuan_integrasi',
        'metode_integrasi',
        'link_dokumen_integrasi',
        'nama_aplikasi',
        'tipe_aplikasi',
        'link_aplikasi',
        'keluaran_aplikasi',
        'letak_server',
        'tahun_pembuatan',
        'link_dpa',
        'link_sla',
        'link_sop',
        'helpdesk',
    ];

    protected $casts = [
        'is_prioritas' => 'boolean',
        'interoperabilitas' => 'boolean',
    ];

    public function dinas()
    {
        return $this->belongsTo(Dinas::class);
    }

    public function mrKonteks()
    {
        return $this->hasOne(MrKonteks::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
