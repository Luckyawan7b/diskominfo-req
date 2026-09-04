<?php

namespace Database\Seeders;

use App\Models\Dinas;
use Illuminate\Database\Seeder;

class DinasSeeder extends Seeder
{
    /**
     * 3 dinas dummy untuk dev/testing.
     * Jangan jalankan di production.
     */
    public function run(): void
    {
        $dinasList = [
            [
                'alias'      => 'DUKCAPIL',
                'nama_dinas' => 'Dinas Kependudukan dan Pencatatan Sipil',
            ],
            [
                'alias'      => 'KOMINFO',
                'nama_dinas' => 'Dinas Komunikasi dan Informatika',
            ],
            [
                'alias'      => 'KESBANGPOL',
                'nama_dinas' => 'Badan Kesatuan Bangsa dan Politik',
            ],
        ];

        foreach ($dinasList as $dinas) {
            Dinas::firstOrCreate(['alias' => $dinas['alias']], $dinas);
        }
    }
}
