<?php

namespace App\Domains\Parser\Parsers;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;

class ExcelParser
{
    protected array $errors = [];
    protected array $records = [];

    public function parse(string $filePath): array
    {
        $this->errors = [];
        $this->records = [];

        try {
            // Detectar tipo de arquivo
            $fileType = IOFactory::identify($filePath);
            $reader = IOFactory::createReader($fileType);
            $spreadsheet = $reader->load($filePath);

            // Obter primeira sheet
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            if (empty($rows)) {
                throw new \Exception("Arquivo Excel vazio");
            }

            // Primeira linha é header
            $headers = array_map('trim', $rows[0]);

            // Validar headers
            if (empty(array_filter($headers))) {
                throw new \Exception("Headers não encontrados");
            }

            // Processar linhas de dados
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                // Pular linhas vazias
                if (empty(array_filter($row))) {
                    continue;
                }

                // Mapear valores para headers
                $record = [];
                foreach ($headers as $index => $header) {
                    $value = $row[$index] ?? null;

                    // Converter datas do Excel
                    if ($value && is_numeric($value) && $value > 30000 && $value < 60000) {
                        try {
                            $value = Date::excelToDateTimeObject($value)->format('Y-m-d');
                        } catch (\Exception $e) {
                            // Manter valor original se falhar conversão
                        }
                    }

                    $record[trim($header)] = $value;
                }

                $this->records[] = $record;
            }

            Log::info("Excel parseado com sucesso", [
                'file_path' => $filePath,
                'records_count' => count($this->records),
                'errors_count' => count($this->errors),
            ]);

            return [
                'records' => $this->records,
                'errors' => $this->errors,
            ];

        } catch (\Exception $e) {
            Log::error("Erro ao parsear Excel", [
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
