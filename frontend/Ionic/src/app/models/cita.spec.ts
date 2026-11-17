import { armarNotas, aFechaApi, hora12, mapearCita, separarNotas } from './cita';

describe('modelo de cita', () => {
  it('separa el cliente externo guardado dentro de notes', () => {
    expect(separarNotas('Cliente Externo: Ana López | Cortar puntas', 'Admin')).toEqual({
      cliente: 'Ana López',
      notas: 'Cortar puntas',
      externo: true,
    });
    expect(separarNotas('Cliente Externo: Ana López', 'Admin').notas).toBe('');
  });

  it('usa el nombre del usuario cuando no es cliente externo', () => {
    expect(separarNotas('Llegará tarde', 'Carlos Ruiz')).toEqual({
      cliente: 'Carlos Ruiz',
      notas: 'Llegará tarde',
      externo: false,
    });
  });

  it('conserva el prefijo al reconstruir las notas', () => {
    const c = { esExterno: true, cliente: 'Ana López' };
    expect(armarNotas(c, ' nueva nota ')).toBe('Cliente Externo: Ana López | nueva nota');
    expect(armarNotas(c, '')).toBe('Cliente Externo: Ana López');
    expect(armarNotas({ esExterno: false, cliente: 'X' }, 'hola')).toBe('hola');
  });

  it('mapea una cita del API de Laravel', () => {
    const c = mapearCita({
      id: 7,
      service_id: 3,
      staff_name: 'Dra. Pérez',
      appointment_time: '2026-11-12 09:00:00',
      status: 'confirmed',
      notes: 'Cliente Externo: Maria Delgado | Primera vez',
      service: { id: 3, name: 'Limpieza', price: '25.00', duration_minutes: 45 },
      user: { name: 'Admin Clínica Sonrisas', email: 'clinica@bookit.com' },
    });
    expect(c.cliente).toBe('Maria Delgado');
    expect(c.estado).toBe('confirmada');
    expect(c.inicio).toBe('2026-11-12T09:00:00');
    expect(c.fin).toBe('2026-11-12T09:45:00');
    expect(c.precio).toBe(25);
    expect(c.notas).toBe('Primera vez');
    expect(c.profesional).toBe('Dra. Pérez');
  });

  it('formatea hora y fecha para el API', () => {
    expect(hora12('13:00')).toBe('01:00 PM');
    expect(hora12('08:00')).toBe('08:00 AM');
    expect(hora12('12:00')).toBe('12:00 PM');
    expect(aFechaApi('2026-11-12', '10:00')).toBe('2026-11-12 10:00:00');
  });
});
