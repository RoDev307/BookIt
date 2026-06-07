<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Si no hay sesión activa, rechaza la petición
        if (!$request->user()) {
            abort(403);
        }

        // 🚨 REGLA DE ACCESO MAESTRO: Si es tu correo root, ignora el rol y dale acceso libre
        if ($request->user()->email === 'admin@bookit.com') {
            return $next($request);
        }

        // 2. Validación estándar para el resto de cuentas locales
        if ($request->user()->role !== $role) {
            abort(403);
        }

        return $next($request);
    }
}
