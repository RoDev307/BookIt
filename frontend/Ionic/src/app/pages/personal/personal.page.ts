import { Component, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent } from '@ionic/angular';
import { ApiService } from '../../services/api.service';
interface Empleado {
  id: number;
  name: string;
  position: string | null;
  phone: string | null;
  email: string | null;
}
type Datos = Omit<Empleado, 'id'>;
@Component({
  selector: 'app-personal',
  standalone: true,
  imports: [CommonModule, FormsModule, IonContent],
  templateUrl: './personal.page.html',
  styleUrls: ['./personal.page.scss'],
})
export class PersonalPage {
  empleados: Empleado[] = [];
  busqueda = '';
  cargando = false;
  guardando = false;
  error = '';
  formulario = false;
  editando: number | null = null;
  datos: Datos = this.vacio();
  constructor(
    private api: ApiService,
    private cdr: ChangeDetectorRef,
  ) {}
  ionViewWillEnter() {
    this.cargar();
  }
  vacio(): Datos {
    return { name: '', position: '', phone: '', email: '' };
  }
  get visibles() {
    const q = this.busqueda.trim().toLowerCase();
    return this.empleados.filter((e) =>
      [e.name, e.position, e.email, e.phone].some((v) => v?.toLowerCase().includes(q)),
    );
  }
  cargar() {
    this.cargando = true;
    this.error = '';
    this.api.get<{ data: Empleado[] }>('/tenant/staff').subscribe({
      next: (r) => {
        this.empleados = r.data;
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (e) => {
        this.error = e.error?.message || 'No se pudo cargar el personal. Verifica tu sesión y la conexión.';
        this.cargando = false;
        this.cdr.detectChanges();
      },
    });
  }
  nuevo() {
    this.editando = null;
    this.datos = this.vacio();
    this.error = '';
    this.formulario = true;
  }
  editar(e: Empleado) {
    this.editando = e.id;
    this.datos = { name: e.name, position: e.position, phone: e.phone, email: e.email };
    this.error = '';
    this.formulario = true;
  }
  guardar() {
    if (!this.datos.name.trim() || this.guardando) return;
    this.guardando = true;
    this.error = '';
    const data = { ...this.datos, name: this.datos.name.trim() };
    const req =
      this.editando === null
        ? this.api.post('/tenant/staff', data)
        : this.api.put(`/tenant/staff/${this.editando}`, data);
    req.subscribe({
      next: () => {
        this.guardando = false;
        this.formulario = false;
        this.cargar();
      },
      error: (e) => {
        this.guardando = false;
        this.error = e.error?.message || 'No se pudieron guardar los datos.';
        this.cdr.detectChanges();
      },
    });
  }
  eliminar(e: Empleado) {
    if (!confirm(`¿Eliminar a ${e.name} del personal?`)) return;
    this.error = '';
    this.api.delete(`/tenant/staff/${e.id}`).subscribe({
      next: () => this.cargar(),
      error: (err) => {
        this.error = err.error?.message || 'No se pudo eliminar el empleado.';
        this.cdr.detectChanges();
      },
    });
  }
}
