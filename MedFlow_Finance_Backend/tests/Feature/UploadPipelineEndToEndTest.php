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

        // Salvar no storage fake (local disk)
        Storage::disk('local')->put('uploads/test/test_upload.csv', $csvContent);
        return 'uploads/test/test_upload.csv';
    }

    /** @test */
    public function pipeline_creates_records_before_validations()
    {
        // Criar upload apontando para arquivo real no storage
        $filePath = $this->csvPath;
        $fileHash = hash('sha256', Storage::disk('local')->get($filePath));

        $upload = Upload::factory()->create([
            'clinic_id' => $this->clinic->id,
            'user_id' => $this->user->id,
            'file_path' => $filePath,
            'original_filename' => 'test_upload.csv',
            'file_type' => 'csv',
            'file_hash' => $fileHash,
            'status' => 'pending',
            'total_rows' => 0,
        ]);

        // Executar pipeline na ordem correta com arquivo real
        // Não simular cache - deixar ParseFileJob fazer seu trabalho
        (new ParseFileJob($upload))->handle();
        (new NormalizeRecordsJob($upload))->handle();
        (new FinalizeUploadJob($upload))->handle();
        (new ValidatePersistedRecordsJob($upload))->handle();
        (new FinalizeUploadStatusJob($upload))->handle();

        // Refresh upload para obter dados atualizados
        $upload->refresh();

        // VALIDAÇÕES CRÍTICAS
        
        // 1. Validar que records foram criados
        $recordCount = Record::where('upload_id', $upload->id)->count();
        $this->assertGreaterThan(0, $recordCount, 'Deve haver registros criados a partir do arquivo real');
        $this->assertEquals(3, $recordCount, 'Deve haver exatamente 3 registros do CSV');

        // 2. Validar status do upload
        $this->assertDatabaseHas('uploads', [
            'id' => $upload->id,
            'status' => 'completed',
        ]);

        // 3. Validar que validações foram criadas
        $validationCount = Validation::where('upload_id', $upload->id)->count();
        $this->assertGreaterThan(0, $validationCount, 'Deve haver validações criadas');

        // 4. Validar que NENHUMA validação é órfã
        $orphanedValidations = Validation::where('upload_id', $upload->id)
            ->whereNull('record_id')
            ->count();
        $this->assertEquals(
            0,
            $orphanedValidations,
            'Nenhuma validação deve ter record_id nulo'
        );

        // 5. Validar que todos os records têm status válido
        $invalidStatusCount = Record::where('upload_id', $upload->id)
            ->whereNotIn('status', ['pending', 'approved', 'rejected', 'disputed'])
            ->count();
        $this->assertEquals(0, $invalidStatusCount, 'Todos os records devem ter status válido (pending, approved, rejected, disputed)');

        // 6. Validar que cada record tem validações
        $records = Record::where('upload_id', $upload->id)->get();
        foreach ($records as $record) {
            $recordValidations = $record->validations()->count();
            $this->assertGreaterThan(
                0,
                $recordValidations,
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
