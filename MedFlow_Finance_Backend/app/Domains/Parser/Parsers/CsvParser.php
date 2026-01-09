<?php

namespace App\Domains\Parser\Parsers;

use Illuminate\Support\Facades\Log;

class CsvParser
{
    protected array $errors = [];
    protected array $records = [];

    public function parse(string $filePath): array
    {
        $this->errors = [];
        $this->records = [];

        try {
            $file = fopen($filePath, 'r');
            if (!$file) {
                throw new \Exception("Não foi possível abrir o arquivo CSV");
            }

            $headers = null;
            $rowNumber = 0;

            while (($row = fgetcsv($file)) !== false) {
                $rowNumber++;

                // Primeira linha é header
                if ($rowNumber === 1) {
                    $headers = $row;
                    continue;
                }

                if (!$headers) {
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'code' => 'NO_HEADERS',
                        'message' => 'Arquivo CSV sem headers',
                    ];
                    continue;
                }

                // Pular linhas vazias
                if (empty(array_filter($row))) {
                    continue;
                }

                // Mapear valores para headers
                $record = [];
                foreach ($headers as $index => $header) {
                    $record[trim($header)] = $row[$index] ?? null;
                }

                $this->records[] = $record;
            }

            fclose($file);

            Log::info("CSV parseado com sucesso", [
                'file_path' => $filePath,
                'records_count' => count($this->records),
                'errors_count' => count($this->errors),
            ]);

            return [
                'records' => $this->records,
                'errors' => $this->errors,
            ];

        } catch (\Exception $e) {
            Log::error("Erro ao parsear CSV", [
                'file_path' => $filePath,
                'error' => $e->getMessage(),
            ]);

            $this->errors[] = [
                'code' => 'PARSE_ERROR',
                'message' => $e->getMessage(),
            ];

            return [
                'records' => $this->records,
                'errors' => $this->errors,
            ];
        }
    }
}
