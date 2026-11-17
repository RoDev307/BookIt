import { Component, OnInit, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { IonContent, IonIcon, IonSpinner, IonToggle } from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  calendarOutline,
  chevronDown,
  optionsOutline,
  notificationsOutline,
  shieldCheckmarkOutline,
  checkmark,
} from 'ionicons/icons';
import { ApiService } from '../../services/api.service';

export interface DiaHorario {
  num: number; // 1 = Lunes, 7 = Domingo
  etiqueta: string;
  abierto: boolean;
  apertura: string;
  cierre: string;
}

@Component({
  selector: 'app-disponibilidady-horarios',
  templateUrl: './disponibilidady-horarios.page.html',
  styleUrls: ['./disponibilidady-horarios.page.scss'],
  standalone: true,
  imports: [CommonModule, FormsModule, IonContent, IonIcon, IonSpinner, IonToggle],
})
export class DisponibilidadyHorariosPage implements OnInit {
  negocio: string = 'Mi Comercio';
  cargando: boolean = true;
  guardando: boolean = false;
  sucio: boolean = false;
  error: boolean = false;
  errorMsg: string = '';

  horario: DiaHorario[] = [
    { num: 1, etiqueta: 'Lunes', abierto: true, apertura: '08:00', cierre: '17:00' },
    { num: 2, etiqueta: 'Martes', abierto: true, apertura: '08:00', cierre: '17:00' },
    { num: 3, etiqueta: 'Miércoles', abierto: true, apertura: '08:00', cierre: '17:00' },
    { num: 4, etiqueta: 'Jueves', abierto: true, apertura: '08:00', cierre: '17:00' },
    { num: 5, etiqueta: 'Viernes', abierto: true, apertura: '08:00', cierre: '17:00' },
    { num: 6, etiqueta: 'Sábado', abierto: false, apertura: '09:00', cierre: '13:00' },
    { num: 0, etiqueta: 'Domingo', abierto: false, apertura: '09:00', cierre: '13:00' },
  ];

  constructor(
    private api: ApiService,
    private cdr: ChangeDetectorRef,
  ) {
    addIcons({
      calendarOutline,
      chevronDown,
      optionsOutline,
      notificationsOutline,
      shieldCheckmarkOutline,
      checkmark,
    });
  }

  ngOnInit() {
    this.cargar();
  }

  cargar() {
    this.cargando = true;
    this.error = false;

    this.api.get<any>('/tenant/business').subscribe({
      next: (res) => {
        const data = res.data ?? res;
        if (data.name) this.negocio = data.name;

        if (Array.isArray(data.hours)) {
          this.horario = this.horario.map((d) => {
            const h = data.hours.find((item: any) => Number(item.day_of_week) === d.num);
            return h
              ? {
                  ...d,
                  abierto: !Boolean(Number(h.is_closed)),
                  apertura: h.opening_time?.slice(0, 5) || d.apertura,
                  cierre: h.closing_time?.slice(0, 5) || d.cierre,
                }
              : d;
          });
        }
        this.sucio = false;
        // Se apaga la carga tras éxito
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (err) => {
        console.error('Error cargando disponibilidad desde la API:', err);
        // CORRECCIÓN CLAVE: Apagar la carga para mostrar el estado de error o la plantilla por defecto
        this.cargando = false;
        this.error = true;
        this.errorMsg = err.error?.message || 'No se pudo conectar con la API de disponibilidad.';
        this.cdr.detectChanges();
      },
    });
  }

  toggleDia(h: DiaHorario, event: any) {
    h.abierto = event.detail.checked;
    this.sucio = true;
  }

  editarHoras() {
    this.sucio = true;
  }

  igualarSemana() {
    const base = this.horario[0];
    this.horario.forEach((d) => {
      d.abierto = base.abierto;
      d.apertura = base.apertura;
      d.cierre = base.cierre;
    });
    this.sucio = true;
  }

  guardar() {
    if (this.horario.some((h) => h.abierto && (!h.apertura || !h.cierre || h.apertura >= h.cierre))) {
      alert('La hora de cierre debe ser posterior a la apertura en todos los días abiertos.');
      return;
    }
    this.guardando = true;
    const payload = {
      hours: this.horario.map((h) => ({
        day_of_week: h.num,
        is_closed: !h.abierto,
        // Se envían siempre las horas (aunque el día esté cerrado) para no romper validaciones del backend
        opening_time: h.apertura ? `${h.apertura}:00` : null,
        closing_time: h.cierre ? `${h.cierre}:00` : null,
      })),
    };

    this.api.put<any>('/tenant/business/hours', payload).subscribe({
      next: () => {
        this.guardando = false;
        this.sucio = false;
        alert('Horario actualizado con éxito');
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.guardando = false;
        alert(err.error?.message || 'Error al guardar horario');
        this.cdr.detectChanges();
      },
    });
  }

  menu() {
    // Opciones del menú
  }
}
