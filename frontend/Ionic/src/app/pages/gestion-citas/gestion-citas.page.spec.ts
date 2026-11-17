import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';
import { provideIonicAngular } from '@ionic/angular';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { GestionCitasPage } from './gestion-citas.page';

describe('GestionCitasPage', () => {
  let component: GestionCitasPage;
  let fixture: ComponentFixture<GestionCitasPage>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideRouter([]), provideIonicAngular(), provideHttpClient(), provideHttpClientTesting()],
    });
    fixture = TestBed.createComponent(GestionCitasPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
