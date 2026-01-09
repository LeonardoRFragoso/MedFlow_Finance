<?php

namespace App\Http\Controllers;

use App\Models\Clinic;
use App\Models\ClinicSetting;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    public function index(Request $request)
    {
        // Apenas admin pode listar todas as clínicas
        if (!auth()->user()->isAdmin()) {
            return $this->respondError('Unauthorized', 403);
        }

        $query = Clinic::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('cnpj', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        $clinics = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->respondPaginated($clinics, 'Clínicas listadas com sucesso');
    }

    public function show($id)
    {
        $clinic = Clinic::findOrFail($id);

        // Validar acesso
        if (auth()->user()->clinic_id !== $clinic->id && !auth()->user()->isAdmin()) {
            return $this->respondError('Unauthorized', 403);
        }

        return $this->respondSuccess([
            'clinic' => $clinic,
            'settings' => $clinic->settings,
            'user_count' => $clinic->users()->count(),
            'upload_count' => $clinic->uploads()->count(),
        ], 'Clínica recuperada com sucesso');
    }

    public function store(Request $request)
    {
        // Apenas admin pode criar clínicas
        if (!auth()->user()->isAdmin()) {
            return $this->respondError('Unauthorized', 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:clinics',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|size:2',
            'zip_code' => 'nullable|string|max:10',
            'billing_type' => 'nullable|in:private,public,mixed',
            'plan_type' => 'nullable|string',
        ]);

        $clinic = Clinic::create($validated);

        // Criar configurações padrão
        ClinicSetting::create([
            'clinic_id' => $clinic->id,
            'notification_email' => $validated['email'],
        ]);

        return $this->respondSuccess([
            'clinic' => $clinic,
        ], 'Clínica criada com sucesso', 201);
    }

    public function update(Request $request, $id)
    {
        $clinic = Clinic::findOrFail($id);

        // Validar acesso
        if (auth()->user()->clinic_id !== $clinic->id && !auth()->user()->isAdmin()) {
            return $this->respondError('Unauthorized', 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|size:2',
            'zip_code' => 'nullable|string|max:10',
            'billing_type' => 'nullable|in:private,public,mixed',
            'status' => 'sometimes|in:active,inactive,suspended',
        ]);

        $clinic->update($validated);

        return $this->respondSuccess([
            'clinic' => $clinic,
        ], 'Clínica atualizada com sucesso');
    }

    public function settings($id)
    {
        $clinic = Clinic::findOrFail($id);

        // Validar acesso
        if (auth()->user()->clinic_id !== $clinic->id && !auth()->user()->isAdmin()) {
            return $this->respondError('Unauthorized', 403);
        }

        $settings = $clinic->settings ?? ClinicSetting::create(['clinic_id' => $clinic->id]);

        return $this->respondSuccess([
            'settings' => $settings,
        ], 'Configurações recuperadas com sucesso');
    }

    public function updateSettings(Request $request, $id)
    {
        $clinic = Clinic::findOrFail($id);

        // Validar acesso
        if (auth()->user()->clinic_id !== $clinic->id && !auth()->user()->isAdmin()) {
            return $this->respondError('Unauthorized', 403);
        }

        $validated = $request->validate([
            'default_billing_type' => 'sometimes|in:private,public,mixed',
            'currency' => 'sometimes|string|size:3',
            'enable_glosa_detection' => 'sometimes|boolean',
            'enable_compliance_check' => 'sometimes|boolean',
            'data_retention_days' => 'sometimes|integer|min:1',
            'notify_on_upload_complete' => 'sometimes|boolean',
            'notify_on_error' => 'sometimes|boolean',
            'notification_email' => 'sometimes|email',
            'webhook_url' => 'nullable|url',
        ]);

        $settings = $clinic->settings ?? ClinicSetting::create(['clinic_id' => $clinic->id]);
        $settings->update($validated);

        return $this->respondSuccess([
            'settings' => $settings,
        ], 'Configurações atualizadas com sucesso');
    }
}
