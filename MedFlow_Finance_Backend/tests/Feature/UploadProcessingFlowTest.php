<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Upload;
use App\Models\Record;
use App\Models\Validation;
use App\Models\Error;
use App\Jobs\ProcessUploadJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadProcessingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $clinic;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('local');
        
        $this->clinic = Clinic::factory()->create([
            'status' => 'active',
            'max_monthly_uploads' => 100,
            'max_file_size_mb' => 100,
        ]);
        
        $this->user = User::factory()->create([
            'clinic_id' => $this->clinic->id,
        ]);
        
        $this->user->givePermissionTo('uploads.create');
    }

    /** @test */
    public function user_can_upload_csv_file()
    {
        $file = UploadedFile::fake()->create('faturamento.csv', 100, 'text/csv');

        $response = $this->actingAs($this->user)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
                'description' => 'Faturamento de janeiro',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'upload' => [
                        'id',
                        'clinic_id',
                        'user_id',
                        'original_filename',
                        'file_path',
                        'file_type',
                        'status',
                        'billing_period_start',
                        'billing_period_end',
                        'created_at',
                    ]
                ]
            ]);

        $this->assertDatabaseHas('uploads', [
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
            'original_filename' => 'faturamento.csv',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function upload_triggers_processing_job()
    {
        Queue::fake();
        
        $file = UploadedFile::fake()->create('faturamento.csv', 100, 'text/csv');

        $this->actingAs($this->user)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        Queue::assertPushed(ProcessUploadJob::class);
    }

    /** @test */
    public function user_cannot_upload_without_permission()
    {
        $userWithoutPermission = User::factory()->create([
            'clinic_id' => $this->clinic->id,
        ]);
        
        $file = UploadedFile::fake()->create('faturamento.csv', 100, 'text/csv');

        $response = $this->actingAs($userWithoutPermission)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function user_cannot_upload_if_clinic_inactive()
    {
        $inactiveClinic = Clinic::factory()->create(['status' => 'inactive']);
        $userInactiveClinic = User::factory()->create(['clinic_id' => $inactiveClinic->id]);
        $userInactiveClinic->givePermissionTo('uploads.create');
        
        $file = UploadedFile::fake()->create('faturamento.csv', 100, 'text/csv');

        $response = $this->actingAs($userInactiveClinic)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function upload_validates_file_type()
    {
        $file = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->user)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('file');
    }

    /** @test */
    public function upload_validates_billing_period()
    {
        $file = UploadedFile::fake()->create('faturamento.csv', 100, 'text/csv');

        $response = $this->actingAs($this->user)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-31',
                'billing_period_end' => '2024-01-01',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('billing_period_start');
    }

    /** @test */
    public function upload_prevents_duplicate_files()
    {
        $file1 = UploadedFile::fake()->create('faturamento.csv', 100, 'text/csv');
        
        $this->actingAs($this->user)
            ->postJson('/api/uploads', [
                'file' => $file1,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        $file2 = UploadedFile::fake()->create('faturamento.csv', 100, 'text/csv');
        
        $response = $this->actingAs($this->user)
            ->postJson('/api/uploads', [
                'file' => $file2,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function user_can_view_upload_list()
    {
        Upload::factory()->count(5)->create([
            'clinic_id' => $this->clinic->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/uploads');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'original_filename',
                        'status',
                        'total_rows',
                        'valid_rows',
                        'error_rows',
                    ]
                ],
                'pagination' => [
                    'total',
                    'per_page',
                    'current_page',
                    'last_page',
                ]
            ]);

        $this->assertEquals(5, $response->json('pagination.total'));
    }

    /** @test */
    public function user_can_view_upload_details()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'status' => 'completed',
            'total_rows' => 100,
            'valid_rows' => 95,
            'error_rows' => 5,
            'warning_rows' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/uploads/{$upload->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'upload' => [
                        'id',
                        'original_filename',
                        'status',
                        'total_rows',
                    ],
                    'statistics' => [
                        'total_rows',
                        'valid_rows',
                        'error_rows',
                        'warning_rows',
                        'success_rate',
                    ],
                    'errors' => [],
                ]
            ]);

        $this->assertEquals(95.0, $response->json('data.statistics.success_rate'));
    }

    /** @test */
    public function user_can_check_upload_status()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'status' => 'processing',
            'total_rows' => 100,
            'valid_rows' => 50,
            'error_rows' => 0,
            'warning_rows' => 0,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/uploads/{$upload->id}/status");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'status',
                    'progress',
                    'statistics' => [
                        'total_rows',
                        'valid_rows',
                        'error_rows',
                        'warning_rows',
                    ],
                    'error_message',
                ]
            ]);

        $this->assertEquals('processing', $response->json('data.status'));
        $this->assertGreaterThan(0, $response->json('data.progress'));
    }

    /** @test */
    public function user_can_delete_upload()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/uploads/{$upload->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('uploads', ['id' => $upload->id]);
    }

    /** @test */
    public function user_cannot_view_other_clinic_uploads()
    {
        $otherClinic = Clinic::factory()->create();
        $upload = Upload::factory()->create(['clinic_id' => $otherClinic->id]);

        $response = $this->actingAs($this->user)
            ->getJson("/api/uploads/{$upload->id}");

        $response->assertStatus(404);
    }

    /** @test */
    public function upload_respects_monthly_limit()
    {
        $clinic = Clinic::factory()->create([
            'max_monthly_uploads' => 2,
        ]);
        
        $user = User::factory()->create(['clinic_id' => $clinic->id]);
        $user->givePermissionTo('uploads.create');

        Upload::factory()->count(2)->create([
            'clinic_id' => $clinic->id,
            'created_at' => now(),
        ]);

        $file = UploadedFile::fake()->create('faturamento.csv', 100, 'text/csv');

        $response = $this->actingAs($user)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        $response->assertStatus(429);
    }

    /** @test */
    public function upload_respects_file_size_limit()
    {
        $clinic = Clinic::factory()->create([
            'max_file_size_mb' => 1,
        ]);
        
        $user = User::factory()->create(['clinic_id' => $clinic->id]);
        $user->givePermissionTo('uploads.create');

        $file = UploadedFile::fake()->create('faturamento.csv', 5000, 'text/csv');

        $response = $this->actingAs($user)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        $response->assertStatus(422);
    }
}
