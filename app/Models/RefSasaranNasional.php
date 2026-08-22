<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefSasaranNasional extends Model
{
    protected $table = 'ref_sasaran_nasional';

    protected $fillable = [
        'teks_sasaran',
    ];

    public function sasaranUpr()
    {
        return $this->hasMany(MrSasaranUpr::class, 'ref_sasaran_nasional_id');
    }
}
