<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Models\Validation;
use App\Models\Error;
use App\Domains\Validation\Services\ValidationEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ValidateRecordsJob implements ShouldQueue
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
            Log::info("Iniciando validação de registros", [
                'upload_id' => $this->upload->id,
            ]);

            // Recuperar registros normalizados do cache
            $normalizedCacheKey = "upload_normalized_{$this->upload->id}";
            $normalizedRecords = cache()->get($normalizedCacheKey);

            if (!$normalizedRecords) {
                throw new \Exception("Registros normalizados não encontrados em cache");
            }

            $validationEngine = new ValidationEngine();
            $validRecords = [];
            $validationResults = [];
            $validationErrors = [];

            foreach ($normalizedRecords as $index => $record) {
                try {
                    // Executar validações
                    $result = $validationEngine->validate($record);

                    $validationResults[] = [
                        'record' => $record,
                        'result' => $result,
                        'index' => $index,
                    ];

                    // Contar como válido se não houver erros críticos
                    if ($result['is_valid']) {
                        $validRecords[] = $record;
                    }

                } catch (\Exception $e) {
                    Log::warning("Erro ao validar registro", [
                        'upload_id' => $this->upload->id,
                        'index' => $index,
                        'error' => $e->getMessage(),
                    ]);

                    $validationErrors[] = [
                        'index' => $index,
                        'error' => $e->getMessage(),
                        'record' => $record,
                    ];
                }
            }

            Log::info("Validação concluída", [
                'upload_id' => $this->upload->id,
                'valid_count' => count($validRecords),
                'invalid_count' => count($normalizedRecords) - count($validRecords),
            ]);

            // Armazenar resultados de validação em cache
            $validationCacheKey = "upload_validations_{$this->upload->id}";
            cache()->put($validationCacheKey, $validationResults, now()->addHours(24));

            // Armazenar registros válidos em cache
            $validRecordsCacheKey = "upload_valid_records_{$this->upload->id}";
            cache()->put($validRecordsCacheKey, $validRecords, now()->addHours(24));

            // Registrar validações no banco
            foreach ($validationResults as $validation) {
                foreach ($validation['result']['validations'] as $rule) {
                    Validation::create([
                        'clinic_id' => $this->upload->clinic_id,
                        'upload_id' => $this->upload->id,
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
                }
            }

            // Registrar erros de validação
            foreach ($validationErrors as $error) {
                Error::create([
                    'clinic_id' => $this->upload->clinic_id,
                    'upload_id' => $this->upload->id,
                    'error_type' => 'validation',
                    'error_code' => 'VALIDATION_ERROR',
                    'error_message' => $error['error'],
                    'row_number' => $error['index'] + 1,
                    'raw_value' => json_encode($error['record']),
                    'status' => 'new',
                ]);
            }

            // Atualizar contagem de registros válidos
            $this->upload->update([
                'valid_rows' => count($validRecords),
                'error_rows' => $this->upload->error_rows + count($validationErrors),
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao validar registros", [
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
