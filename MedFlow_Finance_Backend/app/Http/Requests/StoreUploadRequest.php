<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!$this->user()->hasPermission('uploads.create')) {
            return false;
        }

        if (!$this->user()->clinic->is_active) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:csv,xlsx,xls',
                'max:102400', // 100MB
            ],
            'billing_period_start' => [
                'required',
                'date',
                'before_or_equal:billing_period_end',
            ],
            'billing_period_end' => [
                'required',
                'date',
                'after_or_equal:billing_period_start',
                'before_or_equal:today',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Por favor, selecione um arquivo para upload.',
            'file.mimes' => 'Apenas arquivos CSV (.csv) ou Excel (.xlsx, .xls) são permitidos.',
            'file.max' => 'O arquivo não pode ser maior que 100MB.',
            'billing_period_start.required' => 'A data de início do período é obrigatória.',
            'billing_period_start.before_or_equal' => 'A data de início deve ser anterior ou igual à data final.',
            'billing_period_end.required' => 'A data final do período é obrigatória.',
            'billing_period_end.after_or_equal' => 'A data final deve ser posterior ou igual à data inicial.',
            'billing_period_end.before_or_equal' => 'A data final não pode ser no futuro.',
            'description.max' => 'A descrição não pode ter mais de 500 caracteres.',
        ];
    }
}
