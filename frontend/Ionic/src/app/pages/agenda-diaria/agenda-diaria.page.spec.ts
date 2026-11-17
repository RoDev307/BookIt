import { provideHttpClient } from '@angular/common/http';
import { provideHttpClientTesting } from '@angular/common/http/testing';
import { provideRouter } from '@angular/router';
import { provideIonicAngular } from '@ionic/angular';
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { AgendaDiariaPage } from './agenda-diaria.page';

describe('AgendaDiariaPage', () => {
  let component: AgendaDiariaPage;
  let fixture: ComponentFixture<AgendaDiariaPage>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [provideRouter([]), provideIonicAngular(), provideHttpClient(), provideHttpClientTesting()],
    });
    fixture = TestBed.createComponent(AgendaDiariaPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
