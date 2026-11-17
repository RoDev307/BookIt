import { Component, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IonContent } from '@ionic/angular';
import { ApiService } from '../../services/api.service';
interface Notificacion {
  id: number;
  title: string;
  message: string;
  created_at: string;
  read_at: string | null;
}
@Component({
  selector: 'app-notificaciones',
  standalone: true,
  imports: [CommonModule, IonContent],
  templateUrl: './notificaciones.page.html',
  styleUrls: ['./notificaciones.page.scss'],
})
export class NotificacionesPage {
  notificaciones: Notificacion[] = [];
  soloNoLeidas = false;
  cargando = true;
  error = '';
  procesando: number | null = null;
  constructor(
    private api: ApiService,
    private cdr: ChangeDetectorRef,
  ) {}
  get visibles() {
    return this.soloNoLeidas ? this.notificaciones.filter((n) => !n.read_at) : this.notificaciones;
  }
  ionViewWillEnter() {
    this.soloNoLeidas = localStorage.getItem('bookit_solo_no_leidas') === 'true';
    this.cargar();
  }
  cargar() {
    this.cargando = true;
    this.error = '';
    this.api.get<{ data: Notificacion[] }>('/tenant/notifications').subscribe({
      next: (r) => {
        this.notificaciones = r.data;
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (e) => {
        this.error = e.error?.message || 'No se pudieron cargar las notificaciones.';
        this.cargando = false;
        this.cdr.detectChanges();
      },
    });
  }
  leer(n: Notificacion) {
    if (n.read_at || this.procesando !== null) return;
    this.procesando = n.id;
    this.api.patch<{ data: Notificacion }>(`/tenant/notifications/${n.id}/read`).subscribe({
      next: (r) => {
        n.read_at = r.data.read_at;
        this.procesando = null;
        this.cdr.detectChanges();
      },
      error: (e) => {
        this.procesando = null;
        this.error = e.error?.message || 'No se pudo actualizar la notificación.';
        this.cdr.detectChanges();
      },
    });
  }
}
