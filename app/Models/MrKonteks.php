<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class MrKonteks extends Model
{
    use SoftDeletes;

    protected $table = 'mr_konteks';

    protected $fillable = [
        'dinas_id',
        'layanan_id',
        'nama_instansi',
        'nama_upr',
        'tugas_upr',
        'fungsi_upr',
        'tahun_penilaian',
        'selera_risiko',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tahun_penilaian' => 'integer',
        'selera_risiko'   => 'integer',
    ];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function dinas(): BelongsTo
    {
        return $this->belongsTo(Dinas::class);
    }

    public function layanan(): BelongsTo
    {
        return $this->belongsTo(Layanan::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sasaranUpr(): HasMany
    {
        return $this->hasMany(MrSasaranUpr::class, 'mr_konteks_id')->orderBy('urutan');
    }

    public function strukturPelaksana(): HasOne
    {
        return $this->hasOne(MrStrukturPelaksana::class);
    }

    public function risiko(): HasMany
    {
        return $this->hasMany(MrRisiko::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Operator tidak bisa mengedit saat submitted/approved/archived */
    public function isEditableByOperator(): bool
    {
        return in_array($this->status, ['draft', 'rejected']);
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }
}
