import { TestBed } from '@angular/core/testing';
import { Router, provideRouter } from '@angular/router';

import { AppComponent } from './app.component';

describe('AppComponent', () => {
  it('should create the app', async () => {
    await TestBed.configureTestingModule({
      imports: [AppComponent],
      providers: [provideRouter([])],
    }).compileComponents();

    const fixture = TestBed.createComponent(AppComponent);
    const app = fixture.componentInstance;
    expect(app).toBeTruthy();
  });

  it('quita el foco del elemento activo al navegar (evita el aviso aria-hidden de Ionic)', async () => {
    await TestBed.configureTestingModule({
      imports: [AppComponent],
      providers: [provideRouter([{ path: 'destino', children: [] }])],
    }).compileComponents();
    TestBed.createComponent(AppComponent);

    const enlace = document.createElement('a');
    enlace.href = '#';
    document.body.appendChild(enlace);
    enlace.focus();
    expect(document.activeElement).toBe(enlace);

    await TestBed.inject(Router).navigateByUrl('/destino');
    expect(document.activeElement).not.toBe(enlace);
    enlace.remove();
  });
});
