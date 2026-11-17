import { HttpClient, provideHttpClient, withInterceptors } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { Router, provideRouter } from '@angular/router';
import { environment } from '../../environments/environment';
import { authInterceptor } from './auth.interceptor';

describe('authInterceptor', () => {
  let http: HttpClient;
  let ctrl: HttpTestingController;

  beforeEach(() => {
    localStorage.clear();
    TestBed.configureTestingModule({
      providers: [provideRouter([]), provideHttpClient(withInterceptors([authInterceptor])), provideHttpClientTesting()],
    });
    http = TestBed.inject(HttpClient);
    ctrl = TestBed.inject(HttpTestingController);
  });

  it('agrega Accept y Bearer token a las peticiones de la API', () => {
    localStorage.setItem('token', 'abc123');
    http.get(`${environment.apiUrl}/auth/me`).subscribe();
    const req = ctrl.expectOne(`${environment.apiUrl}/auth/me`);
    expect(req.request.headers.get('Authorization')).toBe('Bearer abc123');
    expect(req.request.headers.get('Accept')).toBe('application/json');
    req.flush({});
  });

  it('no toca peticiones a otros dominios', () => {
    localStorage.setItem('token', 'abc123');
    http.get('https://otro-dominio.com/x').subscribe();
    const req = ctrl.expectOne('https://otro-dominio.com/x');
    expect(req.request.headers.has('Authorization')).toBe(false);
    req.flush({});
  });

  it('ante un 401 limpia la sesión y manda a /login', () => {
    localStorage.setItem('token', 'viejo');
    localStorage.setItem('user', '{}');
    const nav = vi.spyOn(TestBed.inject(Router), 'navigate').mockResolvedValue(true);
    http.get(`${environment.apiUrl}/tenant/services`).subscribe({ error: () => undefined });
    ctrl.expectOne(`${environment.apiUrl}/tenant/services`).flush({}, { status: 401, statusText: 'Unauthorized' });
    expect(localStorage.getItem('token')).toBeNull();
    expect(nav).toHaveBeenCalledWith(['/login'], { replaceUrl: true });
  });

  it('un 401 en /auth/login no redirige (lo maneja la pantalla de login)', () => {
    const nav = vi.spyOn(TestBed.inject(Router), 'navigate').mockResolvedValue(true);
    http.post(`${environment.apiUrl}/auth/login`, {}).subscribe({ error: () => undefined });
    ctrl.expectOne(`${environment.apiUrl}/auth/login`).flush({}, { status: 401, statusText: 'Unauthorized' });
    expect(nav).not.toHaveBeenCalled();
  });
});
