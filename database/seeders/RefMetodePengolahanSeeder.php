<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefMetodePengolahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_metode' => 'Penyimpulan Otomatis (Automated Summarization)',
                'deskripsi_mekanisme' => 'Pemanfaatan AI untuk memadatkan dokumen panjang (regulasi, laporan, SOP) tanpa menghilangkan esensi informasi.',
                'output_contoh' => 'Ringkasan eksekutif, poin-poin penting (bullet points).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_metode' => 'Sintesis Pengetahuan Mutakhir (Knowledge Synthesis)',
                'deskripsi_mekanisme' => 'Pemanfaatan AI untuk memadatkan menggabungkan informasi dari beberapa dokumen terpisah untuk menjawab satu isu spesifik.',
                'output_contoh' => 'Jawaban komprehensif berbasis arsitektur RAG (Retrieval-Augmented Generation).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_metode' => 'Anotasi Gambar & Diagram Fisik',
                'deskripsi_mekanisme' => 'Pemanfaatan AI untuk menerjemahkan aset visual (diagram alir, foto fasilitas) menjadi deskripsi tekstual.',
                'output_contoh' => 'Dokumentasi proses bisnis (BPMN) atau manual teknis yang dapat dicari.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_metode' => 'Narasi Data (Data Storytelling)',
                'deskripsi_mekanisme' => 'Pemanfaatan AI untuk mengubah angka dan tren dari dasbor analitik menjadi laporan berbasis narasi bahasa alami.',
                'output_contoh' => 'Laporan perkembangan bulanan otomatis, analisis tren performa.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_metode' => 'Penyusunan Materi Pembelajaran',
                'deskripsi_mekanisme' => 'Mengolah dokumen teknis internal menjadi materi edukasi yang siap pakai.',
                'output_contoh' => 'Modul pelatihan karyawan, kurikulum onboarding, soal kuis evaluasi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_metode' => 'Digitalisasi Tacit Knowledge',
                'deskripsi_mekanisme' => 'Mengolah hasil wawancara informal dengan pakar senior menjadi aset pengetahuan formal.',
                'output_contoh' => 'Dokumen FAQ (Tanya-Jawab), panduan troubleshooting mandiri.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_metode' => 'Dedupikasi & Rekonsiliasi Konseptual',
                'deskripsi_mekanisme' => 'Mendeteksi dan menyatukan dokumen yang memiliki kemiripan makna meskipun redaksi katanya berbeda.',
                'output_contoh' => 'Repositori bersih dari dokumen ganda/redundan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        \Illuminate\Support\Facades\DB::table('ref_metode_pengolahan')->insert($data);
    }
}
