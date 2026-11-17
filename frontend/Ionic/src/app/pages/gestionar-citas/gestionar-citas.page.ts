import { CommonModule } from '@angular/common';
import { Component, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import { IonContent, IonModal, AlertController } from '@ionic/angular';
import { firstValueFrom } from 'rxjs';
import { ApiService } from '../../services/api.service';

interface AppointmentRow {
  id: number; appointment_time: string; status: string; staff_name: string | null; notes: string | null;
  client?: { name: string } | null; user?: { name: string } | null;
  service?: { name: string; price?: string | number } | null;
}
@Component({
  selector: 'app-gestionar-citas', standalone: true,
  imports: [CommonModule, FormsModule, RouterLink, IonContent, IonModal],
  templateUrl: './gestionar-citas.page.html', styleUrls: ['./gestionar-citas.page.scss'],
})
export class GestionarCitasPage {
  private readonly api = inject(ApiService);
  private readonly alerts = inject(AlertController);
  readonly allRows = signal<AppointmentRow[]>([]);
  readonly editing = signal<AppointmentRow | null>(null);
  readonly saving = signal(false);
  readonly actionError = signal('');
  readonly actionNotice = signal('');
  readonly cancelling = signal<number | null>(null);
  readonly receiptId = signal<number | null>(null);
  edit = { date: '', time: '', staff: '', notes: '' };
  order = 'id-desc';
  get received() { return this.allRows().length; }
  get confirmed() { return this.allRows().filter(r => r.status === 'confirmed').length; }
  get cancelled() { return this.allRows().filter(r => r.status === 'cancelled').length; }
  readonly rows = signal<AppointmentRow[]>([]);
  readonly loading = signal(false);
  readonly error = signal('');
  readonly reportError = signal('');
  readonly reportNotice = signal('');
  readonly pdfUrl = signal<string | null>(null);
  readonly pdfName = signal('');
  readonly downloading = signal<'semana' | 'mes' | null>(null);
  readonly page = signal(1);
  readonly pageSize = 10;
  readonly states: Record<string, string> = {
    pending: 'Pendiente', confirmed: 'Confirmada', completed: 'Completada', cancelled: 'Cancelada',
  };
  date = '';
  status = '';
  private appliedDate = '';
  private appliedStatus = '';
  private requestId = 0;
  ionViewWillEnter() { void this.load(); }
  async load(apply = false) {
    if (apply) { this.appliedDate = this.date; this.appliedStatus = this.status; }
    const query = new URLSearchParams();
    if (this.appliedDate) query.set('date', this.appliedDate);
    if (this.appliedStatus) query.set('status', this.appliedStatus);
    const request = ++this.requestId;
    this.loading.set(true); this.error.set('');
    try {
      const filtered = firstValueFrom(this.api.get<{ data: AppointmentRow[] }>('/tenant/appointments?' + query));
      // Sin filtros ambas listas son la misma; con filtros se piden en paralelo (antes eran una tras otra).
      const unfiltered = query.size ? firstValueFrom(this.api.get<{ data: AppointmentRow[] }>('/tenant/appointments')) : filtered;
      const [response, all] = await Promise.all([filtered, unfiltered]);
      if (request !== this.requestId) return;
      this.allRows.set(all.data);
      this.rows.set(response.data); this.page.set(1);
    } catch {
      if (request === this.requestId) { this.rows.set([]); this.error.set('No se pudieron cargar las citas. Reintenta.'); }
    } finally { if (request === this.requestId) this.loading.set(false); }
  }
  clear() { this.date = ''; this.status = ''; void this.load(true); }
  get visibleRows() { const sorted = [...this.rows()].sort((a,b) => this.order === 'date-asc' ? a.appointment_time.localeCompare(b.appointment_time) || a.id-b.id : b.id-a.id); return sorted.slice((this.page() - 1) * this.pageSize, this.page() * this.pageSize); }
  get lastPage() { return Math.max(1, Math.ceil(this.rows().length / this.pageSize)); }
  move(delta: number) { this.page.set(Math.min(this.lastPage, Math.max(1, this.page() + delta))); }
  clientName(row: AppointmentRow): string {
    if (row.client?.name) return row.client.name;
    if (row.notes?.startsWith('Cliente Externo:')) return row.notes.slice(16).split('|')[0].trim();
    return row.user?.name || 'Cliente';
  }
  ngOnDestroy() { const url = this.pdfUrl(); if (url) URL.revokeObjectURL(url); }
  async download(period: 'semana' | 'mes') {
    if (this.downloading()) return;
    this.downloading.set(period); this.reportError.set(''); this.reportNotice.set('');
    try {
      const blob = await firstValueFrom(this.api.getPdf('/tenant/appointments/report/pdf?periodo=' + period));
      if (!(await blob.slice(0, 5).text()).startsWith('%PDF')) throw new Error('Respuesta sin PDF');
      this.preparePdf(blob, 'BookIt-citas-' + period + '.pdf');
    } catch {
      this.reportError.set('No se pudo generar el PDF. Comprueba tu conexión e inténtalo de nuevo.');
    } finally { this.downloading.set(null); }
  }
  private preparePdf(blob: Blob, name: string) {
      const previous = this.pdfUrl();
      if (previous) URL.revokeObjectURL(previous);
      const url = URL.createObjectURL(blob);
      this.pdfUrl.set(url); this.pdfName.set(name);
      const link = document.createElement('a');
      link.href = url; link.download = name;
      document.body.appendChild(link); link.click(); link.remove();
      this.reportNotice.set('PDF listo. Puedes abrirlo o guardarlo si el navegador no inició la descarga.');
  }
  async receipt(row: AppointmentRow) {
    if (this.receiptId() || this.downloading()) return;
    this.receiptId.set(row.id); this.reportError.set(''); this.reportNotice.set('');
    try {
      const blob = await firstValueFrom(this.api.getPdf('/tenant/appointments/' + row.id + '/pdf'));
      if (!(await blob.slice(0,5).text()).startsWith('%PDF')) throw new Error('Sin PDF');
      this.preparePdf(blob, 'BookIt-cita-' + row.id + '.pdf');
    } catch { this.reportError.set('No se pudo generar el comprobante de la cita.'); }
    finally { this.receiptId.set(null); }
  }
  openEdit(row: AppointmentRow) {
    this.actionError.set('');
    this.edit = { date: row.appointment_time.slice(0,10), time: row.appointment_time.slice(11,16), staff: row.staff_name || '',
      notes: row.notes?.startsWith('Cliente Externo:') ? row.notes.split('|').slice(1).join('|').trim() : row.notes || '' };
    this.editing.set(row);
  }
  closeEdit() { if (!this.saving()) this.editing.set(null); }
  async saveEdit() {
    const row = this.editing(); if (!row || this.saving()) return;
    this.saving.set(true); this.actionError.set('');
    const prefix = row.notes?.startsWith('Cliente Externo:') ? row.notes.split('|')[0].trim() : '';
    const notes = prefix ? prefix + (this.edit.notes.trim() ? ' | ' + this.edit.notes.trim() : '') : this.edit.notes.trim();
    try {
      await firstValueFrom(this.api.put('/tenant/appointments/' + row.id, {
        appointment_time: this.edit.date + ' ' + this.edit.time + ':00', staff_name: this.edit.staff.trim(), notes,
      }));
      this.editing.set(null); this.actionNotice.set('Cita #' + row.id + ' actualizada.'); await this.load();
    } catch (e: unknown) { this.actionError.set(this.errorMessage(e, 'No se pudo actualizar la cita.')); }
    finally { this.saving.set(false); }
  }
  async askCancel(row: AppointmentRow) {
    if (this.cancelling() || ['cancelled','completed'].includes(row.status)) return;
    const alert = await this.alerts.create({ header: 'Cancelar cita #' + row.id,
      message: '¿Deseas cancelar esta cita? Seguirá apareciendo en el historial.',
      buttons: [{ text: 'Volver', role: 'cancel' }, { text: 'Cancelar cita', role: 'destructive', handler: () => { void this.cancelRow(row); } }],
    });
    await alert.present();
  }
  private async cancelRow(row: AppointmentRow) {
    this.cancelling.set(row.id); this.actionError.set('');
    try {
      await firstValueFrom(this.api.patch('/tenant/appointments/' + row.id + '/status', {status:'cancelled'}));
      this.actionNotice.set('Cita #' + row.id + ' cancelada.'); await this.load();
    } catch (e: unknown) { this.actionError.set(this.errorMessage(e, 'No se pudo cancelar la cita.')); }
    finally { this.cancelling.set(null); }
  }
  private errorMessage(e: unknown, fallback: string) {
    const response = e as {error?: {message?: string; errors?: Record<string,string[]>}};
    return response.error?.errors ? Object.values(response.error.errors).flat().join(' ') : response.error?.message || fallback;
  }
}
