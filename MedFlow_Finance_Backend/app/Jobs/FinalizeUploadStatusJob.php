<?php

namespace App\Jobs;

use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FinalizeUploadStatusJob implements ShouldQueue
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
            Log::info("Finalizando status do upload", [
                'upload_id' => $this->upload->id,
            ]);

            // Limpar cache restante
            cache()->forget("upload_validations_{$this->upload->id}");

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
            Log::error("Erro ao finalizar status do upload", [
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
