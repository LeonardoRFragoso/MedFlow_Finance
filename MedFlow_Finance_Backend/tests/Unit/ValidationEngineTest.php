<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Domains\Validation\Services\ValidationEngine;

class ValidationEngineTest extends TestCase
{
    /** @test */
    public function validates_required_fields()
    {
        $engine = new ValidationEngine();
        
        $record = [
            'patient_name' => 'João Silva',
            'patient_cpf' => '12345678901',
            // procedure_code faltando (obrigatório)
            'procedure_date' => '2024-01-15',
            'amount_billed' => 1500.00,
        ];
        
        $result = $engine->validate($record);
        
        $this->assertFalse($result['is_valid']);
        $this->assertGreaterThan(0, count($result['validations']));
    }

    /** @test */
    public function validates_data_types()
    {
        $engine = new ValidationEngine();
        
        $record = [
            'patient_name' => 'João Silva',
            'patient_cpf' => '12345678901',
            'procedure_code' => '123456',
            'procedure_date' => '2024-01-15',
            'amount_billed' => 'abc', // tipo inválido (deveria ser numérico)
        ];
        
        $result = $engine->validate($record);
        
        $this->assertFalse($result['is_valid']);
    }

    /** @test */
    public function passes_validation_with_valid_data()
    {
        $engine = new ValidationEngine();
        
        $record = [
            'patient_name' => 'João Silva',
            'patient_cpf' => '12345678901',
            'procedure_code' => '123456',
            'procedure_date' => '2024-01-15',
            'amount_billed' => 1500.00,
            'health_plan' => 'Unimed',
            'authorization_code' => 'AUTH123',
        ];
        
        $result = $engine->validate($record);
        
        $this->assertTrue($result['is_valid']);
    }
}
