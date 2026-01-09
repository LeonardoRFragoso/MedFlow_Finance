<?php

namespace App\Domains\Validation\Rules;

class GlosaDetectionRule extends Rule
{
    // Valores máximos por tipo de procedimento (simplificado para MVP)
    protected array $procedureMaxValues = [
        'CONS' => 500.00,      // Consulta
        'PROC' => 5000.00,     // Procedimento
        'EXAM' => 2000.00,     // Exame
        'CIRC' => 10000.00,    // Cirurgia
    ];

    public function validate(array $record): array
    {
        $validations = [];

        // Detectar valor acima do esperado
        if (!empty($record['procedure_code']) && !empty($record['amount_billed'])) {
            $maxValue = $this->getMaxValueForProcedure($record['procedure_code']);

            if ($maxValue && $record['amount_billed'] > $maxValue) {
                $validations[] = $this->createValidation(
                    'GlosaDetectionRule::ValueAboveExpected',
                    'glosa',
                    false,
                    'warning',
                    "Valor acima do esperado para este procedimento",
                    'amount_billed',
                    "<= {$maxValue}",
                    $record['amount_billed']
                );
            }
        }

        // Detectar valores muito altos (possível erro de digitação)
        if (!empty($record['amount_billed']) && $record['amount_billed'] > 50000) {
            $validations[] = $this->createValidation(
                'GlosaDetectionRule::SuspiciouslyHighValue',
                'glosa',
                false,
                'warning',
                "Valor muito alto - possível erro de digitação",
                'amount_billed',
                '<= 50000',
                $record['amount_billed']
            );
        }

        // Detectar valores muito baixos (possível erro de digitação)
        if (!empty($record['amount_billed']) && $record['amount_billed'] > 0 && $record['amount_billed'] < 0.01) {
            $validations[] = $this->createValidation(
                'GlosaDetectionRule::SuspiciouslyLowValue',
                'glosa',
                false,
                'warning',
                "Valor muito baixo - possível erro de digitação",
                'amount_billed',
                '>= 0.01',
                $record['amount_billed']
            );
        }

        // Detectar falta de autorização (se campo estiver vazio)
        if (empty($record['authorization_number'] ?? null)) {
            $validations[] = $this->createValidation(
                'GlosaDetectionRule::MissingAuthorization',
                'glosa',
                false,
                'warning',
                "Número de autorização não informado",
                'authorization_number',
                'não vazio',
                'vazio'
            );
        }

        // Detectar falta de informações do convênio
        if (empty($record['insurance_name'] ?? null) || empty($record['insurance_id'] ?? null)) {
            $validations[] = $this->createValidation(
                'GlosaDetectionRule::MissingInsuranceInfo',
                'glosa',
                false,
                'warning',
                "Informações do convênio incompletas",
                'insurance_name',
                'não vazio',
                'vazio'
            );
        }

        // Se nenhuma validação foi adicionada, adicionar validação de sucesso
        if (empty($validations)) {
            $validations[] = $this->createValidation(
                'GlosaDetectionRule::Success',
                'glosa',
                true,
                'info',
                "Nenhuma possível glosa detectada"
            );
        }

        return $validations;
    }

    protected function getMaxValueForProcedure(string $procedureCode): ?float
    {
        // Extrair prefixo do código
        $prefix = substr($procedureCode, 0, 4);

        return $this->procedureMaxValues[$prefix] ?? null;
    }

    public function addProcedureMaxValue(string $procedureCode, float $maxValue): self
    {
        $this->procedureMaxValues[$procedureCode] = $maxValue;
        return $this;
    }
}
