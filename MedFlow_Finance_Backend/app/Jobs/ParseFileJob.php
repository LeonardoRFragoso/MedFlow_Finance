<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Models\Error;
use App\Domains\Parser\Services\FileParserService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ParseFileJob implements ShouldQueue
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
            Log::info("Iniciando parse do arquivo", [
                'upload_id' => $this->upload->id,
                'file_type' => $this->upload->file_type,
                'file_path' => $this->upload->file_path,
            ]);

            // Obter arquivo do storage
            $filePath = Storage::disk('local')->path($this->upload->file_path);

            if (!file_exists($filePath)) {
                throw new \Exception("Arquivo não encontrado: {$filePath}");
            }

            // Parsear arquivo
            $parserService = new FileParserService();
            $parsedData = $parserService->parse(
                $filePath,
                $this->upload->file_type
            );

            Log::info("Arquivo parseado com sucesso", [
                'upload_id' => $this->upload->id,
                'total_rows' => count($parsedData['records']),
                'errors' => count($parsedData['errors']),
            ]);

            // Armazenar dados parseados em cache para o próximo job
            $cacheKey = "upload_parsed_{$this->upload->id}";
            cache()->put($cacheKey, $parsedData, now()->addHours(24));

            // Registrar erros de parsing
            if (!empty($parsedData['errors'])) {
                foreach ($parsedData['errors'] as $error) {
                    Error::create([
                        'clinic_id' => $this->upload->clinic_id,
                        'upload_id' => $this->upload->id,
                        'error_type' => 'parse',
                        'error_code' => $error['code'] ?? 'PARSE_ERROR',
                        'error_message' => $error['message'],
                        'row_number' => $error['row'] ?? null,
                        'field_name' => $error['field'] ?? null,
                        'raw_value' => $error['value'] ?? null,
                        'status' => 'new',
                    ]);
                }
            }

            // Atualizar upload com contagem inicial
            $this->upload->update([
                'total_rows' => count($parsedData['records']),
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao parsear arquivo", [
                'upload_id' => $this->upload->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Registrar erro
            Error::create([
                'clinic_id' => $this->upload->clinic_id,
                'upload_id' => $this->upload->id,
                'error_type' => 'parse',
                'error_code' => 'PARSE_FAILED',
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'status' => 'new',
            ]);

            throw $e;
        }
    }
}
