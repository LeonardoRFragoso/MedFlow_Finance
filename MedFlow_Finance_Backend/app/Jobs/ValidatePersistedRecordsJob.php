<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Models\Record;
use App\Models\Validation;
use App\Models\Error;
use App\Domains\Validation\Services\ValidationEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ValidatePersistedRecordsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;

    public function __construct(
        public Upload $upload
    ) {}

    public function handle(): void
    {
        try {
            Log::info("Iniciando validação de registros persistidos", [
                'upload_id' => $this->upload->id,
            ]);

            // Recuperar registros já persistidos no banco
            $records = Record::where('upload_id', $this->upload->id)->get();

            if ($records->isEmpty()) {
                Log::warning("Nenhum registro encontrado para validação", [
                    'upload_id' => $this->upload->id,
                ]);
                return;
            }

            $validationEngine = new ValidationEngine();
            $validationCount = 0;
            $errorCount = 0;

            foreach ($records as $record) {
                try {
                    // Converter record para array para validação
                    $recordData = $record->toArray();

                    // Executar validações
                    $result = $validationEngine->validate($recordData);

                    // Registrar validações no banco com record_id
                    foreach ($result['validations'] as $rule) {
                        Validation::create([
                            'clinic_id' => $this->upload->clinic_id,
                            'upload_id' => $this->upload->id,
                            'record_id' => $record->id,
                            'rule_name' => $rule['rule_name'],
                            'rule_type' => $rule['rule_type'],
                            'is_valid' => $rule['is_valid'],
                            'severity' => $rule['severity'],
                            'field_name' => $rule['field_name'] ?? null,
                            'expected_value' => $rule['expected_value'] ?? null,
                            'actual_value' => $rule['actual_value'] ?? null,
                            'message' => $rule['message'],
                            'rule_config' => $rule['config'] ?? null,
                        ]);

                        $validationCount++;
                    }

                    // Atualizar status do record baseado na validação
                    if (!$result['is_valid']) {
                        $record->update(['status' => 'error']);
                    }

                } catch (\Exception $e) {
                    Log::warning("Erro ao validar registro persistido", [
                        'upload_id' => $this->upload->id,
                        'record_id' => $record->id,
                        'error' => $e->getMessage(),
                    ]);

                    Error::create([
                        'clinic_id' => $this->upload->clinic_id,
                        'upload_id' => $this->upload->id,
                        'record_id' => $record->id,
                        'error_type' => 'validation',
                        'error_code' => 'VALIDATION_ERROR',
                        'error_message' => $e->getMessage(),
                        'status' => 'new',
                    ]);

                    $errorCount++;
                }
            }

            Log::info("Validação de registros persistidos concluída", [
                'upload_id' => $this->upload->id,
                'total_records' => $records->count(),
                'validations_created' => $validationCount,
                'errors' => $errorCount,
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao validar registros persistidos", [
                'upload_id' => $this->upload->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Error::create([
                'clinic_id' => $this->upload->clinic_id,
                'upload_id' => $this->upload->id,
                'error_type' => 'validation',
                'error_code' => 'VALIDATION_FAILED',
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'status' => 'new',
            ]);

            throw $e;
        }
    }
}
