<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RefKategoriRisiko extends Model
{
    protected $table = 'ref_kategori_risiko';

    protected $fillable = ['nama_kategori', 'urutan'];

    public function risiko(): HasMany
    {
        return $this->hasMany(MrRisiko::class, 'ref_kategori_risiko_id');
    }
}
