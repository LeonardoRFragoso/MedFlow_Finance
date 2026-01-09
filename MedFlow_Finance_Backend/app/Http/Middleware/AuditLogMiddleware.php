<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Registrar auditoria apenas para requisições autenticadas
        if (auth()->check()) {
            $this->logAudit($request, $response);
        }

        return $response;
    }

    private function logAudit(Request $request, Response $response): void
    {
        try {
            $user = auth()->user();
            $clinicId = $user->clinic_id ?? null;

            if (!$clinicId) {
                return;
            }

            // Não auditar requisições GET
            if ($request->method() === 'GET') {
                return;
            }

            AuditLog::create([
                'clinic_id' => $clinicId,
                'user_id' => $user->id,
                'action' => $this->getAction($request),
                'resource_type' => $this->getResourceType($request),
                'resource_id' => $this->getResourceId($request),
                'description' => $request->path(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'http_method' => $request->method(),
                'http_path' => $request->path(),
                'http_status_code' => $response->getStatusCode(),
                'status' => $response->getStatusCode() < 400 ? 'success' : 'failure',
                'error_message' => $response->getStatusCode() >= 400 ? $response->getContent() : null,
            ]);
        } catch (\Exception $e) {
            // Silenciosamente falhar para não interromper a requisição
            \Log::error('Erro ao registrar auditoria: ' . $e->getMessage());
        }
    }

    private function getAction(Request $request): string
    {
        $method = $request->method();

        return match ($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => strtolower($method),
        };
    }

    private function getResourceType(Request $request): string
    {
        $path = $request->path();
        $segments = explode('/', $path);

        // Remover 'api' do início
        if ($segments[0] === 'api') {
            array_shift($segments);
        }

        return $segments[0] ?? 'unknown';
    }

    private function getResourceId(Request $request): ?string
    {
        $path = $request->path();
        $segments = explode('/', $path);

        // Remover 'api' do início
        if ($segments[0] === 'api') {
            array_shift($segments);
        }

        // Retornar o segundo segmento se existir (ID do recurso)
        return $segments[1] ?? null;
    }
}
