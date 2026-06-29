import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope, Paginated } from '../../core/models/api.model';
import { CreateInvoicePayload, Invoice, PaymentMethod } from '../../core/models/invoice.model';

export interface InvoiceQuery {
  search?: string;
  payment_method_id?: number | null;
  status?: string | null;
  date_filter?: 'today' | 'week' | 'month' | null;
  page?: number;
  per_page?: number;
}

@Injectable({ providedIn: 'root' })
export class InvoiceService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  list(query: InvoiceQuery): Observable<Paginated<Invoice>> {
    let params = new HttpParams();
    if (query.search) params = params.set('search', query.search);
    if (query.payment_method_id) params = params.set('payment_method_id', query.payment_method_id);
    if (query.status) params = params.set('status', query.status);
    if (query.date_filter) params = params.set('date_filter', query.date_filter);
    params = params.set('page', query.page ?? 1).set('per_page', query.per_page ?? 12);
    return this.http.get<Paginated<Invoice>>(`${this.base}/invoices`, { params });
  }

  get(id: number): Observable<DataEnvelope<Invoice>> {
    return this.http.get<DataEnvelope<Invoice>>(`${this.base}/invoices/${id}`);
  }

  create(payload: CreateInvoicePayload): Observable<DataEnvelope<Invoice>> {
    return this.http.post<DataEnvelope<Invoice>>(`${this.base}/invoices`, payload);
  }

  void(id: number, reason: string): Observable<DataEnvelope<Invoice>> {
    return this.http.post<DataEnvelope<Invoice>>(`${this.base}/invoices/${id}/void`, { reason });
  }

  paymentMethods(): Observable<DataEnvelope<PaymentMethod[]>> {
    return this.http.get<DataEnvelope<PaymentMethod[]>>(`${this.base}/payment-methods`);
  }

  /** Descarga el PDF de la factura como blob. */
  downloadPdf(id: number): Observable<Blob> {
    return this.http.get(`${this.base}/invoices/${id}/pdf`, { responseType: 'blob' });
  }
}
