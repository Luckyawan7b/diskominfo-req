<?php

namespace Database\Seeders;

use App\Models\Dinas;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed 1 admin (tanpa dinas) + 1 operator per dinas dummy.
     * Jangan jalankan di production.
     */
    public function run(): void
    {
        $adminRole    = Role::where('name', 'admin')->firstOrFail();
        $operatorRole = Role::where('name', 'operator')->firstOrFail();

        // Admin kabupaten — dinas_id null (bisa akses semua dinas)
        User::firstOrCreate(
            ['email' => 'admin@diskominfo.test'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('password'),
                'role_id'  => $adminRole->id,
                'dinas_id' => null,
            ]
        );

        // 1 operator per dinas
        $dinasList = Dinas::all();

        foreach ($dinasList as $dinas) {
            $slug = strtolower($dinas->alias);

            User::firstOrCreate(
                ['email' => "operator.{$slug}@diskominfo.test"],
                [
                    'name'     => "Operator {$dinas->nama_dinas}",
                    'password' => Hash::make('password'),
                    'role_id'  => $operatorRole->id,
                    'dinas_id' => $dinas->id,
                ]
            );
        }
    }
}
