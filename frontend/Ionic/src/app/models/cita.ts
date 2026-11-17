export type EstadoCita = 'completada' | 'confirmada' | 'pendiente' | 'cancelada';

export interface Cita {
  id: number;
  cliente: string;
  /** Texto bajo el nombre: correo del cliente registrado o "Cliente externo". */
  clienteDetalle: string;
  /** true = cita ingresada manualmente por recepción (cliente sin cuenta). */
  esExterno: boolean;
  servicio: string;
  servicioId: number | null;
  profesional: string;
  inicio: string; // YYYY-MM-DDTHH:mm:00 (hora local)
  fin: string;
  estado: EstadoCita;
  precio: number;
  duracionMin: number;
  notas: string;
  creadaEn: string | null;
}

export const ETIQUETAS_ESTADO: Record<EstadoCita, string> = {
  completada: 'Completada',
  confirmada: 'Confirmada',
  pendiente: 'Pendiente',
  cancelada: 'Cancelada',
};

export const ESTADO_DESDE_API: Record<string, EstadoCita> = {
  pending: 'pendiente',
  confirmed: 'confirmada',
  completed: 'completada',
  cancelled: 'cancelada',
  canceled: 'cancelada',
};

export const ESTADO_HACIA_API: Record<EstadoCita, string> = {
  pendiente: 'pending',
  confirmada: 'confirmed',
  completada: 'completed',
  cancelada: 'cancelled',
};

/* ---------- Fechas ---------- */

export const pad = (n: number): string => String(n).padStart(2, '0');

export const claveDia = (fecha: Date): string =>
  `${fecha.getFullYear()}-${pad(fecha.getMonth() + 1)}-${pad(fecha.getDate())}`;

export const aIso = (d: Date): string => `${claveDia(d)}T${pad(d.getHours())}:${pad(d.getMinutes())}:00`;

/** Convierte "2026-10-27 10:00:00" o "2026-10-27T10:00:00" en Date local (sin saltos de zona horaria). */
export function aFecha(valor: string | null | undefined): Date {
  if (!valor) return new Date();
  const [f, h = '00:00:00'] = valor.slice(0, 19).replace('T', ' ').split(' ');
  const [y, m, d] = f.split('-').map(Number);
  const [hh, mm] = h.split(':').map(Number);
  return new Date(y, m - 1, d, hh || 0, mm || 0);
}

/** "13:00" -> "01:00 PM" */
export function hora12(hhmm: string): string {
  const [h, m] = hhmm.split(':').map(Number);
  const suf = h >= 12 ? 'PM' : 'AM';
  const h12 = h % 12 === 0 ? 12 : h % 12;
  return `${pad(h12)}:${pad(m || 0)} ${suf}`;
}

/** Formato que espera Laravel: "YYYY-MM-DD HH:mm:ss" */
export function aFechaApi(fecha: string, hora: string): string {
  const h = hora.length === 5 ? `${hora}:00` : hora;
  return `${fecha} ${h}`;
}

/* ---------- Notas / cliente externo ----------
 * Cuando recepción agenda a un cliente sin cuenta, Laravel guarda el nombre dentro de `notes`:
 *   "Cliente Externo: Juan Pérez | nota opcional"
 * (y `user_id` es el del administrador). Aquí lo separamos igual que hace el backend en /calendar.
 */
const PREFIJO_EXTERNO = 'Cliente Externo:';

export function separarNotas(
  raw: string | null | undefined,
  nombreUsuario?: string | null,
): { cliente: string; notas: string; externo: boolean } {
  const texto = raw ?? '';
  if (texto.includes(PREFIJO_EXTERNO)) {
    const resto = texto.replace(PREFIJO_EXTERNO, '');
    const idx = resto.indexOf('|');
    const nombre = (idx >= 0 ? resto.slice(0, idx) : resto).trim();
    const notas = idx >= 0 ? resto.slice(idx + 1).trim() : '';
    return { cliente: nombre || 'Cliente externo', notas, externo: true };
  }
  return { cliente: nombreUsuario || 'Cliente', notas: texto, externo: false };
}

/** Reconstruye el campo `notes` conservando el prefijo si la cita es de cliente externo. */
export function armarNotas(cita: Pick<Cita, 'esExterno' | 'cliente'>, nota: string): string {
  const limpia = nota.trim();
  return cita.esExterno ? `${PREFIJO_EXTERNO} ${cita.cliente}${limpia ? ' | ' + limpia : ''}` : limpia;
}

/** Mapea una cita del API (/tenant/appointments) al modelo de la app. */
export function mapearCita(raw: any): Cita {
  const servicio = raw.service ?? {};
  const { cliente, notas, externo } = separarNotas(raw.notes, raw.user?.name);
  const dur = Number(servicio.duration_minutes ?? 0);
  const inicio = aFecha(raw.appointment_time);
  const fin = new Date(+inicio + dur * 60000);

  return {
    id: raw.id,
    cliente,
    clienteDetalle: externo ? 'Cliente externo (agendado por recepción)' : raw.user?.email || 'Cliente registrado',
    esExterno: externo,
    servicio: servicio.name ?? 'Servicio',
    servicioId: raw.service_id ?? servicio.id ?? null,
    profesional: raw.staff_name || 'Sin asignar',
    inicio: aIso(inicio),
    fin: aIso(fin),
    estado: ESTADO_DESDE_API[raw.status] ?? 'pendiente',
    precio: Number(servicio.price ?? 0),
    duracionMin: dur,
    notas,
    creadaEn: raw.created_at ?? null,
  };
}

export const formatoDinero = (valor: number, decimales = 2): string =>
  new Intl.NumberFormat('es-SV', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: decimales,
    maximumFractionDigits: decimales,
  }).format(valor);
