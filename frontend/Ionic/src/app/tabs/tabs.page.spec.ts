import type { ComponentFixture } from '@angular/core/testing';
import { TestBed } from '@angular/core/testing';
import { provideRouter } from '@angular/router';

import { TabsPage } from './tabs.page';

describe('TabsPage', () => {
  let component: TabsPage;
  let fixture: ComponentFixture<TabsPage>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [TabsPage],
      providers: [provideRouter([])],
    }).compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(TabsPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });

  it('al cerrar el menú el foco vuelve al botón ☰ y el panel no usa aria-hidden', () => {
    const el: HTMLElement = fixture.nativeElement;
    const boton = el.querySelector<HTMLButtonElement>('.menu-button')!;
    const panel = el.querySelector<HTMLElement>('#menu-vistas')!;

    boton.click();
    fixture.detectChanges();
    expect(component.menuAbierto).toBe(true);
    expect(panel.hasAttribute('inert')).toBe(false);

    // el foco queda dentro del panel (como cuando se pulsa un enlace del menú) y se cierra
    panel.querySelector<HTMLAnchorElement>('nav a')!.focus();
    expect(panel.contains(document.activeElement)).toBe(true);
    panel.querySelector<HTMLButtonElement>('.sidebar-head button')!.click(); // (click)="cerrarMenu()"
    fixture.detectChanges();

    expect(component.menuAbierto).toBe(false);
    expect(panel.hasAttribute('inert')).toBe(true);
    expect(panel.hasAttribute('aria-hidden')).toBe(false);
    expect(panel.contains(document.activeElement)).toBe(false);
  });
});
