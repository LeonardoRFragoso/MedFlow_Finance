<?php

namespace App\Domains\Validation\Services;

use App\Domains\Validation\Rules\Rule;
use App\Domains\Validation\Rules\FieldValidationRule;
use App\Domains\Validation\Rules\BusinessLogicRule;
use App\Domains\Validation\Rules\GlosaDetectionRule;
use Illuminate\Support\Facades\Log;

class ValidationEngine
{
    protected array $rules = [];

    public function __construct()
    {
        $this->initializeRules();
    }

    protected function initializeRules(): void
    {
        $this->rules = [
            new FieldValidationRule(),
            new BusinessLogicRule(),
            new GlosaDetectionRule(),
        ];
    }

    public function validate(array $record): array
    {
        $validations = [];
        $hasErrors = false;

        foreach ($this->rules as $rule) {
            try {
                $result = $rule->validate($record);

                if (is_array($result)) {
                    $validations = array_merge($validations, $result);

                    // Verificar se há erros críticos
                    foreach ($result as $validation) {
                        if ($validation['severity'] === 'error' && !$validation['is_valid']) {
                            $hasErrors = true;
                        }
                    }
                }

            } catch (\Exception $e) {
                Log::warning("Erro ao executar regra de validação", [
                    'rule' => get_class($rule),
                    'error' => $e->getMessage(),
                    'record' => $record,
                ]);

                $validations[] = [
                    'rule_name' => get_class($rule),
                    'rule_type' => 'system',
                    'is_valid' => false,
                    'severity' => 'error',
                    'message' => 'Erro ao executar validação: ' . $e->getMessage(),
                ];

                $hasErrors = true;
            }
        }

        return [
            'is_valid' => !$hasErrors,
            'validations' => $validations,
        ];
    }

    public function addRule(Rule $rule): self
    {
        $this->rules[] = $rule;
        return $this;
    }

    public function removeRule(string $ruleClass): self
    {
        $this->rules = array_filter($this->rules, function ($rule) use ($ruleClass) {
            return !($rule instanceof $ruleClass);
        });

        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }
}
