import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';
import { provideIonicAngular } from '@ionic/angular';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { CatalogoServiciosPage } from './catalogo-servicios.page';

describe('CatalogoServiciosPage', () => {
  let component: CatalogoServiciosPage;
  let fixture: ComponentFixture<CatalogoServiciosPage>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideRouter([]), provideIonicAngular(), provideHttpClient(), provideHttpClientTesting()],
    });
    fixture = TestBed.createComponent(CatalogoServiciosPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
