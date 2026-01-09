<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Record;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::where('clinic_id', auth()->user()->clinic_id);

        // Filtros
        if ($request->has('report_type')) {
            $query->where('report_type', $request->report_type);
        }

        if ($request->has('period_start')) {
            $query->whereDate('period_start', '>=', $request->period_start);
        }

        if ($request->has('period_end')) {
            $query->whereDate('period_end', '<=', $request->period_end);
        }

        $reports = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->respondPaginated($reports, 'Relatórios listados com sucesso');
    }

    public function show($id)
    {
        $report = Report::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        return $this->respondSuccess([
            'report' => $report,
            'exports' => $report->exports()->get(),
        ], 'Relatório recuperado com sucesso');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Report::class);

        $validated = $request->validate([
            'report_type' => 'required|in:summary,detailed,errors,validation,financial',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
        ]);

        $clinic = auth()->user()->clinic;
        $periodStart = $validated['period_start'];
        $periodEnd = $validated['period_end'];

        // Gerar dados do relatório
        $records = Record::where('clinic_id', $clinic->id)
            ->whereDate('procedure_date', '>=', $periodStart)
            ->whereDate('procedure_date', '<=', $periodEnd)
            ->get();

        $totalRecords = $records->count();
        $totalValid = $records->where('status', 'approved')->count();
        $totalErrors = $records->where('status', 'rejected')->count();
        $totalWarnings = $records->where('status', 'disputed')->count();
        $totalAmount = $records->sum('amount_billed');

        // Preparar conteúdo do relatório
        $content = $this->generateReportContent(
            $validated['report_type'],
            $records,
            $clinic
        );

        // Criar relatório
        $report = Report::create([
            'clinic_id' => $clinic->id,
            'user_id' => auth()->id(),
            'report_type' => $validated['report_type'],
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'total_records' => $totalRecords,
            'total_valid' => $totalValid,
            'total_errors' => $totalErrors,
            'total_warnings' => $totalWarnings,
            'total_amount' => $totalAmount,
            'content' => $content,
            'generated_at' => now(),
        ]);

        return $this->respondSuccess([
            'report' => $report,
        ], 'Relatório gerado com sucesso', 201);
    }

    public function exportCsv($id)
    {
        $this->authorize('export', Report::class);

        $report = Report::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        // TODO: Implementar exportação CSV
        // Disparar job ExportDataJob

        return $this->respondSuccess([
            'message' => 'Exportação iniciada',
            'report_id' => $report->id,
        ], 'Exportação em progresso');
    }

    public function exportPdf($id)
    {
        $this->authorize('export', Report::class);

        $report = Report::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        // TODO: Implementar exportação PDF
        // Disparar job ExportDataJob

        return $this->respondSuccess([
            'message' => 'Exportação iniciada',
            'report_id' => $report->id,
        ], 'Exportação em progresso');
    }

    private function generateReportContent($type, $records, $clinic)
    {
        return match ($type) {
            'summary' => $this->generateSummary($records, $clinic),
            'detailed' => $this->generateDetailed($records, $clinic),
            'errors' => $this->generateErrors($records, $clinic),
            'validation' => $this->generateValidation($records, $clinic),
            'financial' => $this->generateFinancial($records, $clinic),
            default => [],
        };
    }

    private function generateSummary($records, $clinic)
    {
        return [
            'clinic' => [
                'id' => $clinic->id,
                'name' => $clinic->name,
                'cnpj' => $clinic->cnpj,
            ],
            'statistics' => [
                'total_records' => $records->count(),
                'total_valid' => $records->where('status', 'approved')->count(),
                'total_errors' => $records->where('status', 'rejected')->count(),
                'total_warnings' => $records->where('status', 'disputed')->count(),
                'total_amount' => $records->sum('amount_billed'),
                'average_amount' => $records->avg('amount_billed'),
            ],
            'top_procedures' => $records->groupBy('procedure_code')
                ->map(function ($group) {
                    return [
                        'code' => $group->first()->procedure_code,
                        'name' => $group->first()->procedure_name,
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount_billed'),
                    ];
                })
                ->values()
                ->take(10),
        ];
    }

    private function generateDetailed($records, $clinic)
    {
        return [
            'clinic' => [
                'id' => $clinic->id,
                'name' => $clinic->name,
            ],
            'records' => $records->map(function ($record) {
                return [
                    'id' => $record->id,
                    'patient_name' => $record->patient_name,
                    'procedure_code' => $record->procedure_code,
                    'procedure_date' => $record->procedure_date,
                    'amount_billed' => $record->amount_billed,
                    'status' => $record->status,
                ];
            })->values(),
        ];
    }

    private function generateErrors($records, $clinic)
    {
        $errorRecords = $records->where('status', 'rejected');

        return [
            'clinic' => [
                'id' => $clinic->id,
                'name' => $clinic->name,
            ],
            'total_errors' => $errorRecords->count(),
            'errors' => $errorRecords->map(function ($record) {
                return [
                    'id' => $record->id,
                    'patient_name' => $record->patient_name,
                    'procedure_code' => $record->procedure_code,
                    'amount' => $record->amount_billed,
                    'error_count' => $record->errors()->count(),
                ];
            })->values(),
        ];
    }

    private function generateValidation($records, $clinic)
    {
        return [
            'clinic' => [
                'id' => $clinic->id,
                'name' => $clinic->name,
            ],
            'validation_summary' => [
                'total_validations' => $records->sum(function ($r) {
                    return $r->validations()->count();
                }),
                'errors' => $records->sum(function ($r) {
                    return $r->validations()->where('severity', 'error')->count();
                }),
                'warnings' => $records->sum(function ($r) {
                    return $r->validations()->where('severity', 'warning')->count();
                }),
            ],
        ];
    }

    private function generateFinancial($records, $clinic)
    {
        return [
            'clinic' => [
                'id' => $clinic->id,
                'name' => $clinic->name,
            ],
            'financial_summary' => [
                'total_billed' => $records->sum('amount_billed'),
                'total_paid' => $records->sum('amount_paid'),
                'total_pending' => $records->sum('amount_pending'),
                'average_per_procedure' => $records->avg('amount_billed'),
            ],
            'by_status' => [
                'approved' => [
                    'count' => $records->where('status', 'approved')->count(),
                    'total' => $records->where('status', 'approved')->sum('amount_billed'),
                ],
                'pending' => [
                    'count' => $records->where('status', 'pending')->count(),
                    'total' => $records->where('status', 'pending')->sum('amount_billed'),
                ],
                'rejected' => [
                    'count' => $records->where('status', 'rejected')->count(),
                    'total' => $records->where('status', 'rejected')->sum('amount_billed'),
                ],
                'disputed' => [
                    'count' => $records->where('status', 'disputed')->count(),
                    'total' => $records->where('status', 'disputed')->sum('amount_billed'),
                ],
            ],
        ];
    }
}
