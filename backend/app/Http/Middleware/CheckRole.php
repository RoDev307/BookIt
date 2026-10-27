<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Valida el rol del usuario autenticado. Soporta múltiples roles separados por coma.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado.'
            ], 401);
        }

        // Acceso global para el super administrador maestro
        if ($request->user()->email === 'admin@bookit.com' || $request->user()->role === 'super_admin') {
            return $next($request);
        }

        // Si se pasan múltiples roles separados por coma en un solo string
        $allowedRoles = [];
        foreach ($roles as $r) {
            foreach (explode(',', $r) as $subRole) {
                $allowedRoles[] = trim($subRole);
            }
        }

        if (!in_array($request->user()->role, $allowedRoles, true)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para acceder a este recurso.'
            ], 403);
        }

        return $next($request);
    }
}
