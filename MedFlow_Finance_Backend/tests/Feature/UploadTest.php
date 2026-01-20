<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Clinic;
use App\Models\Upload;
use App\Jobs\ProcessUploadJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $clinic;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        
        $this->clinic = Clinic::factory()->create();
        $this->user = User::factory()->create([
            'clinic_id' => $this->clinic->id,
        ]);
    }

    /** @test */
    public function user_can_upload_csv_file()
    {
        Queue::fake();
        $file = UploadedFile::fake()->create('faturamento.csv', 100, 'text/csv');

        $response = $this->actingAs($this->user)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'filename', 'status']]);

        $this->assertDatabaseHas('uploads', [
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function user_can_list_their_uploads()
    {
        Upload::factory()->count(3)->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/uploads');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function upload_triggers_processing_job()
    {
        Queue::fake();
        $file = UploadedFile::fake()->create('faturamento.csv', 100);

        $this->actingAs($this->user)
            ->postJson('/api/uploads', [
                'file' => $file,
                'billing_period_start' => '2024-01-01',
                'billing_period_end' => '2024-01-31',
            ]);

        Queue::assertPushed(ProcessUploadJob::class);
    }
}
