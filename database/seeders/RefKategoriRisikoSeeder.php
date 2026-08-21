<?php

namespace Database\Seeders;

use App\Models\RefKategoriRisiko;
use Illuminate\Database\Seeder;

class RefKategoriRisikoSeeder extends Seeder
{
    /**
     * 10 Kategori Risiko Resmi sesuai pedoman Manajemen Risiko SPBE.
     * Urutan sesuai sheet "Keterangan Tambahan" pada template Excel resmi.
     */
    public function run(): void
    {
        $kategori = [
            ['urutan' => 1,  'nama_kategori' => 'Risiko Strategis'],
            ['urutan' => 2,  'nama_kategori' => 'Risiko Operasional'],
            ['urutan' => 3,  'nama_kategori' => 'Risiko Keuangan'],
            ['urutan' => 4,  'nama_kategori' => 'Risiko Kepatuhan'],
            ['urutan' => 5,  'nama_kategori' => 'Risiko Hukum'],
            ['urutan' => 6,  'nama_kategori' => 'Risiko Reputasi'],
            ['urutan' => 7,  'nama_kategori' => 'Risiko Keamanan Informasi'],
            ['urutan' => 8,  'nama_kategori' => 'Risiko Teknologi'],
            ['urutan' => 9,  'nama_kategori' => 'Risiko Sumber Daya Manusia'],
            ['urutan' => 10, 'nama_kategori' => 'Risiko Lingkungan'],
        ];

        foreach ($kategori as $item) {
            RefKategoriRisiko::firstOrCreate(
                ['nama_kategori' => $item['nama_kategori']],
                $item
            );
        }
    }
}
