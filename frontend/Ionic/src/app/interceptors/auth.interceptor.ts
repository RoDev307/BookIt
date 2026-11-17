import { HttpInterceptorFn } from '@angular/common/http';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { catchError, throwError } from 'rxjs';
import { environment } from '../../environments/environment';

const RUTAS_SIN_REDIRECCION = ['/auth/login', '/auth/register', '/auth/me'];

/**
 * - Agrega Accept + Bearer token a las peticiones hacia la API.
 * - Si el backend responde 401 (token vencido/revocado) limpia la sesión y manda al login.
 *
 * Nota: no inyecta SessionService a propósito (SessionService -> ApiService -> HttpClient -> interceptor
 * sería una dependencia circular), por eso lee el token directamente de localStorage.
 */
export const authInterceptor: HttpInterceptorFn = (req, next) => {
  if (!req.url.startsWith(environment.apiUrl)) return next(req);

  const router = inject(Router);
  const token = localStorage.getItem('token') || localStorage.getItem('auth_token');

  let headers = req.headers;
  if (!headers.has('Accept')) headers = headers.set('Accept', 'application/json');
  if (token && !headers.has('Authorization')) headers = headers.set('Authorization', `Bearer ${token}`);

  return next(req.clone({ headers })).pipe(
    catchError((err) => {
      // login/register/me los maneja cada pantalla o el guard (evita doble navegación)
      const manejadoAparte = RUTAS_SIN_REDIRECCION.some((r) => req.url.endsWith(r));
      if (err?.status === 401 && !manejadoAparte) {
        ['token', 'auth_token', 'user'].forEach((k) => localStorage.removeItem(k));
        router.navigate(['/login'], { replaceUrl: true });
      }
      return throwError(() => err);
    }),
  );
};
