<?php

namespace App\Domains\Validation\Rules;

abstract class Rule
{
    protected string $name;
    protected string $type;

    abstract public function validate(array $record): array;

    protected function createValidation(
        string $ruleName,
        string $ruleType,
        bool $isValid,
        string $severity,
        string $message,
        ?string $fieldName = null,
        ?string $expectedValue = null,
        ?string $actualValue = null,
        ?array $config = null
    ): array {
        return [
            'rule_name' => $ruleName,
            'rule_type' => $ruleType,
            'is_valid' => $isValid,
            'severity' => $severity,
            'message' => $message,
            'field_name' => $fieldName,
            'expected_value' => $expectedValue,
            'actual_value' => $actualValue,
            'config' => $config,
        ];
    }
}
