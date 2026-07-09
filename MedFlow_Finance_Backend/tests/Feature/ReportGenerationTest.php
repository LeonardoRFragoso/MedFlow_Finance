<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Report;
use App\Models\Record;
use App\Models\Upload;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportGenerationTest extends TestCase
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

        $this->user->givePermissionTo('reports.create');
        $this->user->givePermissionTo('reports.view');
        $this->user->givePermissionTo('reports.delete');
        $this->user->givePermissionTo('reports.export');
    }

    /** @test */
    public function user_can_generate_summary_report()
    {
        // Criar dados de teste
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        Record::factory(5)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'approved',
        ]);

        // Gerar relatório
        $response = $this->actingAs($this->user)->postJson('/api/reports', [
            'type' => 'summary',
            'period_start' => '2024-01-01',
            'period_end' => '2024-12-31',
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'report' => [
                    'id',
                    'type',
                    'period_start',
                    'period_end',
                    'total_records',
                    'total_valid',
                    'content',
                ]
            ]
        ]);

        $this->assertDatabaseHas('reports', [
            'clinic_id' => $this->clinic->id,
            'report_type' => 'summary',
        ]);
    }

    /** @test */
    public function user_can_generate_detailed_report()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        Record::factory(3)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/reports', [
            'type' => 'detailed',
            'period_start' => '2024-01-01',
            'period_end' => '2024-12-31',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'report_type' => 'detailed',
        ]);
    }

    /** @test */
    public function user_can_generate_errors_report()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        Record::factory(2)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'status' => 'rejected',
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/reports', [
            'type' => 'errors',
            'period_start' => '2024-01-01',
            'period_end' => '2024-12-31',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'report_type' => 'errors',
        ]);
    }

    /** @test */
    public function user_can_generate_validation_report()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        Record::factory(3)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/reports', [
            'type' => 'validation',
            'period_start' => '2024-01-01',
            'period_end' => '2024-12-31',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'report_type' => 'validation',
        ]);
    }

    /** @test */
    public function user_can_generate_financial_report()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        Record::factory(5)->create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'amount_billed' => 1000.00,
            'amount_paid' => 800.00,
            'amount_pending' => 200.00,
        ]);

        $response = $this->actingAs($this->user)->postJson('/api/reports', [
            'type' => 'financial',
            'period_start' => '2024-01-01',
            'period_end' => '2024-12-31',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reports', [
            'report_type' => 'financial',
        ]);
    }

    /** @test */
    public function report_requires_valid_type()
    {
        $response = $this->actingAs($this->user)->postJson('/api/reports', [
            'type' => 'invalid_type',
            'period_start' => '2024-01-01',
            'period_end' => '2024-12-31',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('type');
    }

    /** @test */
    public function report_requires_valid_period()
    {
        $response = $this->actingAs($this->user)->postJson('/api/reports', [
            'type' => 'summary',
            'period_start' => '2024-12-31',
            'period_end' => '2024-01-01',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('period_start');
    }

    /** @test */
    public function user_can_export_report_as_csv()
    {
        $report = Report::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
            'report_type' => 'summary',
        ]);

        $response = $this->actingAs($this->user)->get("/api/reports/{$report->id}/export/csv");

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=utf-8');
        $response->assertHeader('Content-Disposition', "attachment; filename=\"medflow-report-{$report->id}.csv\"");
    }

    /** @test */
    public function pdf_export_returns_not_implemented()
    {
        $report = Report::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get("/api/reports/{$report->id}/export/pdf");

        $response->assertStatus(501);
        $response->assertJson([
            'success' => false,
            'message' => 'Exportação PDF ainda não disponível.',
        ]);
    }

    /** @test */
    public function user_cannot_access_other_clinic_reports()
    {
        $otherClinic = Clinic::factory()->create();
        $otherUser = User::factory()->create([
            'clinic_id' => $otherClinic->id,
        ]);

        $report = Report::factory()->create([
            'clinic_id' => $otherClinic->id,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)->get("/api/reports/{$report->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_list_reports()
    {
        Report::factory(3)->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->getJson('/api/reports');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'report_type',
                        'period_start',
                        'period_end',
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function user_can_delete_report()
    {
        $report = Report::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson("/api/reports/{$report->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('reports', [
            'id' => $report->id,
        ]);
    }
}
