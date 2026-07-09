<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Record;
use App\Models\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ROIFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $clinic;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clinic = Clinic::factory()->create([
            'status' => 'active',
        ]);

        $this->user = User::factory()->create([
            'clinic_id' => $this->clinic->id,
        ]);

        $this->user->givePermissionTo('roi.view');
    }

    /** @test */
    public function user_can_view_roi_summary_without_filters()
    {
        // Criar dados de teste
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        Record::factory(10)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'approved',
            'amount_billed' => 1000.00,
            'amount_paid' => 800.00,
            'amount_pending' => 200.00,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/roi/summary');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'period',
                'volume',
                'quality',
                'financial_impact',
            ]
        ]);
    }

    /** @test */
    public function user_can_view_roi_summary_with_period_filters()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        Record::factory(5)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'procedure_date' => '2024-07-15',
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/roi/summary', [
            'period_start' => '2024-07-01',
            'period_end' => '2024-07-31',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'period',
                'volume',
            ]
        ]);
    }

    /** @test */
    public function roi_rejects_invalid_period()
    {
        $response = $this->actingAs($this->user)->getJson('/api/roi/summary', [
            'period_start' => '2024-12-31',
            'period_end' => '2024-01-01',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function roi_uses_real_processed_records()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        // Criar registros com diferentes status
        Record::factory(5)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'approved',
            'amount_billed' => 1000.00,
        ]);

        Record::factory(2)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'rejected',
            'amount_billed' => 500.00,
        ]);

        Record::factory(1)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'disputed',
            'amount_billed' => 300.00,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/roi/summary');

        $response->assertStatus(200);
        $data = $response->json('data');

        // Validar que usa dados reais
        $this->assertGreaterThan(0, $data['volume']['total_records']);
        $this->assertGreaterThan(0, $data['financial_impact']['total_billed']);
    }

    /** @test */
    public function user_cannot_view_other_clinic_roi_data()
    {
        $otherClinic = Clinic::factory()->create();
        $otherUser = User::factory()->create([
            'clinic_id' => $otherClinic->id,
        ]);

        $upload = Upload::factory()->create([
            'clinic_id' => $otherClinic->id,
            'user_id' => $otherUser->id,
        ]);

        Record::factory(10)->create([
            'clinic_id' => $otherClinic->id,
            'upload_id' => $upload->id,
        ]);

        // Usuário da clínica 1 não deve ver dados da clínica 2
        $response = $this->actingAs($this->user)->getJson('/api/roi/summary');

        $response->assertStatus(200);
        $data = $response->json('data');

        // Dados devem ser da clínica do usuário autenticado
        $this->assertEquals(0, $data['volume']['total_records']);
    }

    /** @test */
    public function roi_executive_report_with_filters()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        Record::factory(10)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'amount_billed' => 1000.00,
            'amount_paid' => 800.00,
            'amount_pending' => 200.00,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/roi/executive-report', [
            'period_start' => '2024-01-01',
            'period_end' => '2024-12-31',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'clinic_name',
                'period',
                'executive_summary',
                'volume_analysis',
                'quality_analysis',
                'financial_impact',
            ]
        ]);
    }
}
