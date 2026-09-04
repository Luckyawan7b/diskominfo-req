<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed urutan dependency:
     * roles → dinas → users (butuh roles & dinas) → ref data → (demo data opsional)
     */
    public function run(): void
    {
        // ── Data master (wajib, semua environment) ──────────────────────────
        $this->call([
            RoleSeeder::class,
            RefKategoriRisikoSeeder::class,
            RefMetodePengolahanSeeder::class,
        ]);

        // ── Data dev/testing (jangan jalankan di production) ─────────────────
        if (app()->environment(['local', 'testing'])) {
            $this->call([
                DinasSeeder::class,
                UserSeeder::class,  // butuh roles & dinas sudah ada
            ]);
        }
    }
}
