<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'description' => 'Administrador da clínica',
                'role_type' => 'system',
            ],
            [
                'name' => 'financial_manager',
                'description' => 'Gestor financeiro',
                'role_type' => 'system',
            ],
            [
                'name' => 'administrative',
                'description' => 'Administrativo',
                'role_type' => 'system',
            ],
            [
                'name' => 'viewer',
                'description' => 'Visualizador (read-only)',
                'role_type' => 'system',
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
