import { Component, Input, OnInit, computed, inject, signal } from '@angular/core';
import { IonContent, IonIcon, IonSpinner, ModalController } from '@ionic/angular';
import { addIcons } from 'ionicons';
import { alertCircleOutline, close } from 'ionicons/icons';
import { SelectorHorasComponent } from '../../components/selector-horas/selector-horas.component';
import { claveDia, formatoDinero } from '../../models/cita';
import { ApiService } from '../../services/api.service';
import { SessionService } from '../../services/session.service';

interface ServicioOpcion {
  id: number;
  nombre: string;
  precio: number;
  duracion: number;
}

const K_STAFF = 'bookit_ultimo_staff';

/**
 * Agendamiento manual (equivalente a "Agendar Cita" del panel Laravel).
 * Endpoint: POST /tenant/appointments
 *   { service_id, client_name, staff_name, fecha_cita: 'YYYY-MM-DD', hora_cita: 'HH:mm', notes? }
 */
@Component({
  selector: 'app-nueva-cita-modal',
  standalone: true,
  imports: [IonContent, IonIcon, IonSpinner, SelectorHorasComponent],
  templateUrl: './nueva-cita.modal.html',
  styleUrls: ['./nueva-cita.modal.scss'],
})
export class NuevaCitaModal implements OnInit {
  private readonly modal = inject(ModalController);
  private readonly api = inject(ApiService);
  private readonly sesion = inject(SessionService);

  /** Día seleccionado en la agenda (YYYY-MM-DD), se usa como fecha inicial. */
  @Input() fechaInicial = '';

  readonly slug = this.sesion.slug;
  readonly hoy = claveDia(new Date());

  readonly servicios = signal<ServicioOpcion[]>([]);
  readonly cargandoServicios = signal(true);
  readonly errorServicios = signal('');

  readonly cliente = signal('');
  readonly servicioId = signal<number | null>(null);
  readonly staff = signal(localStorage.getItem(K_STAFF) ?? '');
  readonly fecha = signal('');
  readonly hora = signal('');
  readonly notas = signal('');

  readonly guardando = signal(false);
  readonly error = signal('');

  readonly valido = computed(
    () =>
      !!this.cliente().trim() &&
      this.servicioId() !== null &&
      !!this.staff().trim() &&
      !!this.fecha() &&
      !!this.hora(),
  );

  constructor() {
    addIcons({ close, alertCircleOutline });
  }

  ngOnInit(): void {
    this.fecha.set(this.fechaInicial && this.fechaInicial >= this.hoy ? this.fechaInicial : this.hoy);

    this.api.get('/tenant/services').subscribe({
      next: (res) => {
        const lista = ApiService.lista(res)
          .filter((s: any) => Boolean(Number(s.is_active ?? 1)))
          .map((s: any) => ({
            id: s.id,
            nombre: s.name,
            precio: Number(s.price ?? 0),
            duracion: Number(s.duration_minutes ?? 0),
          }));
        this.servicios.set(lista);
        this.cargandoServicios.set(false);
      },
      error: (err) => {
        this.errorServicios.set(ApiService.mensajeError(err, 'No se pudieron cargar los servicios.'));
        this.cargandoServicios.set(false);
      },
    });
  }

  etiquetaServicio(s: ServicioOpcion): string {
    return `${s.nombre} · ${formatoDinero(s.precio)} · ${s.duracion} min`;
  }

  texto(sig: { set(v: string): void }, ev: Event): void {
    sig.set((ev.target as HTMLInputElement | HTMLTextAreaElement).value);
  }

  elegirServicio(ev: Event): void {
    const v = (ev.target as HTMLSelectElement).value;
    this.servicioId.set(v ? Number(v) : null);
  }

  cambiarFecha(ev: Event): void {
    this.fecha.set((ev.target as HTMLInputElement).value);
    this.hora.set('');
  }

  cerrar(): void {
    this.modal.dismiss(null, 'cancel');
  }

  guardar(): void {
    if (!this.valido() || this.guardando()) return;
    this.guardando.set(true);
    this.error.set('');

    const body = {
      service_id: this.servicioId(),
      client_name: this.cliente().trim(),
      staff_name: this.staff().trim(),
      fecha_cita: this.fecha(),
      hora_cita: this.hora(),
      notes: this.notas().trim() || null,
    };

    this.api.post('/tenant/appointments', body).subscribe({
      next: () => {
        localStorage.setItem(K_STAFF, this.staff().trim());
        this.modal.dismiss({ fecha: this.fecha() }, 'creada');
      },
      error: (err) => {
        this.guardando.set(false);
        this.error.set(ApiService.mensajeError(err, 'No se pudo agendar la cita.'));
      },
    });
  }
}
