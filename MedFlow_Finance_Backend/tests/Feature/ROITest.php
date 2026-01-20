<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Upload;
use App\Models\Record;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ROITest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $clinic;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->clinic = Clinic::factory()->create();
        $this->user = User::factory()->create([
            'clinic_id' => $this->clinic->id,
        ]);
    }

    /** @test */
    public function roi_summary_returns_correct_structure()
    {
        $upload = Upload::factory()->create(['clinic_id' => $this->clinic->id]);
        
        Record::factory()->count(80)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'approved',
        ]);
        
        Record::factory()->count(20)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'rejected',
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/roi/summary');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'volume' => ['total_records', 'approved', 'rejected', 'success_rate'],
                'quality' => ['error_percentage', 'total_errors'],
                'glosa_risk' => ['glosa_percentage', 'risk_level'],
                'financial_impact' => ['total_billed', 'value_at_risk'],
                'time_saved' => ['hours_saved', 'money_saved'],
                'recommendations',
            ]);
    }

    /** @test */
    public function roi_calculates_success_rate_correctly()
    {
        $upload = Upload::factory()->create(['clinic_id' => $this->clinic->id]);
        
        Record::factory()->count(80)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'approved',
            'amount_billed' => 1000.00,
        ]);
        
        Record::factory()->count(20)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'rejected',
            'amount_billed' => 1000.00,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/roi/summary');

        $volume = $response->json('volume');
        $this->assertEquals(100, $volume['total_records']);
        $this->assertEquals(80, $volume['approved']);
        $this->assertEquals(80.0, $volume['success_rate']);
    }
}
