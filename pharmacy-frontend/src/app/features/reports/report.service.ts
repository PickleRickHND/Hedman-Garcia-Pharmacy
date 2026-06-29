import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope } from '../../core/models/api.model';
import { InventoryReport, SalesReport, TopProduct } from '../../core/models/report.model';

/** Reportes (solo Administrador): ventas, top productos e inventario. */
@Injectable({ providedIn: 'root' })
export class ReportService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  sales(range: { from?: string; to?: string }): Observable<DataEnvelope<SalesReport>> {
    let params = new HttpParams();
    if (range.from) params = params.set('from', range.from);
    if (range.to) params = params.set('to', range.to);
    return this.http.get<DataEnvelope<SalesReport>>(`${this.base}/reports/sales`, { params });
  }

  products(query: {
    from?: string;
    to?: string;
    limit?: number;
    sort_by?: 'quantity' | 'revenue';
  }): Observable<DataEnvelope<TopProduct[]>> {
    let params = new HttpParams();
    if (query.from) params = params.set('from', query.from);
    if (query.to) params = params.set('to', query.to);
    if (query.limit) params = params.set('limit', query.limit);
    if (query.sort_by) params = params.set('sort_by', query.sort_by);
    return this.http.get<DataEnvelope<TopProduct[]>>(`${this.base}/reports/products`, { params });
  }

  inventory(): Observable<DataEnvelope<InventoryReport>> {
    return this.http.get<DataEnvelope<InventoryReport>>(`${this.base}/reports/inventory`);
  }
}
