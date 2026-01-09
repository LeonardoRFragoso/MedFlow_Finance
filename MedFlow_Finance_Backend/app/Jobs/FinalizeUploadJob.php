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
            Log::info("Finalizando upload", [
                'upload_id' => $this->upload->id,
            ]);

            // Recuperar registros válidos do cache
            $validRecordsCacheKey = "upload_valid_records_{$this->upload->id}";
            $validRecords = cache()->get($validRecordsCacheKey, []);

            // Persistir registros no banco de dados
            $recordsToInsert = [];
            foreach ($validRecords as $record) {
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

            // Limpar cache
            cache()->forget("upload_parsed_{$this->upload->id}");
            cache()->forget("upload_normalized_{$this->upload->id}");
            cache()->forget("upload_validations_{$this->upload->id}");
            cache()->forget("upload_valid_records_{$this->upload->id}");

            // Atualizar status para completed
            $this->upload->update([
                'status' => 'completed',
                'processing_completed_at' => now(),
            ]);

            Log::info("Upload finalizado com sucesso", [
                'upload_id' => $this->upload->id,
                'total_rows' => $this->upload->total_rows,
                'valid_rows' => $this->upload->valid_rows,
                'error_rows' => $this->upload->error_rows,
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
