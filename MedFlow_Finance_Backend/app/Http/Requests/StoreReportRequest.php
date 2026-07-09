<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('reports.create');
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                'in:summary,detailed,errors,validation,financial',
            ],
            'period_start' => [
                'required',
                'date',
                'before_or_equal:period_end',
            ],
            'period_end' => [
                'required',
                'date',
                'after_or_equal:period_start',
                'before_or_equal:today',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'O tipo de relatório é obrigatório.',
            'type.in' => 'Tipo de relatório inválido. Tipos aceitos: summary, detailed, errors, validation, financial.',
            'period_start.required' => 'A data de início do período é obrigatória.',
            'period_start.date' => 'A data de início deve ser uma data válida.',
            'period_start.before_or_equal' => 'A data de início não pode ser posterior à data final.',
            'period_end.required' => 'A data final do período é obrigatória.',
            'period_end.date' => 'A data final deve ser uma data válida.',
            'period_end.after_or_equal' => 'A data final não pode ser anterior à data de início.',
            'period_end.before_or_equal' => 'A data final não pode ser no futuro.',
        ];
    }
}
