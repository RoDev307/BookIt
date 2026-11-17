import { Component, Input, computed, inject, signal } from '@angular/core';
import { AlertController, IonContent, IonIcon, IonSpinner, ModalController, ToastController } from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  calendarClearOutline,
  checkmark,
  checkmarkDone,
  close,
  informationCircleOutline,
  personOutline,
  timeOutline,
} from 'ionicons/icons';
import { SelectorHorasComponent } from '../../components/selector-horas/selector-horas.component';
import {
  Cita,
  ESTADO_HACIA_API,
  ETIQUETAS_ESTADO,
  EstadoCita,
  aFecha,
  aFechaApi,
  aIso,
  armarNotas,
  claveDia,
  formatoDinero,
} from '../../models/cita';
import { ApiService } from '../../services/api.service';
import { SessionService } from '../../services/session.service';

const capitalizar = (t: string) => t.charAt(0).toUpperCase() + t.slice(1);

/**
 * Detalle de una cita en un modal (reemplaza a la antigua página /gestion-citas/:id, que llamaba a
 * GET /tenant/appointments/{id}: ese endpoint NO existe en el backend, por eso nunca cargaba).
 * Recibe la cita ya cargada desde la agenda.
 *
 * Endpoints usados:
 *   PATCH /tenant/appointments/{id}/status   { status }
 *   PUT   /tenant/appointments/{id}          { notes } | { appointment_time }
 */
@Component({
  selector: 'app-detalle-cita-modal',
  standalone: true,
  imports: [IonContent, IonIcon, IonSpinner, SelectorHorasComponent],
  templateUrl: './detalle-cita.modal.html',
  styleUrls: ['./detalle-cita.modal.scss'],
})
export class DetalleCitaModal {
  private readonly modal = inject(ModalController);
  private readonly api = inject(ApiService);
  private readonly alertCtrl = inject(AlertController);
  private readonly toastCtrl = inject(ToastController);
  private readonly sesion = inject(SessionService);

  readonly c = signal<Cita | null>(null);

  @Input() set cita(valor: Cita) {
    this.c.set(valor);
  }

  /** La agenda se refresca llamando a este callback cada vez que algo cambia. */
  @Input() alCambiar?: () => void;

  readonly negocio = this.sesion.negocio;
  readonly slug = this.sesion.slug;
  readonly hoy = claveDia(new Date());

  readonly guardando = signal(false);
  readonly etiquetas = ETIQUETAS_ESTADO;

  // Edición de nota
  readonly editandoNota = signal(false);
  readonly borradorNota = signal('');

  // Reprogramación
  readonly reprogramando = signal(false);
  readonly nuevaFecha = signal('');
  readonly nuevaHora = signal('');
  readonly errorReprogramar = signal('');

  readonly iniciales = computed(() => {
    const partes = (this.c()?.cliente ?? '').trim().split(/\s+/).filter(Boolean);
    if (partes.length >= 2) return (partes[0][0] + partes[1][0]).toUpperCase();
    return (partes[0] ?? 'CL').slice(0, 2).toUpperCase();
  });

  readonly cerrada = computed(() => {
    const e = this.c()?.estado;
    return e === 'completada' || e === 'cancelada';
  });

  readonly precio = computed(() => formatoDinero(this.c()?.precio ?? 0));

  readonly fechaLarga = computed(() => {
    const c = this.c();
    if (!c) return '';
    return capitalizar(
      new Intl.DateTimeFormat('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(
        aFecha(c.inicio),
      ),
    );
  });

  readonly rangoHoras = computed(() => {
    const c = this.c();
    if (!c) return '';
    const fmt = (iso: string) => iso.slice(11, 16);
    return c.duracionMin > 0 ? `${fmt(c.inicio)} – ${fmt(c.fin)}` : fmt(c.inicio);
  });

  readonly creadaEn = computed(() => {
    const raw = this.c()?.creadaEn;
    if (!raw) return '';
    const f = new Date(raw);
    if (isNaN(+f)) return '';
    return `Creada el ${new Intl.DateTimeFormat('es-ES', { dateStyle: 'short', timeStyle: 'short' }).format(f)}`;
  });

  /** Staff para filtrar la disponibilidad al reprogramar ('' = no filtrar) */
  readonly staffParaDisponibilidad = computed(() => {
    const p = this.c()?.profesional ?? '';
    return p === 'Sin asignar' ? '' : p;
  });

  constructor() {
    addIcons({
      calendarClearOutline,
      checkmark,
      checkmarkDone,
      close,
      informationCircleOutline,
      personOutline,
      timeOutline,
    });
  }

  cerrar(): void {
    this.modal.dismiss(null, 'cerrar');
  }

  private async aviso(mensaje: string, color: 'success' | 'danger' = 'success'): Promise<void> {
    const t = await this.toastCtrl.create({ message: mensaje, duration: 2200, position: 'top', color });
    await t.present();
  }

  private actualizar(parche: Partial<Cita>): void {
    const actual = this.c();
    if (actual) this.c.set({ ...actual, ...parche });
    this.alCambiar?.();
  }

  /* ---------- Estado ---------- */

  cambiarEstado(nuevo: EstadoCita): void {
    const c = this.c();
    if (!c || this.guardando()) return;
    this.guardando.set(true);

    this.api.patch(`/tenant/appointments/${c.id}/status`, { status: ESTADO_HACIA_API[nuevo] }).subscribe({
      next: () => {
        this.guardando.set(false);
        this.actualizar({ estado: nuevo });
        this.aviso(`Cita ${ETIQUETAS_ESTADO[nuevo].toLowerCase()}`);
      },
      error: (err) => {
        this.guardando.set(false);
        this.aviso(ApiService.mensajeError(err, 'No se pudo cambiar el estado.'), 'danger');
      },
    });
  }

  confirmar(): void {
    this.cambiarEstado('confirmada');
  }

  completar(): void {
    this.cambiarEstado('completada');
  }

  async cancelar(): Promise<void> {
    const alerta = await this.alertCtrl.create({
      header: 'Cancelar cita',
      message: '¿Seguro que deseas cancelar esta cita?',
      buttons: [
        { text: 'No', role: 'cancel' },
        { text: 'Sí, cancelar', role: 'destructive', handler: () => this.cambiarEstado('cancelada') },
      ],
    });
    await alerta.present();
  }

  /* ---------- Nota ---------- */

  empezarNota(): void {
    this.borradorNota.set(this.c()?.notas ?? '');
    this.editandoNota.set(true);
  }

  guardarNota(): void {
    const c = this.c();
    if (!c || this.guardando()) return;
    const texto = this.borradorNota().trim();
    this.guardando.set(true);

    // Para clientes externos el nombre vive dentro de `notes`, así que se conserva el prefijo.
    this.api.put(`/tenant/appointments/${c.id}`, { notes: armarNotas(c, texto) || null }).subscribe({
      next: () => {
        this.guardando.set(false);
        this.editandoNota.set(false);
        this.actualizar({ notas: texto });
        this.aviso('Nota guardada');
      },
      error: (err) => {
        this.guardando.set(false);
        this.aviso(ApiService.mensajeError(err, 'No se pudo guardar la nota.'), 'danger');
      },
    });
  }

  /* ---------- Reprogramar ---------- */

  empezarReprogramar(): void {
    const c = this.c();
    if (!c) return;
    this.nuevaFecha.set(c.inicio.slice(0, 10) >= this.hoy ? c.inicio.slice(0, 10) : this.hoy);
    this.nuevaHora.set('');
    this.errorReprogramar.set('');
    this.reprogramando.set(true);
  }

  cambiarNuevaFecha(ev: Event): void {
    this.nuevaFecha.set((ev.target as HTMLInputElement).value);
    this.nuevaHora.set('');
  }

  guardarReprogramacion(): void {
    const c = this.c();
    if (!c || !this.nuevaFecha() || !this.nuevaHora() || this.guardando()) return;
    this.guardando.set(true);
    this.errorReprogramar.set('');

    this.api
      .put(`/tenant/appointments/${c.id}`, { appointment_time: aFechaApi(this.nuevaFecha(), this.nuevaHora()) })
      .subscribe({
        next: () => {
          this.guardando.set(false);
          this.reprogramando.set(false);
          const inicio = aFecha(`${this.nuevaFecha()} ${this.nuevaHora()}`);
          this.actualizar({
            inicio: aIso(inicio),
            fin: aIso(new Date(+inicio + c.duracionMin * 60000)),
          });
          this.aviso('Cita reprogramada');
        },
        error: (err) => {
          this.guardando.set(false);
          this.errorReprogramar.set(ApiService.mensajeError(err, 'No se pudo reprogramar la cita.'));
        },
      });
  }
}
