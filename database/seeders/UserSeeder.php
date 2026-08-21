<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed 1 admin (tanpa desa) + 1 operator per desa dummy.
     * Jangan jalankan di production.
     */
    public function run(): void
    {
        $adminRole    = Role::where('name', 'admin')->firstOrFail();
        $operatorRole = Role::where('name', 'operator')->firstOrFail();

        // Admin kabupaten — desa_id null (bisa akses semua desa)
        User::firstOrCreate(
            ['email' => 'admin@diskominfo.test'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
                'role_id'  => $adminRole->id,
                'desa_id'  => null,
            ]
        );

        // 1 operator per desa
        $desas = Desa::all();

        foreach ($desas as $desa) {
            $slug = strtolower($desa->kode_desa);

            User::firstOrCreate(
                ['email' => "operator.{$slug}@diskominfo.test"],
                [
                    'name'     => "Operator {$desa->nama_desa}",
                    'password' => Hash::make('password'),
                    'role_id'  => $operatorRole->id,
                    'desa_id'  => $desa->id,
                ]
            );
        }
    }
}
