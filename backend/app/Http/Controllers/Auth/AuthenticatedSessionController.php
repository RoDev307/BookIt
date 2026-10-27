<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Escenario Root: Administrador Maestro va a la consola global de citas/métricas SaaS
        if (is_null($user->business_id) && ($user->role === 'admin_business' || $user->email === 'admin@bookit.com')) {
            return redirect()->route('dashboard');
        }

        // 🚨 CORREGIDO: Los comercios locales (admin_business) ahora van DIRECTO a su panel de métricas de negocio
        if ($user->role === 'admin_business' || $user->business_id !== null) {
            return redirect()->route('dashboard');
        }

        // Escenario Cliente
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
