<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Models\Record;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FinalizeUploadJob implements ShouldQueue
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
            Log::info("Persistindo registros normalizados", [
                'upload_id' => $this->upload->id,
            ]);

            // Recuperar registros normalizados do cache (criados por NormalizeRecordsJob)
            $normalizedCacheKey = "upload_normalized_{$this->upload->id}";
            $normalizedRecords = cache()->get($normalizedCacheKey, []);

            if (empty($normalizedRecords)) {
                Log::warning("Nenhum registro normalizado encontrado em cache", [
                    'upload_id' => $this->upload->id,
                ]);
                return;
            }

            // Persistir registros no banco de dados
            $recordsToInsert = [];
            foreach ($normalizedRecords as $record) {
                $recordsToInsert[] = [
                    'clinic_id' => $this->upload->clinic_id,
                    'upload_id' => $this->upload->id,
                    'patient_name' => $record['patient_name'] ?? null,
                    'patient_cpf' => $record['patient_cpf'] ?? null,
                    'patient_id' => $record['patient_id'] ?? null,
                    'procedure_code' => $record['procedure_code'],
                    'procedure_name' => $record['procedure_name'] ?? null,
                    'procedure_date' => $record['procedure_date'],
                    'amount_billed' => $record['amount_billed'],
                    'amount_paid' => $record['amount_paid'] ?? 0,
                    'amount_pending' => $record['amount_pending'] ?? 0,
                    'status' => 'pending',
                    'provider_name' => $record['provider_name'] ?? null,
                    'provider_id' => $record['provider_id'] ?? null,
                    'insurance_name' => $record['insurance_name'] ?? null,
                    'insurance_id' => $record['insurance_id'] ?? null,
                    'authorization_number' => $record['authorization_number'] ?? null,
                    'raw_data' => json_encode($record),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Inserir em chunks para evitar memory overflow
            if (!empty($recordsToInsert)) {
                foreach (array_chunk($recordsToInsert, 500) as $chunk) {
                    Record::insert($chunk);
                }
            }

            Log::info("Registros persistidos com sucesso", [
                'upload_id' => $this->upload->id,
                'record_count' => count($recordsToInsert),
            ]);

            // Limpar cache de parsing (normalização será limpa após validação)
            cache()->forget("upload_parsed_{$this->upload->id}");

            // Nota: Não limpar upload_normalized_* pois pode ser necessário para debug
            // Não limpar upload_validations_* pois será usado no ValidatePersistedRecordsJob
            // O status será atualizado para 'completed' após ValidatePersistedRecordsJob

            Log::info("Registros normalizados persistidos com sucesso", [
                'upload_id' => $this->upload->id,
                'record_count' => count($recordsToInsert),
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao finalizar upload", [
                'upload_id' => $this->upload->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->upload->update([
                'status' => 'failed',
                'processing_error_message' => $e->getMessage(),
                'processing_completed_at' => now(),
            ]);

            throw $e;
        }
    }
}
