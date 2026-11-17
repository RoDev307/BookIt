import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { provideIonicAngular } from '@ionic/angular';
import { environment } from '../../environments/environment';
import { CatalogoServiciosPage } from './catalogo-servicios/catalogo-servicios.page';
import { DisponibilidadyHorariosPage } from './disponibilidady-horarios/disponibilidady-horarios.page';

const API = environment.apiUrl;

function config() {
  TestBed.configureTestingModule({
    providers: [provideRouter([]), provideIonicAngular(), provideHttpClient(), provideHttpClientTesting()],
  });
  return TestBed.inject(HttpTestingController);
}

describe('Disponibilidad (horario semanal)', () => {
  it('mapea day_of_week / opening_time / is_closed del backend y guarda con los nombres correctos', () => {
    const http = config();
    const page = TestBed.createComponent(DisponibilidadyHorariosPage).componentInstance;
    page.ngOnInit();

    http.expectOne(`${API}/tenant/business`).flush({
      success: true,
      data: {
        name: 'Clínica Dental Sonrisas',
        hours: [
          { day_of_week: 1, opening_time: '08:30:00', closing_time: '19:00:00', is_closed: 0 },
          { day_of_week: 6, opening_time: '09:00:00', closing_time: '14:00:00', is_closed: 0 },
          { day_of_week: 0, opening_time: null, closing_time: null, is_closed: 1 },
        ],
      },
    });

    const h = page.horario;
    expect(page.cargando).toBe(false);
    expect(h.map((d) => d.num)).toEqual([1, 2, 3, 4, 5, 6, 0]); // lunes→domingo
    expect(h[0]).toMatchObject({ etiqueta: 'Lunes', abierto: true, apertura: '08:30', cierre: '19:00' });
    expect(h[5]).toMatchObject({ etiqueta: 'Sábado', abierto: true, apertura: '09:00', cierre: '14:00' });
    expect(h[6]).toMatchObject({ etiqueta: 'Domingo', abierto: false });
    expect(h[1].abierto).toBe(true); // martes sin registro -> regla por defecto del backend

    page.toggleDia(h[1], { detail: { checked: false } });
    page.guardar();
    const put = http.expectOne(`${API}/tenant/business/hours`);
    expect(put.request.method).toBe('PUT');
    const dias = put.request.body.hours;
    expect(dias.length).toBe(7);
    expect(dias[1]).toEqual({ day_of_week: 2, opening_time: '08:00:00', closing_time: '17:00:00', is_closed: true });
    expect(dias[6].day_of_week).toBe(0);
    put.flush({ success: true, data: [] });
    expect(page.sucio).toBe(false);
  });
});

describe('Servicios', () => {
  const servicio = {
    id: 5,
    name: 'Limpieza Dental',
    description: null,
    price: '25.00',
    duration_minutes: 45,
    is_active: 1,
    appointments_count: 2,
  };

  it('carga /tenant/services (incluye inactivos) y al activar/desactivar envía el servicio completo', () => {
    const http = config();
    const page = TestBed.createComponent(CatalogoServiciosPage).componentInstance;
    page.ionViewWillEnter();

    http.expectOne(`${API}/tenant/services`).flush({
      success: true,
      data: [servicio, { ...servicio, id: 6, name: 'Relleno', is_active: 0 }],
    });
    expect(page.servicios.length).toBe(2);
    expect(page.servicios[1].is_active).toBe(false);

    page.toggleActivo(page.servicios[0], { detail: { checked: false } });
    const put = http.expectOne(`${API}/tenant/services/5`);
    expect(put.request.method).toBe('PUT');
    expect(put.request.body).toEqual({
      name: 'Limpieza Dental',
      description: null,
      price: 25,
      duration_minutes: 45,
      is_active: false,
    });
    put.flush({ success: true });
    expect(page.servicios[0].is_active).toBe(false);
  });

  it('revierte el toggle si el backend responde error', () => {
    const http = config();
    const page = TestBed.createComponent(CatalogoServiciosPage).componentInstance;
    page.ionViewWillEnter();
    http.expectOne(`${API}/tenant/services`).flush({ success: true, data: [servicio] });

    page.toggleActivo(page.servicios[0], { detail: { checked: false } });
    expect(page.servicios[0].is_active).toBe(false); // optimista
    http.expectOne(`${API}/tenant/services/5`).flush({ message: 'x' }, { status: 422, statusText: 'Unprocessable' });
    expect(page.servicios[0].is_active).toBe(true); // revertido
  });
});
