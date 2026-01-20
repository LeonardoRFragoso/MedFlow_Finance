<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Http\Requests\StoreUploadRequest;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Upload::class);
        
        $query = Upload::with(['user', 'clinic'])
            ->where('clinic_id', auth()->user()->clinic_id);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('file_type')) {
            $query->where('file_type', $request->file_type);
        }

        if ($request->has('search')) {
            $query->where('original_filename', 'like', '%' . $request->search . '%');
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('billing_period_start', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $uploads = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return $this->respondPaginated($uploads, 'Uploads listados com sucesso');
    }

    public function show($id)
    {
        $upload = Upload::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);
        
        $this->authorize('view', $upload);

        return $this->respondSuccess([
            'upload' => $upload,
            'statistics' => [
                'total_rows' => $upload->total_rows,
                'valid_rows' => $upload->valid_rows,
                'error_rows' => $upload->error_rows,
                'warning_rows' => $upload->warning_rows,
                'success_rate' => $upload->getSuccessRate(),
            ],
            'errors' => $upload->errors()->limit(10)->get(),
        ], 'Upload recuperado com sucesso');
    }

    public function store(StoreUploadRequest $request)
    {
        $validated = $request->validated();

        $file = $request->file('file');
        $clinic = auth()->user()->clinic;

        // Validar limite de uploads
        $monthlyUploads = Upload::where('clinic_id', $clinic->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        if ($monthlyUploads >= $clinic->max_monthly_uploads) {
            return $this->respondError(
                'Limite de uploads mensais atingido',
                429
            );
        }

        // Validar tamanho do arquivo
        if ($file->getSize() > ($clinic->max_file_size_mb * 1024 * 1024)) {
            return $this->respondError(
                'Arquivo excede o tamanho máximo permitido',
                422
            );
        }

        // Gerar hash do arquivo
        $fileHash = hash_file('sha256', $file->getRealPath());

        // Verificar duplicação
        $existingUpload = Upload::where('clinic_id', $clinic->id)
            ->where('file_hash', $fileHash)
            ->where('created_at', '>', now()->subHours(24))
            ->first();

        if ($existingUpload) {
            return $this->respondError(
                'Arquivo duplicado foi enviado nas últimas 24 horas',
                422
            );
        }

        // Armazenar arquivo
        $path = $file->store(
            "uploads/{$clinic->id}/" . now()->format('Y/m'),
            'local'
        );

        // Criar registro de upload
        $upload = Upload::create([
            'clinic_id' => $clinic->id,
            'user_id' => auth()->id(),
            'original_filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size_bytes' => $file->getSize(),
            'file_type' => $this->getFileType($file->getClientOriginalExtension()),
            'file_hash' => $fileHash,
            'status' => 'pending',
            'billing_period_start' => $validated['billing_period_start'],
            'billing_period_end' => $validated['billing_period_end'],
            'description' => $validated['description'] ?? null,
            'tags' => $validated['tags'] ?? [],
        ]);

        // TODO: Disparar job de processamento
        // ProcessUploadJob::dispatch($upload);

        return $this->respondSuccess([
            'upload' => $upload,
        ], 'Upload criado com sucesso', 201);
    }

    public function destroy($id)
    {
        $upload = Upload::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);
        
        $this->authorize('delete', $upload);

        $upload->delete();

        return $this->respondSuccess(null, 'Upload deletado com sucesso');
    }

    public function status($id)
    {
        $upload = Upload::where('clinic_id', auth()->user()->clinic_id)
            ->findOrFail($id);

        return $this->respondSuccess([
            'id' => $upload->id,
            'status' => $upload->status,
            'progress' => $this->calculateProgress($upload),
            'statistics' => [
                'total_rows' => $upload->total_rows,
                'valid_rows' => $upload->valid_rows,
                'error_rows' => $upload->error_rows,
                'warning_rows' => $upload->warning_rows,
            ],
            'error_message' => $upload->processing_error_message,
        ], 'Status do upload');
    }

    private function getFileType($extension)
    {
        return match (strtolower($extension)) {
            'xlsx', 'xls' => 'excel',
            'csv' => 'csv',
            'xml' => 'xml',
            default => 'excel',
        };
    }

    private function calculateProgress(Upload $upload)
    {
        if ($upload->status === 'pending') {
            return 0;
        }

        if ($upload->status === 'processing') {
            if ($upload->total_rows === 0) {
                return 25;
            }

            $processed = $upload->valid_rows + $upload->error_rows + $upload->warning_rows;
            return min(99, (int)(($processed / $upload->total_rows) * 100));
        }

        if ($upload->status === 'completed') {
            return 100;
        }

        if ($upload->status === 'failed') {
            return 0;
        }

        return 0;
    }
}
