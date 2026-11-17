import { Component, inject } from '@angular/core';
import { takeUntilDestroyed } from '@angular/core/rxjs-interop';
import { NavigationStart, Router } from '@angular/router';
import { IonApp, IonRouterOutlet } from '@ionic/angular';
import { filter } from 'rxjs';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  imports: [IonApp, IonRouterOutlet],
})
export class AppComponent {
  constructor() {
    document.documentElement.dataset['theme'] = localStorage.getItem('bookit_theme') === 'light' ? 'light' : 'dark';

    // Ionic marca la página que sale con aria-hidden="true" durante la transición. Si el enlace o botón
    // que se acaba de pulsar sigue con el foco dentro de esa página, el navegador avisa
    // "Blocked aria-hidden on an element because its descendant retained focus". Quitar el foco antes de navegar lo evita.
    inject(Router)
      .events.pipe(
        filter((e) => e instanceof NavigationStart),
        takeUntilDestroyed(),
      )
      .subscribe(() => {
        const activo = document.activeElement;
        if (activo instanceof HTMLElement && activo !== document.body) activo.blur();
      });
  }
}
