<?php

namespace App\Domains\Validation\Rules;

class BusinessLogicRule extends Rule
{
    public function validate(array $record): array
    {
        $validations = [];

        // Validar que amount_paid não excede amount_billed
        if (!empty($record['amount_billed']) && !empty($record['amount_paid'])) {
            if ($record['amount_paid'] > $record['amount_billed']) {
                $validations[] = $this->createValidation(
                    'BusinessLogicRule::AmountPaidExceedsBilled',
                    'business',
                    false,
                    'error',
                    "Valor pago não pode exceder valor faturado",
                    'amount_paid',
                    '<= amount_billed',
                    $record['amount_paid'] . ' > ' . $record['amount_billed']
                );
            }
        }

        // Validar que amount_pending = amount_billed - amount_paid
        if (!empty($record['amount_billed']) && !empty($record['amount_paid']) && !empty($record['amount_pending'])) {
            $expectedPending = $record['amount_billed'] - $record['amount_paid'];
            if (abs($record['amount_pending'] - $expectedPending) > 0.01) {
                $validations[] = $this->createValidation(
                    'BusinessLogicRule::InconsistentAmounts',
                    'business',
                    false,
                    'warning',
                    "Valor pendente não corresponde ao cálculo (faturado - pago)",
                    'amount_pending',
                    $expectedPending,
                    $record['amount_pending']
                );
            }
        }

        // Validar que procedure_date não é no futuro
        if (!empty($record['procedure_date'])) {
            $procedureDate = strtotime($record['procedure_date']);
            $today = strtotime(date('Y-m-d'));

            if ($procedureDate > $today) {
                $validations[] = $this->createValidation(
                    'BusinessLogicRule::FutureDate',
                    'business',
                    false,
                    'warning',
                    "Data do procedimento não pode ser no futuro",
                    'procedure_date',
                    '<= hoje',
                    $record['procedure_date']
                );
            }
        }

        // Validar que procedure_date não é muito antiga (mais de 2 anos)
        if (!empty($record['procedure_date'])) {
            $procedureDate = strtotime($record['procedure_date']);
            $twoYearsAgo = strtotime('-2 years');

            if ($procedureDate < $twoYearsAgo) {
                $validations[] = $this->createValidation(
                    'BusinessLogicRule::VeryOldDate',
                    'business',
                    false,
                    'warning',
                    "Data do procedimento é muito antiga (mais de 2 anos)",
                    'procedure_date',
                    '>= 2 anos atrás',
                    $record['procedure_date']
                );
            }
        }

        // Validar que procedure_code não está vazio
        if (empty($record['procedure_code'])) {
            $validations[] = $this->createValidation(
                'BusinessLogicRule::EmptyProcedureCode',
                'business',
                false,
                'error',
                "Código do procedimento não pode estar vazio",
                'procedure_code',
                'não vazio',
                'vazio'
            );
        }

        // Se nenhuma validação foi adicionada, adicionar validação de sucesso
        if (empty($validations)) {
            $validations[] = $this->createValidation(
                'BusinessLogicRule::Success',
                'business',
                true,
                'info',
                "Todas as regras de negócio foram validadas com sucesso"
            );
        }

        return $validations;
    }
}
