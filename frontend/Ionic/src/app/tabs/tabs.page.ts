import { Component, ElementRef, HostListener, viewChild } from '@angular/core';
import { IonRouterOutlet } from '@ionic/angular';
import { RouterLink, RouterLinkActive } from '@angular/router';
@Component({
  selector: 'app-tabs',
  templateUrl: 'tabs.page.html',
  styleUrls: ['tabs.page.scss'],
  imports: [IonRouterOutlet, RouterLink, RouterLinkActive],
})
export class TabsPage {
  menuAbierto = false;
  private readonly menuBtn = viewChild<ElementRef<HTMLButtonElement>>('menuBtn');

  abrirMenu() {
    this.menuAbierto = true;
  }

  /** Cierra el menú y devuelve el foco al botón ☰ para que no quede dentro del panel oculto (inert). */
  cerrarMenu() {
    this.menuAbierto = false;
    this.menuBtn()?.nativeElement.focus({ preventScroll: true });
  }

  @HostListener('document:keydown.escape') cerrar() {
    if (this.menuAbierto) this.cerrarMenu();
  }
}
