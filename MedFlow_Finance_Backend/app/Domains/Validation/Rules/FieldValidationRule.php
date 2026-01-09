<?php

namespace App\Domains\Validation\Rules;

class FieldValidationRule extends Rule
{
    public function validate(array $record): array
    {
        $validations = [];

        // Validar campos obrigatórios
        $requiredFields = [
            'procedure_code' => 'Código do procedimento',
            'procedure_date' => 'Data do procedimento',
            'amount_billed' => 'Valor faturado',
        ];

        foreach ($requiredFields as $field => $label) {
            if (empty($record[$field] ?? null)) {
                $validations[] = $this->createValidation(
                    'FieldValidationRule::RequiredField',
                    'field',
                    false,
                    'error',
                    "Campo obrigatório vazio: {$label}",
                    $field,
                    'não vazio',
                    'vazio'
                );
            }
        }

        // Validar tipos de dados
        if (!empty($record['amount_billed'])) {
            if (!is_numeric($record['amount_billed'])) {
                $validations[] = $this->createValidation(
                    'FieldValidationRule::InvalidType',
                    'field',
                    false,
                    'error',
                    "Valor faturado deve ser numérico",
                    'amount_billed',
                    'numérico',
                    gettype($record['amount_billed'])
                );
            } elseif ($record['amount_billed'] < 0) {
                $validations[] = $this->createValidation(
                    'FieldValidationRule::NegativeValue',
                    'field',
                    false,
                    'error',
                    "Valor faturado não pode ser negativo",
                    'amount_billed',
                    '>= 0',
                    $record['amount_billed']
                );
            }
        }

        // Validar data
        if (!empty($record['procedure_date'])) {
            if (!$this->isValidDate($record['procedure_date'])) {
                $validations[] = $this->createValidation(
                    'FieldValidationRule::InvalidDate',
                    'field',
                    false,
                    'error',
                    "Data do procedimento em formato inválido",
                    'procedure_date',
                    'YYYY-MM-DD',
                    $record['procedure_date']
                );
            }
        }

        // Validar CPF se presente
        if (!empty($record['patient_cpf'])) {
            if (!$this->isValidCpfFormat($record['patient_cpf'])) {
                $validations[] = $this->createValidation(
                    'FieldValidationRule::InvalidCpf',
                    'field',
                    false,
                    'warning',
                    "CPF em formato inválido",
                    'patient_cpf',
                    'XXX.XXX.XXX-XX',
                    $record['patient_cpf']
                );
            }
        }

        // Se nenhuma validação foi adicionada, adicionar validação de sucesso
        if (empty($validations)) {
            $validations[] = $this->createValidation(
                'FieldValidationRule::Success',
                'field',
                true,
                'info',
                "Todos os campos obrigatórios estão preenchidos"
            );
        }

        return $validations;
    }

    protected function isValidDate(string $date): bool
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) && strtotime($date) !== false;
    }

    protected function isValidCpfFormat(string $cpf): bool
    {
        return preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf);
    }
}
