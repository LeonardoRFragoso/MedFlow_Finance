<?php

namespace App\Jobs;

use App\Models\Upload;
use App\Models\Record;
use App\Models\Error;
use App\Domains\Normalization\Services\DataNormalizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NormalizeRecordsJob implements ShouldQueue
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
            Log::info("Iniciando normalização de registros", [
                'upload_id' => $this->upload->id,
            ]);

            // Recuperar dados parseados do cache
            $cacheKey = "upload_parsed_{$this->upload->id}";
            $parsedData = cache()->get($cacheKey);

            if (!$parsedData) {
                throw new \Exception("Dados parseados não encontrados em cache");
            }

            $normalizer = new DataNormalizer();
            $normalizedRecords = [];
            $normalizationErrors = [];

            foreach ($parsedData['records'] as $index => $record) {
                try {
                    $normalized = $normalizer->normalize($record);
                    $normalizedRecords[] = $normalized;

                } catch (\Exception $e) {
                    Log::warning("Erro ao normalizar registro", [
                        'upload_id' => $this->upload->id,
                        'row' => $index + 1,
                        'error' => $e->getMessage(),
                    ]);

                    $normalizationErrors[] = [
                        'row' => $index + 1,
                        'error' => $e->getMessage(),
                        'record' => $record,
                    ];
                }
            }

            Log::info("Normalização concluída", [
                'upload_id' => $this->upload->id,
                'normalized_count' => count($normalizedRecords),
                'error_count' => count($normalizationErrors),
            ]);

            // Armazenar registros normalizados em cache
            $normalizedCacheKey = "upload_normalized_{$this->upload->id}";
            cache()->put($normalizedCacheKey, $normalizedRecords, now()->addHours(24));

            // Registrar erros de normalização
            foreach ($normalizationErrors as $error) {
                Error::create([
                    'clinic_id' => $this->upload->clinic_id,
                    'upload_id' => $this->upload->id,
                    'error_type' => 'processing',
                    'error_code' => 'NORMALIZATION_ERROR',
                    'error_message' => $error['error'],
                    'row_number' => $error['row'],
                    'raw_value' => json_encode($error['record']),
                    'status' => 'new',
                ]);
            }

            // Atualizar contagem de erros
            $this->upload->increment('error_rows', count($normalizationErrors));

        } catch (\Exception $e) {
            Log::error("Erro ao normalizar registros", [
                'upload_id' => $this->upload->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Error::create([
                'clinic_id' => $this->upload->clinic_id,
                'upload_id' => $this->upload->id,
                'error_type' => 'processing',
                'error_code' => 'NORMALIZATION_FAILED',
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'status' => 'new',
            ]);

            throw $e;
        }
    }
}
