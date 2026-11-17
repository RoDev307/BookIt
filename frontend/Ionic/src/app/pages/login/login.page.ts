import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { IonContent, IonIcon, IonSpinner } from '@ionic/angular';
import { addIcons } from 'ionicons';
import {
  alertCircleOutline,
  calendarOutline,
  mailOutline,
  lockClosedOutline,
  logInOutline,
  eyeOutline,
  eyeOffOutline,
} from 'ionicons/icons';
import { SessionService } from '../../services/session.service';
import { ChangeDetectorRef } from '@angular/core';
import { ApiService } from '../../services/api.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  standalone: true,
  imports: [CommonModule, FormsModule, IonContent, IonIcon, IonSpinner],
})
export class LoginPage {
  email: string = '';
  password: string = '';
  verPassword: boolean = false;
  cargando: boolean = false;

  // Variables que requiere login.page.html
  errorGeneral: string = '';
  errores: { email?: string; password?: string } = {};

  constructor(
    private api: ApiService,
    private router: Router,
    private session: SessionService,
    private cdr: ChangeDetectorRef,
  ) {
    addIcons({
      alertCircleOutline,
      calendarOutline,
      mailOutline,
      lockClosedOutline,
      logInOutline,
      eyeOutline,
      eyeOffOutline,
    });
  }

  actualizarEmail(event: any) {
    this.email = event.target.value || '';
    if (this.errores.email) delete this.errores.email;
  }

  actualizarPassword(event: any) {
    this.password = event.target.value || '';
    if (this.errores.password) delete this.errores.password;
  }

  entrar(event?: any) {
    if (event && event.preventDefault) {
      event.preventDefault();
    }

    this.errores = {};
    this.errorGeneral = '';

    if (!this.email) {
      this.errores.email = 'El correo es obligatorio';
    }
    if (!this.password) {
      this.errores.password = 'La contraseña es obligatoria';
    }

    if (this.errores.email || this.errores.password) {
      return;
    }

    if (this.cargando) return;
    this.cargando = true;

    const credentials = { email: this.email.trim(), password: this.password };

    this.api.post<any>('/auth/login', credentials).subscribe({
      next: (res) => {
        this.cargando = false;
        if (res.success && res.data?.token) {
          if (!this.session.isAdmin(res.data.user)) {
            this.session.clear();
            this.api.logout(res.data.token).subscribe({ error: () => undefined });
            this.password = '';
            this.errorGeneral = 'Esta aplicación es para la administración del negocio. Usa una cuenta administrativa.';
            this.cdr.detectChanges();
            return;
          }
          this.session.save(res.data);
          this.password = '';
          this.router.navigateByUrl(this.session.home(res.data.user), { replaceUrl: true });
        } else { this.errorGeneral = 'No se pudo abrir la sesión. Inténtalo nuevamente.'; }
        this.cdr.detectChanges();
      },
      error: (err) => {
        this.cargando = false;
        if (err.error?.errors) {
          this.errores = {
            email: err.error.errors.email ? err.error.errors.email[0] : undefined,
            password: err.error.errors.password ? err.error.errors.password[0] : undefined,
          };
        }
        this.errorGeneral = Object.values(err.error?.errors || {}).flat().join(' ') || err.error?.message || 'Credenciales inválidas o error de conexión';
        this.cdr.detectChanges();
      },
    });
  }
}
