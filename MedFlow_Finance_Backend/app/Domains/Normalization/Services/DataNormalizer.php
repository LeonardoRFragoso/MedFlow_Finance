<?php

namespace App\Domains\Normalization\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DataNormalizer
{
    protected array $normalizers = [];

    public function __construct()
    {
        $this->normalizers = [
            'date' => [$this, 'normalizeDate'],
            'money' => [$this, 'normalizeMoney'],
            'cpf' => [$this, 'normalizeCpf'],
            'cnpj' => [$this, 'normalizeCnpj'],
            'string' => [$this, 'normalizeString'],
            'code' => [$this, 'normalizeCode'],
        ];
    }

    public function normalize(array $record): array
    {
        $normalized = [];

        // Campos obrigatórios
        $requiredFields = [
            'procedure_code',
            'procedure_date',
            'amount_billed',
        ];

        // Validar campos obrigatórios
        foreach ($requiredFields as $field) {
            if (empty($record[$field] ?? null)) {
                throw new \Exception("Campo obrigatório vazio: {$field}");
            }
        }

        // Normalizar cada campo
        $normalized['patient_name'] = $this->normalizeString($record['patient_name'] ?? null);
        $normalized['patient_cpf'] = $this->normalizeCpf($record['patient_cpf'] ?? null);
        $normalized['patient_id'] = $this->normalizeString($record['patient_id'] ?? null);

        $normalized['procedure_code'] = $this->normalizeCode($record['procedure_code']);
        $normalized['procedure_name'] = $this->normalizeString($record['procedure_name'] ?? null);
        $normalized['procedure_date'] = $this->normalizeDate($record['procedure_date']);

        $normalized['amount_billed'] = $this->normalizeMoney($record['amount_billed']);
        $normalized['amount_paid'] = $this->normalizeMoney($record['amount_paid'] ?? 0);
        $normalized['amount_pending'] = $this->normalizeMoney($record['amount_pending'] ?? 0);

        $normalized['provider_name'] = $this->normalizeString($record['provider_name'] ?? null);
        $normalized['provider_id'] = $this->normalizeString($record['provider_id'] ?? null);
        $normalized['insurance_name'] = $this->normalizeString($record['insurance_name'] ?? null);
        $normalized['insurance_id'] = $this->normalizeString($record['insurance_id'] ?? null);
        $normalized['authorization_number'] = $this->normalizeString($record['authorization_number'] ?? null);

        return $normalized;
    }

    protected function normalizeDate($value): string
    {
        if (!$value) {
            throw new \Exception("Data inválida: vazia");
        }

        try {
            // Se já é uma data válida
            if ($value instanceof \DateTime) {
                return $value->format('Y-m-d');
            }

            // Tentar parsear como string
            $date = Carbon::createFromFormat('Y-m-d', $value);
            return $date->format('Y-m-d');

        } catch (\Exception $e) {
            try {
                // Tentar formato DD/MM/YYYY
                $date = Carbon::createFromFormat('d/m/Y', $value);
                return $date->format('Y-m-d');
            } catch (\Exception $e2) {
                try {
                    // Tentar formato DD-MM-YYYY
                    $date = Carbon::createFromFormat('d-m-Y', $value);
                    return $date->format('Y-m-d');
                } catch (\Exception $e3) {
                    throw new \Exception("Data em formato inválido: {$value}");
                }
            }
        }
    }

    protected function normalizeMoney($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }

        // Remover caracteres não numéricos, exceto ponto e vírgula
        $value = preg_replace('/[^\d.,]/', '', (string)$value);

        // Detectar separador decimal
        if (strpos($value, ',') !== false && strpos($value, '.') !== false) {
            // Ambos presentes: último é decimal
            if (strrpos($value, ',') > strrpos($value, '.')) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        } elseif (strpos($value, ',') !== false) {
            // Apenas vírgula
            $value = str_replace(',', '.', $value);
        }

        $float = (float)$value;

        if ($float < 0) {
            throw new \Exception("Valor monetário não pode ser negativo: {$value}");
        }

        return round($float, 2);
    }

    protected function normalizeCpf(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        // Remover caracteres não numéricos
        $cpf = preg_replace('/\D/', '', $value);

        // Validar tamanho
        if (strlen($cpf) !== 11) {
            throw new \Exception("CPF com tamanho inválido: {$value}");
        }

        // Formatar como XXX.XXX.XXX-XX
        return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
    }

    protected function normalizeCnpj(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        // Remover caracteres não numéricos
        $cnpj = preg_replace('/\D/', '', $value);

        // Validar tamanho
        if (strlen($cnpj) !== 14) {
            throw new \Exception("CNPJ com tamanho inválido: {$value}");
        }

        // Formatar como XX.XXX.XXX/XXXX-XX
        return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    }

    protected function normalizeString(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        // Trim e remover espaços múltiplos
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value);

        return $value ?: null;
    }

    protected function normalizeCode(?string $value): string
    {
        if (!$value) {
            throw new \Exception("Código vazio");
        }

        // Remover espaços e converter para maiúsculas
        $code = strtoupper(trim($value));

        if (strlen($code) === 0) {
            throw new \Exception("Código inválido");
        }

        return $code;
    }
}
