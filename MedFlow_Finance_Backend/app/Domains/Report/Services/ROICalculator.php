<?php

namespace App\Domains\Report\Services;

use App\Models\Record;
use App\Models\Error;
use App\Models\Validation;
use Carbon\Carbon;

class ROICalculator
{
    protected $clinicId;
    protected $periodStart;
    protected $periodEnd;

    public function __construct($clinicId, $periodStart = null, $periodEnd = null)
    {
        $this->clinicId = $clinicId;
        $this->periodStart = $periodStart ?? now()->subMonth();
        $this->periodEnd = $periodEnd ?? now();
    }

    public function calculate(): array
    {
        $records = $this->getRecords();
        $errors = $this->getErrors();
        $validations = $this->getValidations();

        return [
            'period' => [
                'start' => $this->periodStart->format('Y-m-d'),
                'end' => $this->periodEnd->format('Y-m-d'),
                'days' => $this->periodStart->diffInDays($this->periodEnd),
            ],
            'volume' => $this->calculateVolume($records),
            'quality' => $this->calculateQuality($records, $errors),
            'glosa_risk' => $this->calculateGlosaRisk($validations),
            'financial_impact' => $this->calculateFinancialImpact($records, $errors),
            'time_saved' => $this->calculateTimeSaved($records),
            'recommendations' => $this->generateRecommendations($records, $errors, $validations),
        ];
    }

    protected function getRecords()
    {
        return Record::where('clinic_id', $this->clinicId)
            ->whereDate('procedure_date', '>=', $this->periodStart)
            ->whereDate('procedure_date', '<=', $this->periodEnd)
            ->get();
    }

    protected function getErrors()
    {
        return Error::where('clinic_id', $this->clinicId)
            ->whereDate('created_at', '>=', $this->periodStart)
            ->whereDate('created_at', '<=', $this->periodEnd)
            ->get();
    }

    protected function getValidations()
    {
        return Validation::where('clinic_id', $this->clinicId)
            ->whereDate('created_at', '>=', $this->periodStart)
            ->whereDate('created_at', '<=', $this->periodEnd)
            ->get();
    }

    protected function calculateVolume($records): array
    {
        $total = $records->count();
        $approved = $records->where('status', 'approved')->count();
        $rejected = $records->where('status', 'rejected')->count();
        $disputed = $records->where('status', 'disputed')->count();
        $pending = $records->where('status', 'pending')->count();

        return [
            'total_records' => $total,
            'approved' => $approved,
            'rejected' => $rejected,
            'disputed' => $disputed,
            'pending' => $pending,
            'success_rate' => $total > 0 ? round(($approved / $total) * 100, 2) : 0,
            'error_rate' => $total > 0 ? round((($rejected + $disputed) / $total) * 100, 2) : 0,
        ];
    }

    protected function calculateQuality($records, $errors): array
    {
        $totalRecords = $records->count();
        $recordsWithErrors = $records->filter(function ($record) use ($errors) {
            return $errors->where('record_id', $record->id)->count() > 0;
        })->count();

        $errorTypes = $errors->groupBy('error_type')->map(function ($group) {
            return $group->count();
        });

        return [
            'total_records' => $totalRecords,
            'records_with_errors' => $recordsWithErrors,
            'error_percentage' => $totalRecords > 0 ? round(($recordsWithErrors / $totalRecords) * 100, 2) : 0,
            'total_errors' => $errors->count(),
            'errors_by_type' => $errorTypes->toArray(),
            'critical_errors' => $errors->where('error_type', 'validation')->count(),
        ];
    }

    protected function calculateGlosaRisk($validations): array
    {
        $totalValidations = $validations->count();
        $glosaAlerts = $validations->where('rule_type', 'glosa')->count();
        $warnings = $validations->where('severity', 'warning')->count();
        $errors = $validations->where('severity', 'error')->count();

        return [
            'total_validations' => $totalValidations,
            'glosa_alerts' => $glosaAlerts,
            'glosa_percentage' => $totalValidations > 0 ? round(($glosaAlerts / $totalValidations) * 100, 2) : 0,
            'warnings' => $warnings,
            'errors' => $errors,
            'risk_level' => $this->calculateRiskLevel($glosaAlerts, $totalValidations),
        ];
    }

    protected function calculateFinancialImpact($records, $errors): array
    {
        $totalBilled = $records->sum('amount_billed');
        $totalApproved = $records->where('status', 'approved')->sum('amount_billed');
        $totalRejected = $records->where('status', 'rejected')->sum('amount_billed');
        $totalDisputed = $records->where('status', 'disputed')->sum('amount_billed');
        $totalPending = $records->where('status', 'pending')->sum('amount_billed');

        // Estimar valor em risco (registros com erros)
        $recordsWithErrors = $records->filter(function ($record) use ($errors) {
            return $errors->where('record_id', $record->id)->count() > 0;
        });
        $valueAtRisk = $recordsWithErrors->sum('amount_billed');

        // Estimar valor recuperável (com base em taxa histórica)
        $recoveryRate = 0.15; // 15% de recuperação típica
        $potentialRecovery = $valueAtRisk * $recoveryRate;

        return [
            'total_billed' => round($totalBilled, 2),
            'total_approved' => round($totalApproved, 2),
            'total_rejected' => round($totalRejected, 2),
            'total_disputed' => round($totalDisputed, 2),
            'total_pending' => round($totalPending, 2),
            'value_at_risk' => round($valueAtRisk, 2),
            'potential_recovery' => round($potentialRecovery, 2),
            'recovery_rate_percentage' => round($recoveryRate * 100, 2),
        ];
    }

    protected function calculateTimeSaved($records): array
    {
        // Tempo médio para processar um registro manualmente: 2 minutos
        $timePerRecordMinutes = 2;
        $totalRecords = $records->count();
        $totalMinutesSaved = $totalRecords * $timePerRecordMinutes;
        $totalHoursSaved = round($totalMinutesSaved / 60, 2);
        $totalDaysSaved = round($totalHoursSaved / 8, 2);

        // Custo horário médio de um administrativo: R$ 50
        $hourlyRate = 50;
        $moneySaved = $totalHoursSaved * $hourlyRate;

        return [
            'total_records_processed' => $totalRecords,
            'time_per_record_minutes' => $timePerRecordMinutes,
            'total_minutes_saved' => $totalMinutesSaved,
            'total_hours_saved' => $totalHoursSaved,
            'total_days_saved' => $totalDaysSaved,
            'hourly_rate' => $hourlyRate,
            'money_saved_labor' => round($moneySaved, 2),
        ];
    }

    protected function calculateRiskLevel($glosaAlerts, $totalValidations): string
    {
        if ($totalValidations === 0) {
            return 'low';
        }

        $percentage = ($glosaAlerts / $totalValidations) * 100;

        if ($percentage >= 30) {
            return 'high';
        } elseif ($percentage >= 15) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    protected function generateRecommendations($records, $errors, $validations): array
    {
        $recommendations = [];

        // Recomendação 1: Erros críticos
        $criticalErrors = $errors->where('error_type', 'validation')->count();
        if ($criticalErrors > 0) {
            $recommendations[] = [
                'priority' => 'high',
                'title' => 'Erros Críticos Detectados',
                'description' => "Foram encontrados {$criticalErrors} erros críticos que precisam de atenção imediata.",
                'action' => 'Revisar registros com erros de validação',
                'potential_impact' => 'Evitar rejeições de faturamento',
            ];
        }

        // Recomendação 2: Glosas em risco
        $glosaAlerts = $validations->where('rule_type', 'glosa')->count();
        if ($glosaAlerts > 0) {
            $recommendations[] = [
                'priority' => 'high',
                'title' => 'Alertas de Glosa',
                'description' => "Detectados {$glosaAlerts} registros com risco de glosa.",
                'action' => 'Revisar procedimentos com valores acima do esperado',
                'potential_impact' => 'Recuperar até 15% do valor em risco',
            ];
        }

        // Recomendação 3: Taxa de sucesso baixa
        $volume = $this->calculateVolume($records);
        if ($volume['success_rate'] < 80) {
            $recommendations[] = [
                'priority' => 'medium',
                'title' => 'Taxa de Sucesso Baixa',
                'description' => "Apenas {$volume['success_rate']}% dos registros foram aprovados.",
                'action' => 'Investigar causas de rejeição',
                'potential_impact' => 'Melhorar taxa de aprovação',
            ];
        }

        // Recomendação 4: Registros pendentes
        if ($volume['pending'] > 0) {
            $recommendations[] = [
                'priority' => 'medium',
                'title' => 'Registros Pendentes',
                'description' => "{$volume['pending']} registros ainda estão pendentes de processamento.",
                'action' => 'Completar processamento dos registros pendentes',
                'potential_impact' => 'Finalizar ciclo de faturamento',
            ];
        }

        return $recommendations;
    }
}
