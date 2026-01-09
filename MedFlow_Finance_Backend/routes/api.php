<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClinicController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Rotas de autenticação (sem proteção)
Route::post('/auth/login', [AuthController::class, 'login']);

// Rotas protegidas por Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Autenticação
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Clínicas
    Route::apiResource('clinics', ClinicController::class);
    Route::get('/clinics/{id}/settings', [ClinicController::class, 'settings']);
    Route::put('/clinics/{id}/settings', [ClinicController::class, 'updateSettings']);

    // Uploads
    Route::apiResource('uploads', UploadController::class);
    Route::get('/uploads/{id}/status', [UploadController::class, 'status']);

    // Registros
    Route::apiResource('records', RecordController::class)->only(['index', 'show', 'update']);
    Route::get('/records/search', [RecordController::class, 'search']);

    // Relatórios
    Route::apiResource('reports', ReportController::class)->only(['index', 'show', 'store']);
    Route::get('/reports/{id}/export/csv', [ReportController::class, 'exportCsv']);
    Route::get('/reports/{id}/export/pdf', [ReportController::class, 'exportPdf']);

    // Usuários
    Route::apiResource('users', UserController::class);
    Route::post('/users/{id}/roles', [UserController::class, 'assignRole']);
    Route::delete('/users/{id}/roles', [UserController::class, 'removeRole']);

    // Dashboard
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);
});
