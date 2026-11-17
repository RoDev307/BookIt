import { Component, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterLink } from '@angular/router';
import { IonContent } from '@ionic/angular';
import { catchError, forkJoin, of } from 'rxjs';
import { ApiService } from '../../services/api.service';
interface Cita {
  id: number;
  status: string;
  appointment_time: string;
  staff_name: string | null;
  notes: string | null;
  user?: { name: string } | null;
  service?: { name: string; price: number | string; duration_minutes: number } | null;
}
interface Servicio {
  id: number;
  is_active: boolean | number;
}
interface Empleado {
  id: number;
}
interface Dia {
  fecha: string;
  nombre: string;
  numero: string;
  total: number;
  altura: number;
}
@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, RouterLink, IonContent],
  templateUrl: './dashboard.page.html',
  styleUrls: ['./dashboard.page.scss'],
})
export class DashboardPage {
  cargando = true;
  error = '';
  negocio = 'Tu negocio';
  citas: Cita[] = [];
  servicios: Servicio[] = [];
  personal: Empleado[] = [];
  constructor(
    private api: ApiService,
    private cdr: ChangeDetectorRef,
  ) {}
  ionViewWillEnter() {
    this.cargar();
  }
  cargar() {
    this.cargando = true;
    this.error = '';
    forkJoin({
      business: this.api.get<any>('/tenant/business'),
      appointments: this.api.get<any>('/tenant/appointments'),
      services: this.api.get<any>('/tenant/services'),
      staff: this.api.get<any>('/tenant/staff').pipe(catchError(() => of(null))),
    }).subscribe({
      next: (r) => {
        this.negocio = r.business.data?.name || 'Tu negocio';
        this.citas = Array.isArray(r.appointments.data) ? r.appointments.data : [];
        this.servicios = Array.isArray(r.services.data) ? r.services.data : [];
        this.personal = Array.isArray(r.staff?.data) ? r.staff.data : [];
        this.personalDisponible = !!r.staff;
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (e) => {
        this.cargando = false;
        this.error = e.error?.message || 'No se pudo cargar el resumen. Comprueba tu sesión y conexión.';
        this.cdr.detectChanges();
      },
    });
  }
  personalDisponible = true;
  private clave(fecha: Date) {
    return `${fecha.getFullYear()}-${String(fecha.getMonth() + 1).padStart(2, '0')}-${String(fecha.getDate()).padStart(2, '0')}`;
  }
  get hoy() {
    return this.clave(new Date());
  }
  get citasHoy() {
    return this.citas.filter((c) => c.appointment_time?.slice(0, 10) === this.hoy && c.status !== 'cancelled');
  }
  get pendientes() {
    return this.citas.filter((c) => c.status === 'pending').length;
  }
  get confirmadas() {
    return this.citas.filter((c) => c.status === 'confirmed').length;
  }
  get completadas() {
    return this.citas.filter((c) => c.status === 'completed').length;
  }
  get canceladas() {
    return this.citas.filter((c) => c.status === 'cancelled').length;
  }
  get ingresosPrevistos() {
    return this.citasHoy.reduce((total, c) => total + Number(c.service?.price || 0), 0);
  }
  get minutosHoy() {
    return this.citasHoy.reduce((total, c) => total + Number(c.service?.duration_minutes || 0), 0);
  }
  get serviciosActivos() {
    return this.servicios.filter((s) => !!Number(s.is_active)).length;
  }
  get estados() {
    return [
      { nombre: 'Confirmadas', valor: this.confirmadas, color: '#6c63ff' },
      { nombre: 'Pendientes', valor: this.pendientes, color: '#d6aa65' },
      { nombre: 'Completadas', valor: this.completadas, color: '#6fb596' },
      { nombre: 'Canceladas', valor: this.canceladas, color: '#d0818c' },
    ];
  }
  get escala() {
    return Math.max(4, ...this.dias.map((d) => d.total));
  }
  get dias(): Dia[] {
    const base = new Date();
    base.setHours(0, 0, 0, 0);
    const dias = Array.from({ length: 7 }, (_, i) => {
      const d = new Date(base);
      d.setDate(base.getDate() + i);
      const fecha = this.clave(d);
      return {
        fecha,
        nombre: new Intl.DateTimeFormat('es-SV', { weekday: 'short' }).format(d).replace('.', ''),
        numero: String(d.getDate()),
        total: this.citas.filter((c) => c.status !== 'cancelled' && c.appointment_time?.slice(0, 10) === fecha).length,
        altura: 0,
      };
    });
    const max = Math.max(4, ...dias.map((d) => d.total));
    return dias.map((d) => ({ ...d, altura: d.total ? Math.max(7, (d.total / max) * 100) : 0 }));
  }
  get proximas() {
    const ahora = new Date();
    const hoy = this.hoy;
    const hora = `${String(ahora.getHours()).padStart(2, '0')}:${String(ahora.getMinutes()).padStart(2, '0')}`;
    return this.citas
      .filter(
        (c) =>
          ['pending', 'confirmed'].includes(c.status) &&
          !!c.appointment_time &&
          (c.appointment_time.slice(0, 10) > hoy ||
            (c.appointment_time.slice(0, 10) === hoy && c.appointment_time.slice(11, 16) >= hora)),
      )
      .sort((a, b) => a.appointment_time.localeCompare(b.appointment_time))
      .slice(0, 4);
  }
  cliente(c: Cita) {
    return c.notes?.startsWith('Cliente Externo:') ? c.notes.slice(16).split('|')[0].trim() : c.user?.name || 'Cliente';
  }
  fecha(c: Cita) {
    const v = c.appointment_time;
    const [year, month, day] = v.slice(0, 10).split('-').map(Number);
    const date = new Date(year, month - 1, day);
    return `${new Intl.DateTimeFormat('es-SV', { day: 'numeric', month: 'short' }).format(date)} · ${v.slice(11, 16)}`;
  }
  dinero(n: number) {
    return new Intl.NumberFormat('es-SV', { style: 'currency', currency: 'USD' }).format(n);
  }
  tiempo(m: number) {
    return `${Math.floor(m / 60)} h ${m % 60} min`;
  }
}
