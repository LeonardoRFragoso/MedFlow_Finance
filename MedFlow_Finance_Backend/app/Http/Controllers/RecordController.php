<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    public function index(Request $request)
    {
        $query = Record::where('clinic_id', auth()->user()->clinic_id);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('procedure_code')) {
            $query->where('procedure_code', $request->procedure_code);
        }

        if ($request->has('date_from')) {
            $query->whereDate('procedure_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('procedure_date', '<=', $request->date_to);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%$search%")
                    ->orWhere('patient_cpf', 'like', "%$search%")
                    ->orWhere('procedure_code', 'like', "%$search%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $records = $query->paginate($request->get('per_page', 15));

        return $this->respondPaginated($records, 'Registros listados com sucesso');
    }

    public function show($id)
    {
        $record = Record::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        return $this->respondSuccess([
            'record' => $record,
            'validations' => $record->validations()->get(),
            'errors' => $record->errors()->get(),
        ], 'Registro recuperado com sucesso');
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', Record::class);

        $record = Record::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected,disputed',
            'amount_paid' => 'nullable|numeric|min:0',
            'amount_pending' => 'nullable|numeric|min:0',
        ]);

        $record->update($validated);

        return $this->respondSuccess([
            'record' => $record,
        ], 'Registro atualizado com sucesso');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'query' => 'required|string|min:3',
            'type' => 'nullable|in:patient,procedure,provider',
        ]);

        $query = Record::where('clinic_id', auth()->user()->clinic_id);
        $searchTerm = $validated['query'];
        $type = $validated['type'] ?? null;

        if ($type === 'patient' || !$type) {
            $query->orWhere('patient_name', 'like', "%$searchTerm%")
                ->orWhere('patient_cpf', 'like', "%$searchTerm%");
        }

        if ($type === 'procedure' || !$type) {
            $query->orWhere('procedure_code', 'like', "%$searchTerm%")
                ->orWhere('procedure_name', 'like', "%$searchTerm%");
        }

        if ($type === 'provider' || !$type) {
            $query->orWhere('provider_name', 'like', "%$searchTerm%");
        }

        $results = $query->limit(20)->get();

        return $this->respondSuccess([
            'results' => $results,
            'count' => $results->count(),
        ], 'Busca realizada com sucesso');
    }
}
