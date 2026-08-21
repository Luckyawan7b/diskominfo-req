<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed urutan dependency:
     * roles → desa → users (butuh roles & desa) → ref data → (demo data opsional)
     */
    public function run(): void
    {
        // ── Data master (wajib, semua environment) ──────────────────────────
        $this->call([
            RoleSeeder::class,
            RefKategoriRisikoSeeder::class,
        ]);

        // ── Data dev/testing (jangan jalankan di production) ─────────────────
        if (app()->environment(['local', 'testing'])) {
            $this->call([
                DesaSeeder::class,
                UserSeeder::class,  // butuh roles & desa sudah ada
            ]);
        }
    }
}
