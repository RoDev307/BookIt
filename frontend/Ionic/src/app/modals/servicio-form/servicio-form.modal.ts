import { Component, Input, OnInit, computed, inject, signal } from '@angular/core';
import { AlertController, IonContent, IonIcon, IonSpinner, IonToggle, ModalController } from '@ionic/angular';
import { addIcons } from 'ionicons';
import { alertCircleOutline, close, trashOutline } from 'ionicons/icons';
import { ApiService } from '../../services/api.service';

export interface Servicio {
  id: number;
  nombre: string;
  descripcion: string;
  duracion_min: number;
  precio: number;
  activo: boolean;
  citas: number; // appointments_count
}

/**
 * Crear / editar servicio.
 *   POST   /tenant/services        { name, description?, price, duration_minutes, is_active }
 *   PUT    /tenant/services/{id}   (name, price y duration_minutes son OBLIGATORIOS en el backend)
 *   DELETE /tenant/services/{id}
 */
@Component({
  selector: 'app-servicio-form-modal',
  standalone: true,
  imports: [IonContent, IonIcon, IonSpinner, IonToggle],
  templateUrl: './servicio-form.modal.html',
  styleUrls: ['./servicio-form.modal.scss'],
})
export class ServicioFormModal implements OnInit {
  private readonly modal = inject(ModalController);
  private readonly api = inject(ApiService);
  private readonly alertCtrl = inject(AlertController);

  /** Si viene un servicio es edición; si no, creación. */
  @Input() servicio?: Servicio;

  readonly nombre = signal('');
  readonly descripcion = signal('');
  readonly precio = signal('');
  readonly duracion = signal('');
  readonly activo = signal(true);

  readonly guardando = signal(false);
  readonly error = signal('');

  readonly esEdicion = computed(() => !!this.servicio);

  readonly valido = computed(() => {
    const p = Number(this.precio());
    const d = Number(this.duracion());
    return (
      !!this.nombre().trim() &&
      this.precio() !== '' &&
      !isNaN(p) &&
      p >= 0 &&
      Number.isInteger(d) &&
      d >= 1
    );
  });

  constructor() {
    addIcons({ close, alertCircleOutline, trashOutline });
  }

  ngOnInit(): void {
    const s = this.servicio;
    if (s) {
      this.nombre.set(s.nombre);
      this.descripcion.set(s.descripcion);
      this.precio.set(String(s.precio));
      this.duracion.set(String(s.duracion_min));
      this.activo.set(s.activo);
    } else {
      this.duracion.set('30');
    }
  }

  texto(sig: { set(v: string): void }, ev: Event): void {
    sig.set((ev.target as HTMLInputElement | HTMLTextAreaElement).value);
  }

  cerrar(): void {
    this.modal.dismiss(null, 'cancel');
  }

  guardar(): void {
    if (!this.valido() || this.guardando()) return;
    this.guardando.set(true);
    this.error.set('');

    const body = {
      name: this.nombre().trim(),
      description: this.descripcion().trim() || null,
      price: Number(this.precio()),
      duration_minutes: Number(this.duracion()),
      is_active: this.activo(),
    };

    const peticion = this.servicio
      ? this.api.put(`/tenant/services/${this.servicio.id}`, body)
      : this.api.post('/tenant/services', body);

    peticion.subscribe({
      next: () => this.modal.dismiss(null, this.servicio ? 'editado' : 'creado'),
      error: (err) => {
        this.guardando.set(false);
        this.error.set(ApiService.mensajeError(err, 'No se pudo guardar el servicio.'));
      },
    });
  }

  async eliminar(): Promise<void> {
    const s = this.servicio;
    if (!s) return;

    // En la BD las citas tienen FK con ON DELETE CASCADE hacia services:
    // al borrar un servicio también se borran todas sus citas.
    const aviso =
      s.citas > 0
        ? `Este servicio tiene ${s.citas} cita(s) registradas. Si lo eliminas, esas citas también se borrarán de forma permanente. Si solo quieres dejar de ofrecerlo, desactívalo.`
        : 'Esta acción no se puede deshacer.';

    const alerta = await this.alertCtrl.create({
      header: `¿Eliminar "${s.nombre}"?`,
      message: aviso,
      buttons: [
        { text: 'Cancelar', role: 'cancel' },
        { text: 'Eliminar', role: 'destructive', handler: () => this.confirmarEliminar(s.id) },
      ],
    });
    await alerta.present();
  }

  private confirmarEliminar(id: number): void {
    this.guardando.set(true);
    this.error.set('');
    this.api.delete(`/tenant/services/${id}`).subscribe({
      next: () => this.modal.dismiss(null, 'eliminado'),
      error: (err) => {
        this.guardando.set(false);
        this.error.set(ApiService.mensajeError(err, 'No se pudo eliminar el servicio.'));
      },
    });
  }
}
