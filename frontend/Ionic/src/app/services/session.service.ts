import { Injectable, inject } from '@angular/core';
import { Router } from '@angular/router';
import { firstValueFrom, timeout } from 'rxjs';
import { ApiService } from './api.service';

export interface SessionUser {
  id: number; name: string; email: string; role: string; business_id: number | null;
  business?: { name?: string; slug?: string } | null;
}

/** Tiempo durante el que se confía en el usuario guardado sin volver a llamar a /auth/me. */
const TTL_VALIDACION_MS = 60_000;
/** Si /auth/me no responde en este tiempo, no se bloquea la navegación. */
const TIMEOUT_VALIDACION_MS = 8_000;

@Injectable({ providedIn: 'root' })
export class SessionService {
  private readonly api = inject(ApiService);
  private readonly router = inject(Router);

  private validadoEn = 0;
  private verificacion?: Promise<SessionUser | null>;

  save(data: { token: string; user: SessionUser }) {
    localStorage.setItem('token', data.token); localStorage.setItem('auth_token', data.token);
    localStorage.setItem('user', JSON.stringify(data.user));
    this.validadoEn = Date.now(); // recién autenticado: no hace falta preguntar de nuevo
  }

  get token(): string | null { return localStorage.getItem('token') || localStorage.getItem('auth_token'); }
  get user(): SessionUser | null {
    try { return JSON.parse(localStorage.getItem('user') || 'null'); } catch { return null; }
  }
  /** Slug del negocio del usuario (para /businesses/{slug}/availability). */
  get slug(): string { return this.user?.business?.slug ?? ''; }
  get negocio(): string { return this.user?.business?.name ?? ''; }

  limpiar() { this.clear(); }
  clear() {
    ['token', 'auth_token', 'user'].forEach(key => localStorage.removeItem(key));
    this.validadoEn = 0;
    this.verificacion = undefined;
  }

  isAdmin(user: SessionUser | null): boolean {
    return !!user && ['admin_business', 'super_admin'].includes(user.role);
  }

  home(user: SessionUser) { return this.isAdmin(user) ? '/tabs/dashboard' : '/login'; }

  /**
   * Devuelve el usuario de la sesión. Solo consulta /auth/me si hace más de 1 minuto que se validó
   * (antes se consultaba en CADA navegación, hasta 3 veces seguidas, y la pantalla quedaba "congelada").
   * Las peticiones simultáneas comparten una sola consulta.
   */
  private usuarioValido(): Promise<SessionUser | null> {
    const guardado = this.user;
    if (guardado && Date.now() - this.validadoEn < TTL_VALIDACION_MS) return Promise.resolve(guardado);
    this.verificacion ??= this.consultarMe(guardado).finally(() => { this.verificacion = undefined; });
    return this.verificacion;
  }

  private async consultarMe(guardado: SessionUser | null): Promise<SessionUser | null> {
    try {
      const res = await firstValueFrom(
        this.api.get<{ data: { user: SessionUser } }>('/auth/me').pipe(timeout(TIMEOUT_VALIDACION_MS)),
      );
      const user = res.data.user;
      localStorage.setItem('user', JSON.stringify(user));
      this.validadoEn = Date.now();
      return user;
    } catch (e) {
      const status = (e as { status?: number })?.status;
      if (status === 401 || status === 403) { this.clear(); return null; }
      // Servidor lento, sin conexión o error 5xx: no se saca al usuario de la app, se usa el usuario guardado.
      return guardado;
    }
  }

  async guard(_area: 'admin' | 'any') {
    if (!this.token) return this.router.parseUrl('/login');
    const user = await this.usuarioValido();
    if (!user) return this.router.parseUrl('/login');
    if (!this.isAdmin(user)) { this.clear(); return this.router.parseUrl('/login'); }
    return true;
  }
}
