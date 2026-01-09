<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $user = auth()->user();
        $clinicId = $request->route('clinic_id') ?? $request->input('clinic_id');

        // Se houver clinic_id na rota/request, validar se o usuário tem acesso
        if ($clinicId && $user->clinic_id !== $clinicId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
