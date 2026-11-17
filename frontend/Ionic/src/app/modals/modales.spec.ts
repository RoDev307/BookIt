import { provideHttpClient } from '@angular/common/http';
import { HttpTestingController, provideHttpClientTesting } from '@angular/common/http/testing';
import { TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';
import { ModalController, provideIonicAngular } from '@ionic/angular';
import { environment } from '../../environments/environment';
import { mapearCita } from '../models/cita';
import { DetalleCitaModal } from './detalle-cita/detalle-cita.modal';
import { NuevaCitaModal } from './nueva-cita/nueva-cita.modal';

const API = environment.apiUrl;

function config() {
  TestBed.configureTestingModule({
    providers: [provideRouter([]), provideIonicAngular(), provideHttpClient(), provideHttpClientTesting()],
  });
  return TestBed.inject(HttpTestingController);
}

describe('NuevaCitaModal', () => {
  it('crea la cita con los campos que exige POST /tenant/appointments', () => {
    const http = config();
    const dismiss = vi.spyOn(TestBed.inject(ModalController), 'dismiss').mockResolvedValue(true);
    const modal = TestBed.createComponent(NuevaCitaModal).componentInstance;
    modal.ngOnInit();

    http.expectOne(`${API}/tenant/services`).flush({
      success: true,
      data: [
        { id: 2, name: 'Limpieza', price: '25.00', duration_minutes: 45, is_active: 1 },
        { id: 3, name: 'Inactivo', price: '10.00', duration_minutes: 30, is_active: 0 },
      ],
    });
    expect(modal.servicios().map((s) => s.id)).toEqual([2]); // solo activos

    expect(modal.valido()).toBe(false);
    modal.cliente.set('Juan Pérez');
    modal.servicioId.set(2);
    modal.staff.set('Dr. Mendoza');
    modal.fecha.set('2099-01-05');
    modal.hora.set('10:00');
    expect(modal.valido()).toBe(true);

    modal.guardar();
    const req = http.expectOne(`${API}/tenant/appointments`);
    expect(req.request.method).toBe('POST');
    expect(req.request.body).toEqual({
      service_id: 2,
      client_name: 'Juan Pérez',
      staff_name: 'Dr. Mendoza',
      fecha_cita: '2099-01-05',
      hora_cita: '10:00',
      notes: null,
    });
    req.flush({ success: true, data: {} }, { status: 201, statusText: 'Created' });
    expect(dismiss).toHaveBeenCalledWith({ fecha: '2099-01-05' }, 'creada');
  });

  it('muestra el mensaje de validación del backend (422)', () => {
    const http = config();
    const modal = TestBed.createComponent(NuevaCitaModal).componentInstance;
    modal.ngOnInit();
    http.expectOne(`${API}/tenant/services`).flush({ success: true, data: [] });
    modal.cliente.set('A');
    modal.servicioId.set(1);
    modal.staff.set('B');
    modal.fecha.set('2099-01-04');
    modal.hora.set('07:00');
    modal.guardar();
    http.expectOne(`${API}/tenant/appointments`).flush(
      { success: false, message: 'x', errors: { appointment_time: ['El horario de atención para este día es de 08:00 a 17:00.'] } },
      { status: 422, statusText: 'Unprocessable' },
    );
    expect(modal.error()).toBe('El horario de atención para este día es de 08:00 a 17:00.');
    expect(modal.guardando()).toBe(false);
  });
});

describe('DetalleCitaModal', () => {
  const cita = () =>
    mapearCita({
      id: 9,
      service_id: 2,
      staff_name: 'Dr. Mendoza',
      appointment_time: '2099-01-05 10:00:00',
      status: 'pending',
      notes: 'Cliente Externo: Ana López | Alergia',
      service: { id: 2, name: 'Limpieza', price: '25.00', duration_minutes: 45 },
      user: { name: 'Admin', email: 'a@a.com' },
    });

  it('confirma con PATCH /status y avisa a la agenda', () => {
    const http = config();
    const modal = TestBed.createComponent(DetalleCitaModal).componentInstance;
    const alCambiar = vi.fn();
    modal.cita = cita();
    modal.alCambiar = alCambiar;

    modal.confirmar();
    const req = http.expectOne(`${API}/tenant/appointments/9/status`);
    expect(req.request.method).toBe('PATCH');
    expect(req.request.body).toEqual({ status: 'confirmed' });
    req.flush({ success: true });
    expect(modal.c()?.estado).toBe('confirmada');
    expect(alCambiar).toHaveBeenCalled();
  });

  it('al editar la nota conserva el nombre del cliente externo', () => {
    const http = config();
    const modal = TestBed.createComponent(DetalleCitaModal).componentInstance;
    modal.cita = cita();
    modal.empezarNota();
    expect(modal.borradorNota()).toBe('Alergia');
    modal.borradorNota.set('Sin alergias');
    modal.guardarNota();
    const req = http.expectOne(`${API}/tenant/appointments/9`);
    expect(req.request.method).toBe('PUT');
    expect(req.request.body).toEqual({ notes: 'Cliente Externo: Ana López | Sin alergias' });
    req.flush({ success: true });
    expect(modal.c()?.notas).toBe('Sin alergias');
    expect(modal.c()?.cliente).toBe('Ana López');
  });

  it('reprograma con appointment_time en formato Y-m-d H:i:s', () => {
    const http = config();
    const modal = TestBed.createComponent(DetalleCitaModal).componentInstance;
    modal.cita = cita();
    modal.nuevaFecha.set('2099-01-06');
    modal.nuevaHora.set('11:00');
    modal.guardarReprogramacion();
    const req = http.expectOne(`${API}/tenant/appointments/9`);
    expect(req.request.body).toEqual({ appointment_time: '2099-01-06 11:00:00' });
    req.flush({ success: true });
    expect(modal.c()?.inicio).toBe('2099-01-06T11:00:00');
    expect(modal.c()?.fin).toBe('2099-01-06T11:45:00');
  });
});
