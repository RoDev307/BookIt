import { Routes } from '@angular/router';
import { inject } from '@angular/core';
import { Router } from '@angular/router';
import { SessionService } from './services/session.service';
import { adminGuard } from './guards/auth.guard';
export const routes: Routes = [
  { path: '', pathMatch: 'full', canActivate: [async () => {
    const session = inject(SessionService); const router = inject(Router);
    const result = await session.guard('any');
    if (result !== true) return result;
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    return router.parseUrl(session.home(user));
  }], loadComponent: () => import('./pages/login/login.page').then(m => m.LoginPage) },
  { path: 'login', loadComponent: () => import('./pages/login/login.page').then(m => m.LoginPage) },
  { path: 'tabs', canActivate: [adminGuard], loadChildren: () => import('./tabs/tabs.routes').then(m => m.routes) },
  { path: 'gestion-citas', canActivate: [adminGuard], loadComponent: () => import('./pages/gestion-citas/gestion-citas.page').then(m => m.GestionCitasPage) },
  { path: 'gestion-citas/:id', canActivate: [adminGuard], loadComponent: () => import('./pages/gestion-citas/gestion-citas.page').then(m => m.GestionCitasPage) },
  { path: '**', redirectTo: '' },
];
