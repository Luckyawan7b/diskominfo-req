<?php

namespace Database\Seeders;

use App\Models\Desa;
use Illuminate\Database\Seeder;

class DesaSeeder extends Seeder
{
    /**
     * 3 desa dummy untuk dev/testing.
     * Jangan jalankan di production.
     */
    public function run(): void
    {
        $desas = [
            [
                'kode_desa'  => 'SKM',
                'nama_desa'  => 'Desa Sukamaju',
                'kecamatan'  => 'Kecamatan Ciawi',
                'kabupaten'  => 'Kabupaten Bogor',
                'provinsi'   => 'Jawa Barat',
            ],
            [
                'kode_desa'  => 'MJY',
                'nama_desa'  => 'Desa Mekarjaya',
                'kecamatan'  => 'Kecamatan Dramaga',
                'kabupaten'  => 'Kabupaten Bogor',
                'provinsi'   => 'Jawa Barat',
            ],
            [
                'kode_desa'  => 'PSD',
                'nama_desa'  => 'Desa Pasirsdang',
                'kecamatan'  => 'Kecamatan Sukajaya',
                'kabupaten'  => 'Kabupaten Bogor',
                'provinsi'   => 'Jawa Barat',
            ],
        ];

        foreach ($desas as $desa) {
            Desa::firstOrCreate(['kode_desa' => $desa['kode_desa']], $desa);
        }
    }
}
