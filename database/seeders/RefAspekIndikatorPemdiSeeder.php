<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RefAspekIndikatorPemdiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Tata Kelola dan Manajemen' => [
                'Tata Kelola dan Manajemen',
                'Manajemen Layanan Digital',
            ],
            'Penyelenggara' => [
                'Sumber daya manusia',
                'Kolaborasi Pemerintah Digital',
            ],
            'Data' => [
                'Tata kelola Data',
                'Pemanfaatan Informasi Geospasial',
                'Pembangunan Statistik',
                'Perlindungan data pribadi',
            ],
            'Keamanan Siber' => [
                'Pelaksanaan Audit Keamanan Siber',
                'Keamanan Siber',
                'Kriptografi untuk Keamanan Data',
                'Penanganan Insiden Siber',
            ],
            'Teknologi Digital' => [
                'Aplikasi Pemerintah Digital',
                'Infrastruktur Pemerintah Digital',
            ],
            'Keterpaduan Layanan Digital Pemerintah' => [
                'Keterpaduan proses bisnis',
                'Integrasi aplikasi',
                'Portal Layanan Digital Pemerintah',
                'Interoperabilitas data',
            ],
            'Kepuasan Pengguna Layanan Digital Pemerintah' => [
                'Fasilitas dukungan pengguna',
                'Tingkat kepuasan pengguna',
            ],
        ];

        DB::table('ref_indikator_pemdi')->delete();
        DB::table('ref_aspek_pemdi')->delete();

        $urutanAspek = 1;
        foreach ($data as $aspek => $indikators) {
            $aspekId = DB::table('ref_aspek_pemdi')->insertGetId([
                'nama_aspek' => $aspek,
                'urutan' => $urutanAspek++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $urutanIndikator = 1;
            foreach ($indikators as $indikator) {
                DB::table('ref_indikator_pemdi')->insert([
                    'ref_aspek_pemdi_id' => $aspekId,
                    'nama_indikator' => $indikator,
                    'urutan' => $urutanIndikator++,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
