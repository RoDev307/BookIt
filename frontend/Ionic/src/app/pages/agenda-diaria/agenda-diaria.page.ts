import { CommonModule, DatePipe } from '@angular/common';
import { Component, DestroyRef, computed, inject, signal, viewChild } from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { Router } from '@angular/router';
import {
  IonAvatar,
  IonBadge,
  IonButton,
  IonCard,
  IonContent,
  IonFooter,
  IonHeader,
  IonIcon,
  IonRefresher,
  IonRefresherContent,
  IonSkeletonText,
  IonToolbar,
} from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  add,
  calendarOutline,
  chevronBack,
  chevronDown,
  chevronForward,
  notificationsOutline,
  timeOutline,
  logOutOutline,
} from 'ionicons/icons';
import { Subject, catchError, of, switchMap, tap } from 'rxjs';
import { ApiService } from '../../services/api.service';

export type EstadoCita = 'completada' | 'confirmada' | 'pendiente' | 'cancelada';

export interface Cita {
  id: number;
  cliente: string;
  servicio: string;
  profesional: string;
  inicio: string;
  fin: string;
  estado: EstadoCita;
  precio: number;
  duracionMin: number;
}

interface DiaSemana {
  fecha: Date;
  clave: string;
  letra: string;
  numero: number;
}

export const claveDia = (fecha: Date): string => {
  const y = fecha.getFullYear();
  const m = String(fecha.getMonth() + 1).padStart(2, '0');
  const d = String(fecha.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
};

const capitalizar = (texto: string) => texto.charAt(0).toUpperCase() + texto.slice(1);
const pad = (n: number) => String(n).padStart(2, '0');

const ESTADO_DESDE_API: Record<string, EstadoCita> = {
  pending: 'pendiente',
  confirmed: 'confirmada',
  completed: 'completada',
  cancelled: 'cancelada',
  canceled: 'cancelada',
};

function aFecha(valor: string): Date {
  if (!valor) return new Date();
  const [f, h = '00:00:00'] = valor.slice(0, 19).replace('T', ' ').split(' ');
  const [y, m, d] = f.split('-').map(Number);
  const [hh, mm] = h.split(':').map(Number);
  return new Date(y, m - 1, d, hh, mm);
}

const aIso = (d: Date) => `${claveDia(d)}T${pad(d.getHours())}:${pad(d.getMinutes())}:00`;

function mapear(raw: any): Cita {
  const servicio = raw.service ?? raw.servicio ?? {};
  const cli = raw.client ?? raw.user ?? raw.customer ?? {};
  const esp = raw.specialist ?? raw.employee ?? raw.professional ?? null;
  const dur = Number(servicio.duration_minutes ?? 0);
  const inicio = aFecha(raw.appointment_time || raw.fecha_cita);
  const fin = new Date(+inicio + dur * 60000);

  return {
    id: raw.id,
    cliente: raw.client?.name ? raw.client.name : raw.notes?.startsWith('Cliente Externo:')
      ? raw.notes.slice(16).split('|')[0].trim()
      : (cli.name ?? raw.customer_name ?? 'Cliente'),
    servicio: servicio.name ?? 'Servicio',
    profesional: esp?.name ?? raw.staff_name ?? 'Sin asignar',
    inicio: aIso(inicio),
    fin: aIso(fin),
    estado: ESTADO_DESDE_API[raw.status] ?? 'pendiente',
    precio: Number(servicio.price ?? 0),
    duracionMin: dur,
  };
}

@Component({
  selector: 'app-agenda-diaria',
  templateUrl: './agenda-diaria.page.html',
  styleUrls: ['./agenda-diaria.page.scss'],
  standalone: true,
  imports: [
    CommonModule,
    DatePipe,
    IonAvatar,
    IonBadge,
    IonButton,
    IonCard,
    IonContent,
    IonFooter,
    IonHeader,
    IonIcon,
    IonRefresher,
    IonRefresherContent,
    IonSkeletonText,
    IonToolbar,
  ],
})
export class AgendaDiariaPage {
  private readonly destroyRef = inject(DestroyRef);
  private readonly router = inject(Router);
  private readonly apiService = inject(ApiService);
  private readonly refresher = viewChild(IonRefresher);
  private readonly cargarCitas$ = new Subject<void>();

  readonly estudio = 'BookIt Admin';
  readonly iniciales = 'BA';
  readonly etiquetasEstado: Record<EstadoCita, string> = {
    completada: 'Completada',
    confirmada: 'Confirmada',
    pendiente: 'Pendiente',
    cancelada: 'Cancelada',
  };

  readonly fechaSeleccionada = signal(new Date());
  readonly ahora = signal(new Date());
  readonly todas = signal<Cita[]>([]);
  readonly cargando = signal(true);
  readonly error = signal(false);
  readonly citaSeleccionadaId = signal<number | null>(null);

  readonly claveSeleccionada = computed(() => claveDia(this.fechaSeleccionada()));
  readonly esHoy = computed(() => this.claveSeleccionada() === claveDia(this.ahora()));

  readonly citas = computed(() => this.todas().filter((c) => c.inicio.slice(0, 10) === this.claveSeleccionada()));

  readonly diasConCitas = computed(
    () =>
      new Set(
        this.todas()
          .filter((c) => c.estado !== 'cancelada')
          .map((c) => c.inicio.slice(0, 10)),
      ),
  );

  readonly fechaLarga = computed(() =>
    capitalizar(
      new Intl.DateTimeFormat('es-ES', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
      }).format(this.fechaSeleccionada()),
    ),
  );

  readonly mesAnio = computed(() => {
    const f = this.fechaSeleccionada();
    const mes = new Intl.DateTimeFormat('es-ES', { month: 'long' }).format(f);
    return `${capitalizar(mes)} ${f.getFullYear()}`;
  });

  readonly semana = computed<DiaSemana[]>(() => {
    const f = this.fechaSeleccionada();
    const diasDesdeLunes = (f.getDay() + 6) % 7;
    const lunes = new Date(f.getFullYear(), f.getMonth(), f.getDate() - diasDesdeLunes);
    return Array.from({ length: 7 }, (_, i) => {
      const fecha = new Date(lunes.getFullYear(), lunes.getMonth(), lunes.getDate() + i);
      return { fecha, clave: claveDia(fecha), letra: 'LMXJVSD'[i], numero: fecha.getDate() };
    });
  });

  readonly etiquetaDia = computed(() => (this.esHoy() ? 'Citas de hoy' : 'Citas del día'));
  readonly porConfirmar = computed(() => this.citas().filter((c) => c.estado === 'pendiente').length);
  readonly ingresos = computed(() => {
    const total = this.citas()
      .filter((c) => c.estado !== 'cancelada')
      .reduce((suma, c) => suma + c.precio, 0);
    return new Intl.NumberFormat('es-SV', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 0,
    }).format(total);
  });

  readonly citasOrdenadas = computed(() => [...this.citas()].sort((a, b) => a.inicio.localeCompare(b.inicio)));

  readonly proxima = computed(() => {
    if (!this.esHoy()) return null;
    const ahora = this.ahora();
    const siguiente = this.citas()
      .filter((c) => c.estado !== 'cancelada' && c.estado !== 'completada' && new Date(c.inicio) > ahora)
      .sort((a, b) => a.inicio.localeCompare(b.inicio))[0];
    if (!siguiente) return null;
    const minutos = Math.ceil((new Date(siguiente.inicio).getTime() - ahora.getTime()) / 60_000);
    return { cliente: siguiente.cliente, tiempo: this.formatearTiempo(minutos) };
  });

  constructor() {
    addIcons({
      add,
      calendarOutline,
      chevronBack,
      chevronDown,
      chevronForward,
      notificationsOutline,
      timeOutline,
      logOutOutline,
    });

    this.cargarCitas$
      .pipe(
        tap(() => {
          this.cargando.set(true);
          this.error.set(false);
        }),
        switchMap(() =>
          this.apiService.get<any>('/tenant/appointments').pipe(
            catchError((e) => {
              console.error('No se pudieron cargar las citas:', e);
              this.error.set(true);
              return of(null);
            }),
          ),
        ),
        takeUntilDestroyed(),
      )
      .subscribe((res) => {
        if (res) {
          const rawList = res.data ?? res;
          const lista: any[] = Array.isArray(rawList) ? rawList : (rawList?.data ?? []);
          this.todas.set(lista.map(mapear));
        }
        this.cargando.set(false);
        (this.refresher() as any)?.complete();
      });

    const temporizador = setInterval(() => this.ahora.set(new Date()), 30_000);
    this.destroyRef.onDestroy(() => clearInterval(temporizador));
    // La primera carga la dispara ionViewWillEnter (evita pedir las citas dos veces al entrar).
  }

  ionViewWillEnter() {
    this.recargar();
  }

  recargar() {
    this.cargarCitas$.next();
  }

  seleccionarDia(fecha: Date) {
    this.fechaSeleccionada.set(fecha);
  }

  cambiarSemana(direccion: -1 | 1) {
    const f = this.fechaSeleccionada();
    this.seleccionarDia(new Date(f.getFullYear(), f.getMonth(), f.getDate() + direccion * 7));
  }

  irAHoy() {
    this.seleccionarDia(new Date());
  }

  abrirCita(cita: Cita) {
    this.citaSeleccionadaId.set(cita.id);
    this.router.navigate(['/gestion-citas', cita.id]);
  }

  nuevaCita() {
    this.router.navigate(['/gestion-citas']);
  }

  cerrarSesion() {
    if (confirm('¿Estás seguro de que deseas cerrar sesión?')) {
      this.apiService.post('/auth/logout', {}).subscribe({
        next: () => this.limpiarYRedirigir(),
        error: () => this.limpiarYRedirigir(),
      });
    }
  }

  private limpiarYRedirigir() {
    localStorage.removeItem('token');
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user');
    this.router.navigate(['/login'], { replaceUrl: true });
  }

  private formatearTiempo(minutos: number): string {
    if (minutos < 60) return `${minutos} min`;
    const h = Math.floor(minutos / 60);
    const m = minutos % 60;
    return m === 0 ? `${h} h` : `${h} h ${m} min`;
  }
}
