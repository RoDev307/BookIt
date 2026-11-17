// src/app/services/api.service.ts
import { Injectable, inject } from '@angular/core';
import { HttpClient, HttpErrorResponse, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../environments/environment';

export type ApiParams = Record<string, string | number | boolean | null | undefined>;

/**
 * Cliente HTTP de la API. La URL base sale de environment.apiUrl (una sola fuente de verdad)
 * y los headers Accept / Authorization los agrega authInterceptor.
 */
@Injectable({
  providedIn: 'root',
})
export class ApiService {
  private readonly http = inject(HttpClient);
  private readonly baseUrl = environment.apiUrl;

  get<T = any>(endpoint: string, params?: ApiParams): Observable<T> {
    return this.http.get<T>(`${this.baseUrl}${endpoint}`, { params: ApiService.aParams(params) });
  }

  post<T = any>(endpoint: string, body: unknown = {}): Observable<T> {
    return this.http.post<T>(`${this.baseUrl}${endpoint}`, body);
  }

  patch<T = any>(endpoint: string, body: unknown = {}): Observable<T> {
    return this.http.patch<T>(`${this.baseUrl}${endpoint}`, body);
  }

  put<T = any>(endpoint: string, body: unknown = {}): Observable<T> {
    return this.http.put<T>(`${this.baseUrl}${endpoint}`, body);
  }

  delete<T = any>(endpoint: string): Observable<T> {
    return this.http.delete<T>(`${this.baseUrl}${endpoint}`);
  }

  getPdf(endpoint: string): Observable<Blob> {
    return this.http.get(`${this.baseUrl}${endpoint}`, {
      headers: { Accept: 'application/pdf' },
      responseType: 'blob',
    });
  }

  logout(token?: string): Observable<any> {
    if (token) {
      return this.http.post(`${this.baseUrl}/auth/logout`, {}, {
        headers: { Authorization: `Bearer ${token}` },
      });
    }
    return this.post('/auth/logout', {});
  }

  /** Convierte un objeto en HttpParams ignorando valores vacíos (undefined / null / ''). */
  private static aParams(params?: ApiParams): HttpParams | undefined {
    if (!params) return undefined;
    let p = new HttpParams();
    for (const [k, v] of Object.entries(params)) {
      if (v !== undefined && v !== null && v !== '') p = p.set(k, String(v));
    }
    return p;
  }

  /**
   * Devuelve el mensaje más útil de un error HTTP:
   * primero el 1er error de validación (422) de Laravel, luego `message`, y si no, el texto de respaldo.
   */
  static mensajeError(err: unknown, respaldo = 'Ocurrió un error. Inténtalo de nuevo.'): string {
    const e = err as HttpErrorResponse | undefined;
    if (e?.status === 0) return 'No se pudo conectar con el servidor. Revisa tu conexión o que el backend esté encendido.';
    const errores = e?.error?.errors as Record<string, string[]> | undefined;
    if (errores && typeof errores === 'object') {
      const primero = Object.values(errores).flat()[0];
      if (primero) return String(primero);
    }
    return e?.error?.message || respaldo;
  }

  /** Extrae el arreglo de una respuesta, sea `[...]`, `{ data: [...] }` o `{ data: { data: [...] } }` (paginado). */
  static lista<T = any>(res: any): T[] {
    if (Array.isArray(res)) return res;
    if (Array.isArray(res?.data)) return res.data;
    if (Array.isArray(res?.data?.data)) return res.data.data;
    return [];
  }
}
