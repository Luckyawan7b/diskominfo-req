<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Desa extends Model
{
    use SoftDeletes;

    protected $table = 'desa';

    protected $fillable = [
        'kode_desa',
        'nama_desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
    ];

    public function konteks(): HasMany
    {
        return $this->hasMany(MrKonteks::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function layanans(): HasMany
    {
        return $this->hasMany(Layanan::class);
    }
}
