<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\ClinicSetting;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::firstOrCreate(
            ['cnpj' => '12.345.678/0001-90'],
            [
                'name' => 'Clínica Teste MedFlow',
                'email' => 'contato@clinicateste.local',
                'phone' => '(11) 3000-0000',
                'address' => 'Rua Teste, 123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip_code' => '01000-000',
                'billing_type' => 'private',
                'default_currency' => 'BRL',
                'status' => 'active',
                'plan_type' => 'basic',
                'plan_started_at' => now(),
                'plan_expires_at' => now()->addYear(),
                'max_users' => 10,
                'max_monthly_uploads' => 500,
                'max_file_size_mb' => 100,
            ]
        );

        // Criar configurações padrão da clínica
        ClinicSetting::firstOrCreate(
            ['clinic_id' => $clinic->id],
            [
                'default_billing_type' => 'private',
                'currency' => 'BRL',
                'enable_glosa_detection' => true,
                'enable_compliance_check' => true,
                'validation_rules' => json_encode([]),
                'data_retention_days' => 2555,
                'auto_delete_old_files' => false,
                'notify_on_upload_complete' => true,
                'notify_on_error' => true,
                'notification_email' => 'contato@clinicateste.local',
            ]
        );
    }
}
