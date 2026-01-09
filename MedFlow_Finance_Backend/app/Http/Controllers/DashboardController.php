<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Upload;
use App\Models\Error;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function summary(Request $request)
    {
        $clinic = auth()->user()->clinic;
        $periodStart = $request->get('period_start', now()->subMonth());
        $periodEnd = $request->get('period_end', now());

        $records = Record::where('clinic_id', $clinic->id)
            ->whereDate('procedure_date', '>=', $periodStart)
            ->whereDate('procedure_date', '<=', $periodEnd)
            ->get();

        $uploads = Upload::where('clinic_id', $clinic->id)
            ->whereDate('created_at', '>=', $periodStart)
            ->whereDate('created_at', '<=', $periodEnd)
            ->get();

        $errors = Error::where('clinic_id', $clinic->id)
            ->whereDate('created_at', '>=', $periodStart)
            ->whereDate('created_at', '<=', $periodEnd)
            ->get();

        return $this->respondSuccess([
            'period' => [
                'start' => $periodStart,
                'end' => $periodEnd,
            ],
            'financial' => [
                'total_billed' => $records->sum('amount_billed'),
                'total_paid' => $records->sum('amount_paid'),
                'total_pending' => $records->sum('amount_pending'),
                'average_per_procedure' => $records->avg('amount_billed'),
            ],
            'records' => [
                'total' => $records->count(),
                'approved' => $records->where('status', 'approved')->count(),
                'pending' => $records->where('status', 'pending')->count(),
                'rejected' => $records->where('status', 'rejected')->count(),
                'disputed' => $records->where('status', 'disputed')->count(),
            ],
            'uploads' => [
                'total' => $uploads->count(),
                'completed' => $uploads->where('status', 'completed')->count(),
                'pending' => $uploads->where('status', 'pending')->count(),
                'processing' => $uploads->where('status', 'processing')->count(),
                'failed' => $uploads->where('status', 'failed')->count(),
            ],
            'errors' => [
                'total' => $errors->count(),
                'new' => $errors->where('status', 'new')->count(),
                'acknowledged' => $errors->where('status', 'acknowledged')->count(),
                'resolved' => $errors->where('status', 'resolved')->count(),
            ],
            'success_rate' => $records->count() > 0
                ? round(($records->where('status', 'approved')->count() / $records->count()) * 100, 2)
                : 0,
        ], 'Resumo do dashboard');
    }

    public function metrics(Request $request)
    {
        $clinic = auth()->user()->clinic;
        $periodStart = $request->get('period_start', now()->subMonth());
        $periodEnd = $request->get('period_end', now());

        $records = Record::where('clinic_id', $clinic->id)
            ->whereDate('procedure_date', '>=', $periodStart)
            ->whereDate('procedure_date', '<=', $periodEnd)
            ->get();

        return $this->respondSuccess([
            'top_procedures' => $records->groupBy('procedure_code')
                ->map(function ($group) {
                    return [
                        'code' => $group->first()->procedure_code,
                        'name' => $group->first()->procedure_name,
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount_billed'),
                        'average_amount' => $group->avg('amount_billed'),
                    ];
                })
                ->values()
                ->sortByDesc('total_amount')
                ->take(10),

            'status_distribution' => [
                'approved' => $records->where('status', 'approved')->count(),
                'pending' => $records->where('status', 'pending')->count(),
                'rejected' => $records->where('status', 'rejected')->count(),
                'disputed' => $records->where('status', 'disputed')->count(),
            ],

            'daily_trend' => $records->groupBy(function ($record) {
                return $record->procedure_date->format('Y-m-d');
            })
                ->map(function ($group) {
                    return [
                        'date' => $group->first()->procedure_date->format('Y-m-d'),
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount_billed'),
                    ];
                })
                ->values(),

            'top_providers' => $records->groupBy('provider_name')
                ->map(function ($group) {
                    return [
                        'name' => $group->first()->provider_name,
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount_billed'),
                    ];
                })
                ->values()
                ->sortByDesc('total_amount')
                ->take(10),

            'top_insurances' => $records->groupBy('insurance_name')
                ->map(function ($group) {
                    return [
                        'name' => $group->first()->insurance_name,
                        'count' => $group->count(),
                        'total_amount' => $group->sum('amount_billed'),
                    ];
                })
                ->values()
                ->sortByDesc('total_amount')
                ->take(10),
        ], 'Métricas do dashboard');
    }
}
