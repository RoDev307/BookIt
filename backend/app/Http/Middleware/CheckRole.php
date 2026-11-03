<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Verificar que exista una sesión autenticada
        if (!$request->user()) {
            abort(401);
        }

        // Verificar que el usuario tenga el rol requerido
        if ($request->user()->role !== $role) {
            abort(403, 'No tienes permiso para acceder a este recurso.');
        }

        return $next($request);
    }
}
