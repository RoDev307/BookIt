<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
            if (!auth()->check()) {
        return redirect('/login');
        }

             if (auth()->user()->role !== 'admin_business') {
                abort(403, 'No autorizado');
    }

         return $next($request);
    }
}