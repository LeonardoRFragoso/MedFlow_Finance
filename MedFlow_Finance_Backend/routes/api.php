<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ROIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas públicas com rate limit de autenticação
Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
});

// Rotas protegidas com rate limit padrão
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Autenticação
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Clínicas
    Route::apiResource('clinics', ClinicController::class);
    Route::get('/clinics/{id}/settings', [ClinicController::class, 'settings']);
    Route::put('/clinics/{id}/settings', [ClinicController::class, 'updateSettings']);

    // Registros
    Route::apiResource('records', RecordController::class)->only(['index', 'show', 'update']);
    Route::get('/records/search', [RecordController::class, 'search']);

    // Usuários
    Route::apiResource('users', UserController::class);
    Route::post('/users/{id}/roles', [UserController::class, 'assignRole']);
    Route::delete('/users/{id}/roles', [UserController::class, 'removeRole']);

    // Dashboard
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);

    // ROI - Métricas e Relatórios Executivos
    Route::prefix('roi')->group(function () {
        Route::get('/summary', [ROIController::class, 'summary'])
            ->name('roi.summary');
        
        Route::get('/executive-report', [ROIController::class, 'executiveReport'])
            ->name('roi.executive-report');
    });

    // Uploads - Leitura
    Route::get('/uploads', [UploadController::class, 'index']);
    Route::get('/uploads/{id}', [UploadController::class, 'show']);
    Route::get('/uploads/{id}/status', [UploadController::class, 'status']);
    Route::delete('/uploads/{id}', [UploadController::class, 'destroy']);

    // Relatórios - Leitura
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/{id}', [ReportController::class, 'show']);
    Route::delete('/reports/{id}', [ReportController::class, 'destroy']);
});

// Uploads com rate limit restritivo
Route::middleware(['auth:sanctum', 'throttle:uploads'])->group(function () {
    Route::post('/uploads', [UploadController::class, 'store']);
    Route::put('/uploads/{id}', [UploadController::class, 'update']);
});

// Geração de reports com rate limit por hora
Route::middleware(['auth:sanctum', 'throttle:reports'])->group(function () {
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/reports/{id}/export/csv', [ReportController::class, 'exportCsv']);
    Route::get('/reports/{id}/export/pdf', [ReportController::class, 'exportPdf']);
});
