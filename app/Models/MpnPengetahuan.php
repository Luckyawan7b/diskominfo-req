<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MpnPengetahuan extends Model
{
    use SoftDeletes;

    protected $table = 'mpn_pengetahuan';
    protected $fillable = [
        'mpn_layanan_id', 'kode_pengetahuan', 'nama_pengetahuan',
        'ref_aspek_pemdi_id', 'ref_indikator_pemdi_id', 'sudah_terdokumentasi',
        'tipe_dok_teks', 'tipe_dok_gambar', 'tipe_dok_audio', 'tipe_dok_video',
        'penanggung_jawab_dokumentasi', 'target_waktu_dokumentasi',
        'pemilik_pengetahuan', 'urutan', 'created_by'
    ];

    protected $casts = [
        'sudah_terdokumentasi' => 'boolean',
        'tipe_dok_teks' => 'boolean',
        'tipe_dok_gambar' => 'boolean',
        'tipe_dok_audio' => 'boolean',
        'tipe_dok_video' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->kode_pengetahuan)) {
                $layanan = $model->layanan;
                if ($layanan) {
                    $konteks = MpnKonteks::find($layanan->mpn_konteks_id);
                    if ($konteks) {
                        $desa = $konteks->desa;
                        $kodeDesa = $desa ? $desa->kode_desa : 'UNKNOWN';
                        $tahun = $konteks->tahun_penilaian;
                        
                        $count = self::whereHas('layanan', function ($q) use ($konteks) {
                            $q->where('mpn_konteks_id', $konteks->id);
                        })->count();
                        
                        $urutanStr = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
                        $model->kode_pengetahuan = "MRP-{$kodeDesa}-{$tahun}-{$urutanStr}";
                    }
                }
            }
        });
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(MpnLayanan::class, 'mpn_layanan_id');
    }

    public function aspek(): BelongsTo
    {
        return $this->belongsTo(RefAspekPemdi::class, 'ref_aspek_pemdi_id');
    }

    public function indikator(): BelongsTo
    {
        return $this->belongsTo(RefIndikatorPemdi::class, 'ref_indikator_pemdi_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
