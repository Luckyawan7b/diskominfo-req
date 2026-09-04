<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MpnPemanfaatan extends Model
{
    protected $table = 'mpn_pemanfaatan';

    protected $fillable = [
        'mpn_pengetahuan_id',
        'tanggal_pemanfaatan',
        'tipe_pengguna',
        'unit_pengguna',
        'tujuan_pemanfaatan',
        'rating_pengetahuan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pemanfaatan' => 'date',
        'rating_pengetahuan' => 'integer',
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
