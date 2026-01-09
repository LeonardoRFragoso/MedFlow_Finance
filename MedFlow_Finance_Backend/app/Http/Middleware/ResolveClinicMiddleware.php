<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveClinicMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Injetar clinic_id no contexto da requisição
            $request->attributes->set('clinic_id', $user->clinic_id);
            
            // Disponibilizar clinic_id globalmente
            app()->singleton('clinic_id', function () use ($user) {
                return $user->clinic_id;
            });
        }

        return $next($request);
    }
}
