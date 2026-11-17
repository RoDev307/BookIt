import { Component, effect, inject, input, model, signal, untracked } from '@angular/core';
import { IonSpinner } from '@ionic/angular';
import { Subscription } from 'rxjs';
import { hora12 } from '../../models/cita';
import { ApiService } from '../../services/api.service';

interface Slot {
  time: string; // HH:mm
  available: boolean;
  reason: string;
}

/**
 * Cuadrícula de horas libres para una fecha.
 * Usa el endpoint público del backend:  GET /businesses/{slug}/availability?date=YYYY-MM-DD[&staff_name=...]
 * (el backend genera bloques de 1 hora, marca ocupados/pasados y avisa si el negocio cierra ese día).
 */
@Component({
  selector: 'app-selector-horas',
  standalone: true,
  imports: [IonSpinner],
  templateUrl: './selector-horas.component.html',
  styleUrls: ['./selector-horas.component.scss'],
})
export class SelectorHorasComponent {
  private readonly api = inject(ApiService);
  private sub?: Subscription;

  readonly slug = input.required<string>();
  readonly fecha = input<string>('');
  readonly staff = input<string>('');
  /** Hora elegida en formato HH:mm (two-way: [(hora)]) */
  readonly hora = model<string>('');

  readonly cargando = signal(false);
  readonly abierto = signal(true);
  readonly motivo = signal('');
  readonly errorCarga = signal('');
  readonly slots = signal<Slot[]>([]);
  readonly otraHora = signal(false);

  readonly etiqueta = hora12;

  constructor() {
    effect(() => {
      const slug = this.slug();
      const fecha = this.fecha();
      const staff = this.staff().trim();
      untracked(() => this.cargar(slug, fecha, staff));
    });
  }

  private cargar(slug: string, fecha: string, staff: string): void {
    this.sub?.unsubscribe();
    this.errorCarga.set('');
    this.motivo.set('');
    if (!fecha) {
      this.slots.set([]);
      return;
    }
    if (!slug) {
      this.errorCarga.set('Tu usuario no tiene un negocio asignado.');
      return;
    }

    this.cargando.set(true);
    this.sub = this.api
      .get(`/businesses/${slug}/availability`, { date: fecha, staff_name: staff || undefined })
      .subscribe({
        next: (res) => {
          const d = res?.data ?? {};
          this.abierto.set(d.is_open !== false);
          this.motivo.set(d.reason ?? '');
          const slots: Slot[] = Array.isArray(d.available_slots) ? d.available_slots : [];
          this.slots.set(slots);
          // Si la hora elegida ya no está libre, se limpia
          const actual = this.hora();
          const slot = slots.find((s) => s.time === actual);
          if (slot && !slot.available) this.hora.set('');
          this.cargando.set(false);
        },
        error: (err) => {
          this.slots.set([]);
          this.abierto.set(true);
          this.errorCarga.set(ApiService.mensajeError(err, 'No se pudo cargar la disponibilidad.'));
          this.cargando.set(false);
        },
      });
  }

  elegir(time: string): void {
    this.otraHora.set(false);
    this.hora.set(time);
  }

  activarOtra(): void {
    this.otraHora.set(true);
    this.hora.set('');
  }

  volverABloques(): void {
    this.otraHora.set(false);
    this.hora.set('');
  }

  onHoraLibre(event: Event): void {
    this.hora.set((event.target as HTMLInputElement).value.slice(0, 5));
  }
}
