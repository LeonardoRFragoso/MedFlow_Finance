<?php

namespace App\Http\Controllers;

use App\Domains\Report\Services\ROICalculator;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ROIController extends Controller
{
    public function summary(Request $request)
    {
        $clinic = auth()->user()->clinic;
        
        // Converter strings para Carbon se necessário
        $periodStart = $request->has('period_start') 
            ? Carbon::parse($request->get('period_start'))
            : now()->subDays(30);
            
        $periodEnd = $request->has('period_end')
            ? Carbon::parse($request->get('period_end'))
            : now();

        // Validar período
        if ($periodEnd->isBefore($periodStart)) {
            return $this->respondError('A data final não pode ser anterior à data inicial', 422);
        }

        $calculator = new ROICalculator($clinic->id, $periodStart, $periodEnd);
        $roi = $calculator->calculate();

        return $this->respondSuccess($roi, 'Resumo de ROI calculado com sucesso');
    }

    public function executiveReport(Request $request)
    {
        $clinic = auth()->user()->clinic;
        
        // Converter strings para Carbon se necessário
        $periodStart = $request->has('period_start')
            ? Carbon::parse($request->get('period_start'))
            : now()->subDays(30);
            
        $periodEnd = $request->has('period_end')
            ? Carbon::parse($request->get('period_end'))
            : now();

        // Validar período
        if ($periodEnd->isBefore($periodStart)) {
            return $this->respondError('A data final não pode ser anterior à data inicial', 422);
        }

        $calculator = new ROICalculator($clinic->id, $periodStart, $periodEnd);
        $roi = $calculator->calculate();

        // Preparar relatório executivo
        $report = [
            'clinic_name' => $clinic->name,
            'period' => $roi['period'],
            'executive_summary' => [
                'title' => 'Resumo Executivo de Faturamento',
                'key_metrics' => [
                    [
                        'label' => 'Total Faturado',
                        'value' => $roi['financial_impact']['total_billed'],
                        'format' => 'currency',
                        'icon' => '💰',
                    ],
                    [
                        'label' => 'Taxa de Sucesso',
                        'value' => $roi['volume']['success_rate'],
                        'format' => 'percentage',
                        'icon' => '✅',
                    ],
                    [
                        'label' => 'Valor em Risco',
                        'value' => $roi['financial_impact']['value_at_risk'],
                        'format' => 'currency',
                        'icon' => '⚠️',
                    ],
                    [
                        'label' => 'Potencial de Recuperação',
                        'value' => $roi['financial_impact']['potential_recovery'],
                        'format' => 'currency',
                        'icon' => '📈',
                    ],
                    [
                        'label' => 'Tempo Economizado',
                        'value' => $roi['time_saved']['total_hours_saved'],
                        'format' => 'hours',
                        'icon' => '⏱️',
                    ],
                    [
                        'label' => 'Economia em Mão de Obra',
                        'value' => $roi['time_saved']['money_saved_labor'],
                        'format' => 'currency',
                        'icon' => '💵',
                    ],
                ],
            ],
            'volume_analysis' => [
                'title' => 'Análise de Volume',
                'total_records' => $roi['volume']['total_records'],
                'approved' => $roi['volume']['approved'],
                'rejected' => $roi['volume']['rejected'],
                'disputed' => $roi['volume']['disputed'],
                'pending' => $roi['volume']['pending'],
                'success_rate' => $roi['volume']['success_rate'],
                'error_rate' => $roi['volume']['error_rate'],
            ],
            'quality_analysis' => [
                'title' => 'Análise de Qualidade',
                'error_percentage' => $roi['quality']['error_percentage'],
                'total_errors' => $roi['quality']['total_errors'],
                'critical_errors' => $roi['quality']['critical_errors'],
                'errors_by_type' => $roi['quality']['errors_by_type'],
            ],
            'glosa_risk_analysis' => [
                'title' => 'Análise de Risco de Glosa',
                'glosa_percentage' => $roi['glosa_risk']['glosa_percentage'],
                'glosa_alerts' => $roi['glosa_risk']['glosa_alerts'],
                'risk_level' => $roi['glosa_risk']['risk_level'],
                'risk_level_label' => $this->getRiskLevelLabel($roi['glosa_risk']['risk_level']),
            ],
            'financial_impact' => $roi['financial_impact'],
            'recommendations' => $roi['recommendations'],
            'next_steps' => [
                [
                    'priority' => 1,
                    'action' => 'Revisar registros com erro',
                    'expected_result' => 'Aumentar taxa de aprovação',
                ],
                [
                    'priority' => 2,
                    'action' => 'Investigar alertas de glosa',
                    'expected_result' => 'Recuperar até R$ ' . number_format($roi['financial_impact']['potential_recovery'], 2, ',', '.'),
                ],
                [
                    'priority' => 3,
                    'action' => 'Processar registros pendentes',
                    'expected_result' => 'Completar ciclo de faturamento',
                ],
            ],
        ];

        return $this->respondSuccess($report, 'Relatório executivo gerado com sucesso');
    }

    protected function getRiskLevelLabel($level): string
    {
        $labels = [
            'low' => 'Baixo Risco',
            'medium' => 'Risco Médio',
            'high' => 'Alto Risco',
        ];

        return $labels[$level] ?? 'Desconhecido';
    }
}
