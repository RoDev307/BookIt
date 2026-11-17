import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { UrlTree, provideRouter } from '@angular/router';
import { environment } from '../../environments/environment';
import { SessionService } from './session.service';

const ME = `${environment.apiUrl}/auth/me`;
const admin = { id: 1, name: 'Ana', email: 'a@a.com', role: 'admin_business', business_id: 1 };

describe('SessionService.guard (rendimiento)', () => {
  let session: SessionService;
  let http: HttpTestingController;

  beforeEach(() => {
    localStorage.clear();
    TestBed.configureTestingModule({
      providers: [provideRouter([]), provideHttpClient(), provideHttpClientTesting()],
    });
    session = TestBed.inject(SessionService);
    http = TestBed.inject(HttpTestingController);
  });

  it('sin token manda a /login sin llamar al backend', async () => {
    const r = await session.guard('admin');
    expect(r instanceof UrlTree && r.toString()).toBe('/login');
    http.expectNone(ME);
  });

  it('justo después de iniciar sesión no consulta /auth/me al navegar', async () => {
    session.save({ token: 't', user: admin });
    expect(await session.guard('admin')).toBe(true);
    expect(await session.guard('admin')).toBe(true);
    http.expectNone(ME);
  });

  it('varias guardas simultáneas comparten UNA sola petición a /auth/me', async () => {
    localStorage.setItem('token', 't');
    localStorage.setItem('user', JSON.stringify(admin));
    const todas = Promise.all([session.guard('admin'), session.guard('admin'), session.guard('any')]);
    http.expectOne(ME).flush({ data: { user: admin } }); // si hubiera 2+ peticiones, expectOne fallaría
    expect(await todas).toEqual([true, true, true]);
    // y dentro del minuto siguiente ya no vuelve a consultar
    expect(await session.guard('admin')).toBe(true);
    http.expectNone(ME);
  });

  it('si el servidor falla o no responde (status 0) no saca al usuario de la app', async () => {
    localStorage.setItem('token', 't');
    localStorage.setItem('user', JSON.stringify(admin));
    const p = session.guard('admin');
    http.expectOne(ME).error(new ProgressEvent('error'), { status: 0 });
    expect(await p).toBe(true);
    expect(localStorage.getItem('token')).toBe('t');
  });

  it('con 401 limpia la sesión y manda a /login', async () => {
    localStorage.setItem('token', 't');
    localStorage.setItem('user', JSON.stringify(admin));
    const p = session.guard('admin');
    http.expectOne(ME).flush({}, { status: 401, statusText: 'Unauthorized' });
    const r = await p;
    expect(r instanceof UrlTree && r.toString()).toBe('/login');
    expect(localStorage.getItem('token')).toBeNull();
  });

  it('una cuenta de cliente final no abre el área admin y pierde su sesión', async () => {
    session.save({ token: 't', user: { ...admin, role: 'client' } });
    const r = await session.guard('admin');
    expect(r instanceof UrlTree && r.toString()).toBe('/login');
    expect(localStorage.getItem('token')).toBeNull();
    expect(localStorage.getItem('user')).toBeNull();
  });
});
