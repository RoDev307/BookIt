import { Component, ChangeDetectorRef } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterLink } from '@angular/router';
import { IonContent } from '@ionic/angular';
import { ApiService } from '../../services/api.service';
interface Usuario {
  name: string;
  email: string;
  business?: { name: string } | null;
}
@Component({
  selector: 'app-mi-cuenta',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, IonContent],
  templateUrl: './mi-cuenta.page.html',
  styleUrls: ['./mi-cuenta.page.scss'],
})
export class MiCuentaPage {
  tema = localStorage.getItem('bookit_theme') === 'light' ? 'light' : 'dark';
  usuario: Usuario | null = null;
  cargando = true;
  cerrando = false;
  error = '';
  soloNoLeidas = localStorage.getItem('bookit_solo_no_leidas') === 'true';
  constructor(
    private api: ApiService,
    private router: Router,
    private cdr: ChangeDetectorRef,
  ) {}
  ionViewWillEnter() {
    this.cargar();
    this.soloNoLeidas = localStorage.getItem('bookit_solo_no_leidas') === 'true';
  }
  cargar() {
    this.cargando = true;
    this.error = '';
    this.api.get<{ data: { user: Usuario } }>('/auth/me').subscribe({
      next: (r) => {
        this.usuario = r.data.user;
        this.cargando = false;
        this.cdr.detectChanges();
      },
      error: (e) => {
        this.cargando = false;
        this.error = e.error?.message || 'No se pudo cargar tu cuenta. Revisa tu sesión y conexión.';
        this.cdr.detectChanges();
      },
    });
  }
  cambiarTema() {
    document.documentElement.dataset['theme'] = this.tema;
    localStorage.setItem('bookit_theme', this.tema);
  }
  guardarPreferencia() {
    localStorage.setItem('bookit_solo_no_leidas', String(this.soloNoLeidas));
  }
  cerrarSesion() {
    if (this.cerrando) return;
    this.cerrando = true;
    this.error = '';
    this.api.logout().subscribe({
      next: () => {
        localStorage.removeItem('token');
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user');
        this.router.navigateByUrl('/login', { replaceUrl: true });
      },
      error: (e) => {
        this.cerrando = false;
        this.error = e.error?.message || 'No se pudo cerrar la sesión. Comprueba la conexión e inténtalo de nuevo.';
        this.cdr.detectChanges();
      },
    });
  }
}
