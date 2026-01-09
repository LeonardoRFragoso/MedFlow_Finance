<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Domains\Upload\Services\UploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class ProcessUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 3;
    public $backoff = [10, 30, 60];

    public function __construct(
        public Upload $upload
    ) {}

    public function handle(): void
    {
        try {
            Log::info("Iniciando processamento do upload", [
                'upload_id' => $this->upload->id,
                'clinic_id' => $this->upload->clinic_id,
                'file_type' => $this->upload->file_type,
            ]);

            // Atualizar status para processing
            $this->upload->update([
                'status' => 'processing',
                'processing_started_at' => now(),
            ]);

            // Disparar cadeia de jobs
            Bus::chain([
                new ParseFileJob($this->upload),
                new NormalizeRecordsJob($this->upload),
                new ValidateRecordsJob($this->upload),
                new FinalizeUploadJob($this->upload),
            ])->dispatch();

            Log::info("Cadeia de jobs disparada com sucesso", [
                'upload_id' => $this->upload->id,
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao processar upload", [
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

    public function failed(\Throwable $exception): void
    {
        Log::error("Job ProcessUploadJob falhou permanentemente", [
            'upload_id' => $this->upload->id,
            'error' => $exception->getMessage(),
        ]);

        $this->upload->update([
            'status' => 'failed',
            'processing_error_message' => 'Falha permanente no processamento',
            'processing_completed_at' => now(),
        ]);
    }
}
