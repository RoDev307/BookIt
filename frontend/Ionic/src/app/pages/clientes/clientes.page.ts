import { CommonModule } from '@angular/common';
import { Component, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { IonContent } from '@ionic/angular';
import { firstValueFrom } from 'rxjs';
import { BusinessClient, ClientAppointment, ClientFields, ClientsService } from '../../services/clients.service';

@Component({
  selector: 'app-clientes', standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, IonContent],
  templateUrl: './clientes.page.html', styleUrls: ['./clientes.page.scss'],
})
export class ClientesPage {
  private readonly api = inject(ClientsService);
  readonly view = signal<'list' | 'form' | 'detail'>('list');
  readonly clients = signal<BusinessClient[]>([]);
  readonly selected = signal<BusinessClient | null>(null);
  readonly history = signal<ClientAppointment[]>([]);
  readonly busy = signal(false);
  readonly saving = signal(false);
  readonly historyBusy = signal(false);
  readonly error = signal('');
  readonly historyError = signal('');
  readonly notice = signal('');
  readonly page = signal(1);
  readonly lastPage = signal(1);
  readonly total = signal(0);
  readonly historyPage = signal(1);
  readonly historyLastPage = signal(1);
  search = '';
  form: ClientFields = { name: '', phone: '', email: '' };
  private requestId = 0;
  private historyRequestId = 0;
  private activeSearch = '';
  readonly states: Record<string, string> = {
    pending: 'Pendiente', confirmed: 'Confirmada', completed: 'Completada', cancelled: 'Cancelada',
  };
  ionViewWillEnter() {
    if (this.view() === 'list') void this.load();
    else if (this.view() === 'detail') void this.loadHistory();
  }
  async load(page = 1, newSearch = false) {
    if (newSearch) this.activeSearch = this.search.trim();
    const request = ++this.requestId;
    this.busy.set(true); this.error.set('');
    try {
      const result = (await firstValueFrom(this.api.list(this.activeSearch, page))).data;
      if (request !== this.requestId) return;
      this.clients.set(result.data); this.page.set(result.current_page);
      this.lastPage.set(result.last_page); this.total.set(result.total);
    } catch { if (request === this.requestId) this.error.set('No se pudieron cargar los clientes. Reintenta.'); }
    finally { if (request === this.requestId) this.busy.set(false); }
  }
  create() {
    this.selected.set(null); this.form = { name: '', phone: '', email: '' };
    this.error.set(''); this.notice.set(''); this.view.set('form');
  }
  edit(client: BusinessClient) {
    this.selected.set(client); this.form = { name: client.name, phone: client.phone ?? '', email: client.email ?? '' };
    this.error.set(''); this.view.set('form');
  }
  async save() {
    if (this.saving() || !this.form.name.trim()) return;
    this.saving.set(true); this.error.set('');
    try {
      const response = await firstValueFrom(this.api.save({ ...this.form, name: this.form.name.trim() }, this.selected()?.id));
      this.selected.set(response.data); this.notice.set('Ficha guardada.'); this.view.set('detail');
      await this.loadHistory();
    } catch (e: unknown) { this.error.set(this.message(e, 'No se pudo guardar la ficha.')); }
    finally { this.saving.set(false); }
  }
  async detail(client: BusinessClient) {
    this.selected.set(client); this.error.set(''); this.notice.set(''); this.view.set('detail');
    await this.loadHistory();
  }
  async loadHistory(page = 1) {
    const client = this.selected(); if (!client) return;
    const request = ++this.historyRequestId;
    this.historyBusy.set(true); this.historyError.set(''); this.history.set([]);
    try {
      const result = (await firstValueFrom(this.api.history(client.id, page))).data;
      if (request !== this.historyRequestId || this.selected()?.id !== client.id) return;
      this.history.set(result.data); this.historyPage.set(result.current_page); this.historyLastPage.set(result.last_page);
    } catch { if (request === this.historyRequestId) this.historyError.set('No se pudo cargar el historial.'); }
    finally { if (request === this.historyRequestId) this.historyBusy.set(false); }
  }
  back() { ++this.historyRequestId; this.error.set(''); this.notice.set(''); this.view.set('list'); void this.load(); }
  private message(e: unknown, fallback: string) {
    const error = e as { error?: { message?: string; errors?: Record<string, string[]> } };
    const fields = error.error?.errors;
    return fields ? Object.values(fields).flat().join(' ') : error.error?.message || fallback;
  }
}
