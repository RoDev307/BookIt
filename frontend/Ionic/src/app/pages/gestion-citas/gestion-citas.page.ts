import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule, Location } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { IonContent, IonIcon, IonSpinner } from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  calendarOutline,
  notificationsOutline,
  arrowBack,
  ellipsisHorizontal,
  chatbubbleOutline,
  callOutline,
  calendarClearOutline,
  personOutline,
  timeOutline,
  checkmark,
  checkmarkDone,
  close,
  informationCircleOutline,
} from 'ionicons/icons';
import { BusinessClient, ClientsService } from '../../services/clients.service';
import { ApiService } from '../../services/api.service';

export interface CitaDetalle {
  id: number;
  codigo: string;
  local?: string;
  estado: 'pendiente' | 'confirmada' | 'completada' | 'cancelada';
  notas?: string;
  duracion: number;
  precio: number;
  fechaHoraRaw: string;
  creadaEnRaw?: string;
  cliente: {
    nombre: string;
    email?: string;
    telefono?: string;
  };
  servicio: {
    nombre: string;
    precio: number;
  };
  profesional?: {
    nombre: string;
  };
}

@Component({
  selector: 'app-gestion-citas',
  templateUrl: './gestion-citas.page.html',
  styleUrls: ['./gestion-citas.page.scss'],
  standalone: true,
  imports: [CommonModule, FormsModule, IonContent, IonIcon, IonSpinner],
})
export class GestionCitasPage implements OnInit {
  cargando: boolean = true;
  guardando: boolean = false;
  error: boolean = false;
  errorMsg: string = '';
  citaId: number | null = null;
  cita: CitaDetalle | null = null;
  clienteExterno = false;
  modoCliente: 'nuevo' | 'buscar' = 'nuevo';
  clienteSeleccionado: BusinessClient | null = null;
  nuevoContacto = { phone: '', email: '' };
  busquedaCliente = '';
  resultadosClientes: BusinessClient[] = [];
  buscandoClientes = false;
  errorClientes = '';
  busquedaRealizada = false;
  private busquedaId = 0;
  clientesUltimaPagina = 1;
  clientesPaginaActual = 1;
  private clienteBusquedaActiva = '';

  servicios: any[] = [];
  personal: any[] = [];
  nueva = { service_id: null as number | null, client_name: '', staff_name: '', fecha: '', hora: '', notes: '' };

  private readonly ESTADOS_API: Record<string, 'pendiente' | 'confirmada' | 'completada' | 'cancelada'> = {
    pending: 'pendiente',
    confirmed: 'confirmada',
    completed: 'completada',
    cancelled: 'cancelada',
    canceled: 'cancelada',
  };

  private readonly ETIQUETAS: Record<string, string> = {
    pendiente: 'Pendiente',
    confirmada: 'Confirmada',
    completada: 'Completada',
    cancelada: 'Cancelada',
  };

  constructor(
    private api: ApiService,
    private clients: ClientsService,
    private route: ActivatedRoute,
    private router: Router,
    private location: Location,
    private cdr: ChangeDetectorRef,
  ) {
    addIcons({
      calendarOutline,
      notificationsOutline,
      arrowBack,
      ellipsisHorizontal,
      chatbubbleOutline,
      callOutline,
      calendarClearOutline,
      personOutline,
      timeOutline,
      checkmark,
      checkmarkDone,
      close,
      informationCircleOutline,
    });
  }

  ngOnInit() {
    const idParam = this.route.snapshot.paramMap.get('id');
    this.citaId = idParam ? Number(idParam) : null;
    this.cargar();
    const clientId = Number(this.route.snapshot.queryParamMap.get('client_id'));
    if (!this.citaId && Number.isInteger(clientId) && clientId > 0) {
      this.modoCliente = 'buscar';
      this.clients.get(clientId).subscribe({
        next: r => this.seleccionarCliente(r.data),
        error: () => { this.errorClientes = 'No se pudo cargar la ficha. Busca al cliente nuevamente.'; this.cdr.detectChanges(); },
      });
    }
  }

  cargar() {
    if (!this.citaId) {
      this.cargando = false;
      this.cargarOpciones();
      return;
    }

    this.cargando = true;
    this.error = false;

    this.api.get<any>(`/tenant/appointments/${this.citaId}`).subscribe({
      next: (res) => {
        const raw = res.data ?? res;
        const cli = raw.client ?? raw.user ?? raw.customer ?? {};
        const srv = raw.service ?? raw.servicio ?? {};
        const esp = raw.specialist ?? raw.employee ?? raw.professional ?? null;

        this.clienteExterno = !!raw.notes?.startsWith('Cliente Externo:');
        this.cita = {
          id: raw.id,
          codigo: raw.code || String(raw.id).padStart(4, '0'),
          local: raw.business?.name || '',
          estado: this.ESTADOS_API[raw.status] ?? 'pendiente',
          notas: raw.notes?.startsWith('Cliente Externo:')
            ? raw.notes.split('|').slice(1).join('|').trim() || ''
            : raw.notes,
          duracion: Number(srv.duration_minutes || 30),
          precio: Number(srv.price || raw.price || 0),
          fechaHoraRaw: raw.appointment_time || raw.fecha_cita,
          creadaEnRaw: raw.created_at,
          cliente: {
            nombre: raw.client?.name ? raw.client.name : raw.notes?.startsWith('Cliente Externo:')
              ? raw.notes.slice(16).split('|')[0].trim()
              : cli.name || raw.customer_name || 'Cliente sin nombre',
            email: raw.client ? raw.client.email : this.clienteExterno ? undefined : cli.email,
            telefono: raw.client ? raw.client.phone : this.clienteExterno ? undefined : cli.phone || raw.customer_phone,
          },
          servicio: {
            nombre: srv.name || 'Servicio',
            precio: Number(srv.price || 0),
          },
          profesional: esp?.name || raw.staff_name ? { nombre: esp?.name || raw.staff_name } : undefined,
        };

        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error al obtener la cita:', err);
        this.cargando = false;
        this.error = true;
        this.errorMsg = err.error?.message || 'No se pudo cargar el detalle de la cita.';
        this.cdr.detectChanges();
      },
    });
  }

  cargarOpciones() {
    this.cargando = true;
    this.error = false;
    this.api.get<any>('/tenant/services').subscribe({
      next: (r) => {
        this.servicios = (r.data || []).filter((s: any) => !!Number(s.is_active));
        this.api.get<any>('/tenant/staff').subscribe({
          next: (staff) => {
            this.personal = staff.data || [];
            this.cargando = false;
            this.cdr.detectChanges();
          },
          error: (e) => {
            this.personal = [];
            this.cargando = false;
            this.error = true;
            this.errorMsg = e.error?.message || 'No se pudo cargar el personal. Inténtalo de nuevo.';
            this.cdr.detectChanges();
          },
        });
      },
      error: (e) => {
        this.cargando = false;
        this.error = true;
        this.errorMsg = e.error?.message || 'No se pudieron cargar los servicios.';
        this.cdr.detectChanges();
      },
    });
  }

  crear() {
    if (
      this.guardando ||
      !this.nueva.service_id ||
      !this.nueva.client_name.trim() ||
      (this.modoCliente === 'buscar' && !this.clienteSeleccionado) ||
      !this.nueva.staff_name.trim() ||
      !this.nueva.fecha ||
      !this.nueva.hora
    )
      return;
    this.guardando = true;
    this.errorMsg = '';
    this.api
      .post<any>('/tenant/appointments', {
        service_id: Number(this.nueva.service_id),
        ...(this.modoCliente === 'buscar' && this.clienteSeleccionado
          ? { client_id: this.clienteSeleccionado.id }
          : { new_client: { name: this.nueva.client_name.trim(), ...this.nuevoContacto } }),
        staff_name: this.nueva.staff_name.trim(),
        fecha_cita: this.nueva.fecha,
        hora_cita: this.nueva.hora,
        notes: this.nueva.notes,
      })
      .subscribe({
        next: () => {
          this.guardando = false;
          this.router.navigateByUrl('/tabs/agenda-diaria');
        },
        error: (e) => {
          this.guardando = false;
          this.errorMsg = e.error?.errors ? Object.values(e.error.errors).flat().join(' ') : e.error?.message || 'No se pudo crear la cita.';
          this.cdr.detectChanges();
        },
      });
  }

  cambiarModoCliente(modo: 'nuevo' | 'buscar') {
    if (this.guardando) return;
    ++this.busquedaId;
    this.buscandoClientes = false;
    this.modoCliente = modo;
    this.clienteSeleccionado = null;
    this.nueva.client_name = '';
    this.nuevoContacto = { phone: '', email: '' };
    this.resultadosClientes = [];
    this.busquedaRealizada = false;
    this.errorClientes = '';
  }

  buscarClientes(pagina = 1) {
    if (pagina === 1) this.clienteBusquedaActiva = this.busquedaCliente.trim();
    const request = ++this.busquedaId;
    this.buscandoClientes = true; this.busquedaRealizada = false; this.errorClientes = ''; this.resultadosClientes = [];
    this.clients.list(this.clienteBusquedaActiva, pagina).subscribe({
      next: response => {
        if (request !== this.busquedaId) return;
        this.resultadosClientes = response.data.data;
        this.busquedaRealizada = true;
        this.clientesPaginaActual = response.data.current_page;
        this.clientesUltimaPagina = response.data.last_page;
        this.buscandoClientes = false; this.cdr.detectChanges();
      },
      error: () => {
        if (request !== this.busquedaId) return;
        this.errorClientes = 'No se pudieron buscar los clientes. Inténtalo de nuevo.';
        this.buscandoClientes = false; this.cdr.detectChanges();
      },
    });
  }

  seleccionarCliente(cliente: BusinessClient) {
    this.clienteSeleccionado = cliente;
    this.nueva.client_name = cliente.name;
    this.resultadosClientes = []; this.errorClientes = '';
    this.cdr.detectChanges();
  }

  get iniciales(): string {
    if (!this.cita?.cliente.nombre) return 'CL';
    const partes = this.cita.cliente.nombre.trim().split(' ');
    if (partes.length >= 2) return `${partes[0][0]}${partes[1][0]}`.toUpperCase();
    return partes[0].slice(0, 2).toUpperCase();
  }

  get etiquetaEstado(): string {
    return this.cita ? this.ETIQUETAS[this.cita.estado] || 'Pendiente' : '';
  }

  get precio(): string {
    return this.cita ? this.cita.precio.toFixed(2) : '0.00';
  }

  get fechaHora(): string {
    if (!this.cita?.fechaHoraRaw) return '';
    const fecha = new Date(this.cita.fechaHoraRaw.replace(' ', 'T'));
    return new Intl.DateTimeFormat('es-ES', {
      dateStyle: 'medium',
      timeStyle: 'short',
    }).format(fecha);
  }

  get creadaEn(): string {
    if (!this.cita?.creadaEnRaw) return '';
    const fecha = new Date(this.cita.creadaEnRaw);
    return `Creada el ${new Intl.DateTimeFormat('es-ES', { dateStyle: 'short', timeStyle: 'short' }).format(fecha)}`;
  }

  get cerrada(): boolean {
    return this.cita?.estado === 'completada' || this.cita?.estado === 'cancelada';
  }

  volver() {
    this.location.back();
  }

  menu() {
    // Menú de opciones
  }

  llamar() {
    if (this.cita?.cliente.telefono) {
      window.open(`tel:${this.cita.cliente.telefono}`, '_system');
    }
  }

  escribir() {
    if (this.cita?.cliente.telefono) {
      window.open(`https://wa.me/${this.cita.cliente.telefono.replace(/[^0-9]/g, '')}`, '_system');
    }
  }

  editarFecha() {
    if (!this.cita) return;
    const fecha = prompt('Nueva fecha (AAAA-MM-DD):', this.cita.fechaHoraRaw.slice(0, 10));
    if (fecha === null) return;
    const hora = prompt('Nueva hora (HH:MM):', this.cita.fechaHoraRaw.slice(11, 16));
    if (hora === null) return;
    if (!/^\d{4}-\d{2}-\d{2}$/.test(fecha) || !/^\d{2}:\d{2}$/.test(hora)) {
      alert('Introduce una fecha y hora válidas.');
      return;
    }
    this.guardando = true;
    this.api.put<any>(`/tenant/appointments/${this.cita.id}`, { appointment_time: `${fecha} ${hora}:00` }).subscribe({
      next: () => {
        this.guardando = false;
        this.cargar();
      },
      error: (err) => {
        this.guardando = false;
        alert(err.error?.message || 'No se pudo modificar la fecha.');
        this.cdr.detectChanges();
      },
    });
  }

  editarNota() {
    if (!this.cita) return;
    const nuevaNota = prompt('Editar nota de la cita:', this.cita.notas || '');
    if (nuevaNota !== null) {
      const nota = this.clienteExterno
        ? `Cliente Externo: ${this.cita.cliente.nombre}${nuevaNota ? ' | ' + nuevaNota : ''}`
        : nuevaNota;
      this.guardando = true;
      this.api.put<any>(`/tenant/appointments/${this.cita.id}`, { notes: nota }).subscribe({
        next: () => {
          this.guardando = false;
          this.cargar();
        },
        error: (err) => {
          this.guardando = false;
          alert(err.error?.message || 'No se pudo guardar la nota.');
          this.cdr.detectChanges();
        },
      });
    }
  }

  cambiarEstado(nuevoEstadoApi: string, nuevoEstadoLocal: 'pendiente' | 'confirmada' | 'completada' | 'cancelada') {
    if (!this.cita) return;
    this.guardando = true;

    this.api.patch<any>(`/tenant/appointments/${this.cita.id}/status`, { status: nuevoEstadoApi }).subscribe({
      next: () => {
        if (this.cita) this.cita.estado = nuevoEstadoLocal;
        this.guardando = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.guardando = false;
        this.cdr.detectChanges();
        alert(err.error?.message || 'Error al cambiar estado');
      },
    });
  }

  confirmar() {
    this.cambiarEstado('confirmed', 'confirmada');
  }

  completar() {
    this.cambiarEstado('completed', 'completada');
  }

  cancelar() {
    if (confirm('¿Seguro que deseas cancelar esta cita?')) {
      this.cambiarEstado('cancelled', 'cancelada');
    }
  }
}
