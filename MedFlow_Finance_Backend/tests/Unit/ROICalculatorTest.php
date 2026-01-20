<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Domains\Report\Services\ROICalculator;
use App\Models\Record;
use App\Models\Clinic;
use App\Models\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ROICalculatorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function calculates_success_rate_correctly()
    {
        $clinic = Clinic::factory()->create();
        $upload = Upload::factory()->create(['clinic_id' => $clinic->id]);
        
        // 80 aprovados
        Record::factory()->count(80)->create([
            'clinic_id' => $clinic->id,
            'upload_id' => $upload->id,
            'status' => 'approved',
        ]);
        
        // 20 rejeitados
        Record::factory()->count(20)->create([
            'clinic_id' => $clinic->id,
            'upload_id' => $upload->id,
            'status' => 'rejected',
        ]);

        $calculator = new ROICalculator($clinic->id);
        $roi = $calculator->calculate();

        $this->assertEquals(100, $roi['volume']['total_records']);
        $this->assertEquals(80, $roi['volume']['approved']);
        $this->assertEquals(80.0, $roi['volume']['success_rate']);
        $this->assertEquals(20.0, $roi['volume']['error_rate']);
    }

    /** @test */
    public function calculates_financial_impact_correctly()
    {
        $clinic = Clinic::factory()->create();
        $upload = Upload::factory()->create(['clinic_id' => $clinic->id]);
        
        // 80 aprovados (R$ 1000 cada) = R$ 80.000
        Record::factory()->count(80)->create([
            'clinic_id' => $clinic->id,
            'upload_id' => $upload->id,
            'status' => 'approved',
            'amount_billed' => 1000.00,
        ]);
        
        // 20 rejeitados (R$ 1000 cada) = R$ 20.000 em risco
        Record::factory()->count(20)->create([
            'clinic_id' => $clinic->id,
            'upload_id' => $upload->id,
            'status' => 'rejected',
            'amount_billed' => 1000.00,
        ]);

        $calculator = new ROICalculator($clinic->id);
        $roi = $calculator->calculate();

        // Total faturado: 100 * 1000 = R$ 100.000
        $this->assertStringContainsString('100.000', $roi['financial_impact']['total_billed']);
        
        // Valor em risco: 20 * 1000 = R$ 20.000
        $this->assertStringContainsString('20.000', $roi['financial_impact']['value_at_risk']);
        
        // Potencial recuperação: 20.000 * 0.15 = R$ 3.000
        $this->assertStringContainsString('3.000', $roi['financial_impact']['potential_recovery']);
    }

    /** @test */
    public function calculates_time_saved_correctly()
    {
        $clinic = Clinic::factory()->create();
        $upload = Upload::factory()->create(['clinic_id' => $clinic->id]);
        
        // 100 registros
        Record::factory()->count(100)->create([
            'clinic_id' => $clinic->id,
            'upload_id' => $upload->id,
        ]);

        $calculator = new ROICalculator($clinic->id);
        $roi = $calculator->calculate();

        // 100 registros * 2 min = 200 min = 3.33 horas
        $this->assertGreaterThan(3.0, $roi['time_saved']['hours_saved']);
        $this->assertLessThan(4.0, $roi['time_saved']['hours_saved']);
    }

    /** @test */
    public function detects_high_glosa_risk()
    {
        $clinic = Clinic::factory()->create();
        $upload = Upload::factory()->create(['clinic_id' => $clinic->id]);
        
        // 70 aprovados
        Record::factory()->count(70)->create([
            'clinic_id' => $clinic->id,
            'upload_id' => $upload->id,
            'status' => 'approved',
        ]);
        
        // 30 rejeitados (30% = risco alto)
        Record::factory()->count(30)->create([
            'clinic_id' => $clinic->id,
            'upload_id' => $upload->id,
            'status' => 'rejected',
        ]);

        $calculator = new ROICalculator($clinic->id);
        $roi = $calculator->calculate();

        $this->assertEquals(30.0, $roi['glosa_risk']['glosa_percentage']);
        $this->assertEquals('high', $roi['glosa_risk']['risk_level']);
    }

    /** @test */
    public function detects_low_glosa_risk()
    {
        $clinic = Clinic::factory()->create();
        $upload = Upload::factory()->create(['clinic_id' => $clinic->id]);
        
        // 100 aprovados (0% rejeição = risco baixo)
        Record::factory()->count(100)->create([
            'clinic_id' => $clinic->id,
            'upload_id' => $upload->id,
            'status' => 'approved',
        ]);

        $calculator = new ROICalculator($clinic->id);
        $roi = $calculator->calculate();

        $this->assertEquals(0.0, $roi['glosa_risk']['glosa_percentage']);
        $this->assertEquals('low', $roi['glosa_risk']['risk_level']);
    }
}
