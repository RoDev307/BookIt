<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return response()->json(['success' => false, 'message' => 'No autenticado.'], 401);
        }

        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $allowed) {
                $allowedRoles[] = trim($allowed);
            }
        }

        if (!in_array($request->user()->role, $allowedRoles, true)) {
            return response()->json(['success' => false, 'message' => 'No tienes permisos para acceder a este recurso.'], 403);
        }

        return $next($request);
    }
}
