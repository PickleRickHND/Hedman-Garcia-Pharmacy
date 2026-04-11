<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Principal', 'email' => 'admin@pharmacy.hn',    'password' => 'admin123',    'role' => 'Administrador'],
            ['name' => 'Douglas Hedman',  'email' => 'douglas@pharmacy.hn',  'password' => 'admin123',    'role' => 'Administrador'],
            ['name' => 'Maria Garcia',    'email' => 'maria@pharmacy.hn',    'password' => 'cajero123',   'role' => 'Cajero'],
            ['name' => 'Carlos Lopez',    'email' => 'carlos@pharmacy.hn',   'password' => 'cajero123',   'role' => 'Cajero'],
            ['name' => 'Invitado Demo',   'email' => 'invitado@pharmacy.hn', 'password' => 'invitado123', 'role' => 'Invitado'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'email_verified_at' => now(),
                ],
            );
            $user->syncRoles([$data['role']]);
        }
    }
}
