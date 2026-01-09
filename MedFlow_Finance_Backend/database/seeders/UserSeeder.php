<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::where('cnpj', '12.345.678/0001-90')->first();

        if (!$clinic) {
            return;
        }

        // Criar usuário admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@medflow.local'],
            [
                'clinic_id' => $clinic->id,
                'name' => 'Administrador MedFlow',
                'password' => Hash::make('password'),
                'phone' => '(11) 99999-9999',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Atribuir role de admin
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $admin->roles()->syncWithoutDetaching($adminRole->id);
        }

        // Criar usuário gestor financeiro
        $financialManager = User::firstOrCreate(
            ['email' => 'gestor@medflow.local'],
            [
                'clinic_id' => $clinic->id,
                'name' => 'Gestor Financeiro',
                'password' => Hash::make('password'),
                'phone' => '(11) 99999-8888',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Atribuir role de financial_manager
        $financialRole = Role::where('name', 'financial_manager')->first();
        if ($financialRole) {
            $financialManager->roles()->syncWithoutDetaching($financialRole->id);
        }

        // Criar usuário administrativo
        $administrative = User::firstOrCreate(
            ['email' => 'admin.clinica@medflow.local'],
            [
                'clinic_id' => $clinic->id,
                'name' => 'Administrativo Clínica',
                'password' => Hash::make('password'),
                'phone' => '(11) 99999-7777',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Atribuir role de administrative
        $administrativeRole = Role::where('name', 'administrative')->first();
        if ($administrativeRole) {
            $administrative->roles()->syncWithoutDetaching($administrativeRole->id);
        }
    }
}
