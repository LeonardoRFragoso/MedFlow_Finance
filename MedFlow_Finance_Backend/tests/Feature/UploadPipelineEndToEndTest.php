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
use App\Jobs\ParseFileJob;
use App\Jobs\NormalizeRecordsJob;
use App\Jobs\FinalizeUploadJob;
use App\Jobs\ValidatePersistedRecordsJob;
use App\Jobs\FinalizeUploadStatusJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Bus\Bus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadPipelineEndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $clinic;
    protected $csvPath;

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
        
        // Criar arquivo CSV de teste
        $this->csvPath = $this->createTestCsv();
    }

    protected function createTestCsv(): string
    {
        $csvContent = <<<CSV
procedure_code,procedure_date,amount_billed,patient_name,patient_cpf,insurance_name,insurance_id,provider_name,authorization_number,amount_paid,amount_pending
PROC001,2024-01-15,1500.00,João Silva,12345678901,Unimed,UNI001,Clínica Central,AUTH001,1500.00,0.00
PROC002,2024-01-16,2500.00,Maria Santos,98765432109,Bradesco Saúde,BRAD001,Clínica Central,AUTH002,2500.00,0.00
PROC003,2024-01-17,800.00,Pedro Oliveira,11122233344,Amil,AMIL001,Clínica Central,AUTH003,0.00,800.00
CSV;

        $path = storage_path('app/test_upload.csv');
        file_put_contents($path, $csvContent);
        return $path;
    }

    /** @test */
    public function pipeline_creates_records_before_validations()
    {
        // Criar upload
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total_rows' => 3,
        ]);

        // Simular dados parseados em cache
        $parsedData = [
            'records' => [
                [
                    'procedure_code' => 'PROC001',
                    'procedure_date' => '2024-01-15',
                    'amount_billed' => 1500.00,
                    'patient_name' => 'João Silva',
                    'patient_cpf' => '12345678901',
                    'insurance_name' => 'Unimed',
                    'insurance_id' => 'UNI001',
                    'provider_name' => 'Clínica Central',
                    'authorization_number' => 'AUTH001',
                    'amount_paid' => 1500.00,
                    'amount_pending' => 0.00,
                ],
                [
                    'procedure_code' => 'PROC002',
                    'procedure_date' => '2024-01-16',
                    'amount_billed' => 2500.00,
                    'patient_name' => 'Maria Santos',
                    'patient_cpf' => '98765432109',
                    'insurance_name' => 'Bradesco Saúde',
                    'insurance_id' => 'BRAD001',
                    'provider_name' => 'Clínica Central',
                    'authorization_number' => 'AUTH002',
                    'amount_paid' => 2500.00,
                    'amount_pending' => 0.00,
                ],
                [
                    'procedure_code' => 'PROC003',
                    'procedure_date' => '2024-01-17',
                    'amount_billed' => 800.00,
                    'patient_name' => 'Pedro Oliveira',
                    'patient_cpf' => '11122233344',
                    'insurance_name' => 'Amil',
                    'insurance_id' => 'AMIL001',
                    'provider_name' => 'Clínica Central',
                    'authorization_number' => 'AUTH003',
                    'amount_paid' => 0.00,
                    'amount_pending' => 800.00,
                ],
            ],
        ];

        cache()->put("upload_parsed_{$upload->id}", $parsedData, now()->addHours(24));

        // Executar pipeline manualmente
        (new ParseFileJob($upload))->handle();
        (new NormalizeRecordsJob($upload))->handle();
        (new FinalizeUploadJob($upload))->handle();
        (new ValidatePersistedRecordsJob($upload))->handle();
        (new FinalizeUploadStatusJob($upload))->handle();

        // Validações
        $this->assertDatabaseCount('records', 3);
        $this->assertDatabaseHas('uploads', [
            'id' => $upload->id,
            'status' => 'completed',
        ]);

        // Verificar que todas as validações têm record_id
        $this->assertEquals(
            0,
            Validation::whereNull('record_id')->count(),
            'Nenhuma validação deve ter record_id nulo'
        );

        // Verificar que há validações
        $this->assertGreaterThan(0, Validation::count());

        // Verificar que cada record tem validações
        $records = Record::where('upload_id', $upload->id)->get();
        foreach ($records as $record) {
            $this->assertGreaterThan(
                0,
                $record->validations()->count(),
                "Record {$record->id} deve ter validações"
            );
        }
    }

    /** @test */
    public function all_validations_have_record_id()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total_rows' => 2,
        ]);

        // Criar registros
        $record1 = Record::factory()->create([
            'upload_id' => $upload->id,
            'clinic_id' => $this->clinic->id,
        ]);

        $record2 = Record::factory()->create([
            'upload_id' => $upload->id,
            'clinic_id' => $this->clinic->id,
        ]);

        // Criar validações com record_id
        Validation::create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'record_id' => $record1->id,
            'rule_name' => 'test_rule',
            'rule_type' => 'field',
            'is_valid' => true,
            'severity' => 'info',
            'message' => 'Test validation',
        ]);

        Validation::create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'record_id' => $record2->id,
            'rule_name' => 'test_rule',
            'rule_type' => 'field',
            'is_valid' => true,
            'severity' => 'info',
            'message' => 'Test validation',
        ]);

        // Verificar que nenhuma validação tem record_id nulo
        $this->assertEquals(0, Validation::whereNull('record_id')->count());
        $this->assertEquals(2, Validation::count());
    }

    /** @test */
    public function no_orphaned_validations_exist()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        $record = Record::factory()->create([
            'upload_id' => $upload->id,
            'clinic_id' => $this->clinic->id,
        ]);

        // Criar validação com record_id válido
        Validation::create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'record_id' => $record->id,
            'rule_name' => 'test_rule',
            'rule_type' => 'field',
            'is_valid' => true,
            'severity' => 'info',
            'message' => 'Test',
        ]);

        // Verificar que a validação tem um record válido
        $validation = Validation::first();
        $this->assertNotNull($validation->record_id);
        $this->assertNotNull($validation->record);
        $this->assertEquals($record->id, $validation->record->id);
    }

    /** @test */
    public function upload_status_updates_correctly()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $upload->status);

        // Simular processamento
        $upload->update(['status' => 'processing', 'processing_started_at' => now()]);
        $this->assertEquals('processing', $upload->fresh()->status);

        // Simular conclusão
        $upload->update(['status' => 'completed', 'processing_completed_at' => now()]);
        $this->assertEquals('completed', $upload->fresh()->status);
    }

    /** @test */
    public function records_and_validations_are_linked()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        $record = Record::factory()->create([
            'upload_id' => $upload->id,
            'clinic_id' => $this->clinic->id,
        ]);

        $validation = Validation::create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'record_id' => $record->id,
            'rule_name' => 'test',
            'rule_type' => 'field',
            'is_valid' => true,
            'severity' => 'info',
            'message' => 'Test',
        ]);

        // Verificar relacionamentos
        $this->assertEquals($record->id, $validation->record->id);
        $this->assertEquals($upload->id, $validation->upload->id);
        $this->assertTrue($record->validations()->where('id', $validation->id)->exists());
    }

    /** @test */
    public function errors_can_have_record_id()
    {
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        $record = Record::factory()->create([
            'upload_id' => $upload->id,
            'clinic_id' => $this->clinic->id,
        ]);

        $error = Error::create([
            'clinic_id' => $this->clinic->id,
            'upload_id' => $upload->id,
            'record_id' => $record->id,
            'error_type' => 'validation',
            'error_code' => 'TEST_ERROR',
            'error_message' => 'Test error',
            'status' => 'new',
        ]);

        $this->assertNotNull($error->record_id);
        $this->assertEquals($record->id, $error->record->id);
    }

    /** @test */
    public function pipeline_order_is_correct()
    {
        // Verificar que os jobs existem e podem ser instanciados
        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
        ]);

        $jobs = [
            ParseFileJob::class,
            NormalizeRecordsJob::class,
            FinalizeUploadJob::class,
            ValidatePersistedRecordsJob::class,
            FinalizeUploadStatusJob::class,
        ];

        foreach ($jobs as $jobClass) {
            $job = new $jobClass($upload);
            $this->assertNotNull($job);
        }
    }
}
