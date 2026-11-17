import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';
import { provideIonicAngular } from '@ionic/angular';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { DisponibilidadyHorariosPage } from './disponibilidady-horarios.page';

describe('DisponibilidadyHorariosPage', () => {
  let component: DisponibilidadyHorariosPage;
  let fixture: ComponentFixture<DisponibilidadyHorariosPage>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideRouter([]), provideIonicAngular(), provideHttpClient(), provideHttpClientTesting()],
    });
    fixture = TestBed.createComponent(DisponibilidadyHorariosPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
