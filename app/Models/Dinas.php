<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dinas extends Model
{
    use SoftDeletes;

    protected $table = 'dinas';

    protected $fillable = [
        'alias',
        'nama_dinas',
    ];

    public function mrKonteks(): HasMany
    {
        return $this->hasMany(MrKonteks::class);
    }

    public function mpnKonteks(): HasMany
    {
        return $this->hasMany(MpnKonteks::class);
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
