import { Injectable, inject } from '@angular/core';
import { ApiService } from './api.service';
export interface BusinessClient {
  id: number; name: string; phone: string | null; email: string | null;
  user_id: number | null; appointments_count?: number;
}
export interface ClientFields { name: string; phone: string; email: string; }
export interface PageData<T> { data: T[]; current_page: number; last_page: number; total: number; }
export interface ClientAppointment {
  id: number; appointment_time: string; status: string; staff_name: string;
  service: { name: string; } | null;
}
@Injectable({ providedIn: 'root' })
export class ClientsService {
  private readonly api = inject(ApiService);
  list(search = '', page = 1) {
    return this.api.get<{ data: PageData<BusinessClient> }>(`/tenant/clients?search=${encodeURIComponent(search)}&page=${page}`);
  }
  save(data: ClientFields, id?: number) {
    return id ? this.api.put<{ data: BusinessClient }>(`/tenant/clients/${id}`, data)
      : this.api.post<{ data: BusinessClient }>('/tenant/clients', data);
  }
  get(id: number) { return this.api.get<{ data: BusinessClient }>(`/tenant/clients/${id}`); }
  history(id: number, page = 1) {
    return this.api.get<{ data: PageData<ClientAppointment> }>(`/tenant/clients/${id}/appointments?page=${page}`);
  }
}
