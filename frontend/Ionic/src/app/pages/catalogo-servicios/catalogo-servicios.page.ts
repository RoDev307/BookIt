import { Component, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent, IonIcon, IonToggle } from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  add,
  calendarOutline,
  informationCircleOutline,
  notificationsOutline,
  searchOutline,
  timeOutline,
  sparklesOutline,
} from 'ionicons/icons';
import { ApiService } from '../../services/api.service';
interface Servicio {
  id: number;
  name: string;
  description: string | null;
  duration_minutes: number;
  price: number;
  is_active: boolean | number;
}
@Component({
  selector: 'app-catalogo-servicios',
  standalone: true,
  imports: [CommonModule, FormsModule, IonContent, IonIcon, IonToggle],
  templateUrl: './catalogo-servicios.page.html',
  styleUrls: ['./catalogo-servicios.page.scss'],
})
export class CatalogoServiciosPage {
  servicios: Servicio[] = [];
  busqueda = '';
  cargando = true;
  guardando = false;
  error = '';
  exito = '';
  formulario = false;
  editando: number | null = null;
  ordenado = false;
  datos = { name: '', description: '', price: 0, duration_minutes: 30, is_active: true };
  constructor(
    private api: ApiService,
    private cdr: ChangeDetectorRef,
  ) {
    addIcons({
      add,
      calendarOutline,
      informationCircleOutline,
      notificationsOutline,
      searchOutline,
      timeOutline,
      sparklesOutline,
    });
  }
  ionViewWillEnter() {
    this.cargar();
  }
  get visibles() {
    const q = this.busqueda.toLowerCase().trim();
    const list = this.servicios.filter(
      (s) => s.name.toLowerCase().includes(q) || (s.description || '').toLowerCase().includes(q),
    );
    return this.ordenado ? list.sort((a, b) => a.name.localeCompare(b.name)) : list;
  }
  cargar() {
    this.cargando = true;
    this.error = '';
    this.api.get<{ data: Servicio[] }>('/tenant/services').subscribe({
      next: (r) => {
        this.servicios = r.data.map((s) => ({ ...s, is_active: !!Number(s.is_active) }));
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (e) => {
        this.cargando = false;
        this.error = e.error?.message || 'No se pudieron cargar los servicios.';
        this.cdr.detectChanges();
      },
    });
  }
  nuevo() {
    this.editando = null;
    this.datos = { name: '', description: '', price: 0, duration_minutes: 30, is_active: true };
    this.formulario = true;
    this.error = '';
    this.exito = '';
  }
  abrir(s: Servicio) {
    this.editando = s.id;
    this.datos = {
      name: s.name,
      description: s.description || '',
      price: Number(s.price),
      duration_minutes: Number(s.duration_minutes),
      is_active: !!Number(s.is_active),
    };
    this.formulario = true;
    this.error = '';
    this.exito = '';
  }
  guardar() {
    if (this.guardando || !this.datos.name.trim() || this.datos.price < 0 || this.datos.duration_minutes < 1) return;
    this.guardando = true;
    this.error = '';
    const data = {
      ...this.datos,
      name: this.datos.name.trim(),
      price: Number(this.datos.price),
      duration_minutes: Number(this.datos.duration_minutes),
    };
    const req =
      this.editando === null
        ? this.api.post('/tenant/services', data)
        : this.api.put(`/tenant/services/${this.editando}`, data);
    req.subscribe({
      next: () => {
        this.guardando = false;
        this.formulario = false;
        this.exito = 'Servicio guardado.';
        this.cargar();
      },
      error: (e) => {
        this.guardando = false;
        this.error = e.error?.message || 'No se pudo guardar el servicio.';
        this.cdr.detectChanges();
      },
    });
  }
  toggleActivo(s: Servicio, event: any) {
    const nuevo = event.detail.checked;
    const anterior = !!Number(s.is_active);
    if (nuevo === anterior) return;
    s.is_active = nuevo;
    this.api
      .put(`/tenant/services/${s.id}`, {
        name: s.name,
        description: s.description,
        price: Number(s.price),
        duration_minutes: Number(s.duration_minutes),
        is_active: nuevo,
      })
      .subscribe({
        next: () => this.cdr.detectChanges(),
        error: (e) => {
          s.is_active = anterior;
          this.error = e.error?.message || 'No se pudo cambiar el estado del servicio.';
          this.cdr.detectChanges();
        },
      });
  }
  precio(valor: number) {
    return new Intl.NumberFormat('es-SV', { style: 'currency', currency: 'USD' }).format(valor);
  }
}
