import { Routes } from '@angular/router';
import { authGuard, adminChildGuard } from '../guards/auth.guard';
import { TabsPage } from './tabs.page';

export const routes: Routes = [
  {
    path: '',
    component: TabsPage,
    canActivateChild: [adminChildGuard],
    children: [
      {
        path: 'gestionar-citas', canActivate: [authGuard],
        loadComponent: () => import('../pages/gestionar-citas/gestionar-citas.page').then(m => m.GestionarCitasPage),
      },
      {
        path: 'clientes', canActivate: [authGuard],
        loadComponent: () => import('../pages/clientes/clientes.page').then(m => m.ClientesPage),
      },
      {
        path: 'dashboard',
        loadComponent: () => import('../pages/dashboard/dashboard.page').then((m) => m.DashboardPage),
      },
      {
        path: 'agenda-diaria',
        loadComponent: () => import('../pages/agenda-diaria/agenda-diaria.page').then((m) => m.AgendaDiariaPage),
      },
      {
        path: 'disponibilidad',
        loadComponent: () =>
          import('../pages/disponibilidady-horarios/disponibilidady-horarios.page').then(
            (m) => m.DisponibilidadyHorariosPage,
          ),
      },
      {
        path: 'servicios',
        loadComponent: () =>
          import('../pages/catalogo-servicios/catalogo-servicios.page').then((m) => m.CatalogoServiciosPage),
      },
      {
        path: 'mi-cuenta',
        loadComponent: () => import('../pages/mi-cuenta/mi-cuenta.page').then((m) => m.MiCuentaPage),
      },
      {
        path: 'notificaciones',
        loadComponent: () => import('../pages/notificaciones/notificaciones.page').then((m) => m.NotificacionesPage),
      },
      {
        path: 'personal',
        loadComponent: () => import('../pages/personal/personal.page').then((m) => m.PersonalPage),
      },
      {
        path: '',
        redirectTo: 'dashboard',
        pathMatch: 'full',
      },
    ],
  },
];
