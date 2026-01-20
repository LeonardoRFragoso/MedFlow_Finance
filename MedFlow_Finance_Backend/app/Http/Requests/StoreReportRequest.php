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
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'type' => [
                'required',
                'string',
                'in:monthly,quarterly,annual,custom',
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
            'filters' => [
                'nullable',
                'array',
            ],
            'filters.status' => [
                'nullable',
                'array',
            ],
            'filters.status.*' => [
                'string',
                'in:approved,rejected,disputed,pending',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título do relatório é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'type.required' => 'O tipo de relatório é obrigatório.',
            'type.in' => 'Tipo de relatório inválido.',
            'period_start.required' => 'A data de início do período é obrigatória.',
            'period_end.required' => 'A data final do período é obrigatória.',
        ];
    }
}
