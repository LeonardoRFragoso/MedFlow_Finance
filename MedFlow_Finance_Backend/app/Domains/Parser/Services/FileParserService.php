<?php

namespace App\Domains\Parser\Services;

use App\Domains\Parser\Parsers\CsvParser;
use App\Domains\Parser\Parsers\ExcelParser;
use Illuminate\Support\Facades\Log;

class FileParserService
{
    protected array $parsers = [];

    public function __construct()
    {
        $this->parsers = [
            'csv' => CsvParser::class,
            'excel' => ExcelParser::class,
            'xlsx' => ExcelParser::class,
            'xls' => ExcelParser::class,
        ];
    }

    public function parse(string $filePath, string $fileType): array
    {
        Log::info("Iniciando parse de arquivo", [
            'file_path' => $filePath,
            'file_type' => $fileType,
        ]);

        if (!file_exists($filePath)) {
            throw new \Exception("Arquivo não encontrado: {$filePath}");
        }

        $parserClass = $this->parsers[$fileType] ?? null;

        if (!$parserClass) {
            throw new \Exception("Tipo de arquivo não suportado: {$fileType}");
        }

        $parser = new $parserClass();
        $result = $parser->parse($filePath);

        Log::info("Parse concluído", [
            'file_type' => $fileType,
            'records_count' => count($result['records']),
            'errors_count' => count($result['errors']),
        ]);

        return $result;
    }

    public function supports(string $fileType): bool
    {
        return isset($this->parsers[$fileType]);
    }

    public function getSupportedTypes(): array
    {
        return array_keys($this->parsers);
    }
}
