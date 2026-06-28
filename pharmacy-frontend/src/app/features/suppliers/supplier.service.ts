import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope, Paginated } from '../../core/models/api.model';
import { Supplier, SupplierPayload } from '../../core/models/catalog.model';

@Injectable({ providedIn: 'root' })
export class SupplierService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  list(query: { search?: string; page?: number; per_page?: number }): Observable<Paginated<Supplier>> {
    let params = new HttpParams();
    if (query.search) params = params.set('search', query.search);
    params = params.set('page', query.page ?? 1).set('per_page', query.per_page ?? 12);
    return this.http.get<Paginated<Supplier>>(`${this.base}/suppliers`, { params });
  }

  get(id: number): Observable<DataEnvelope<Supplier>> {
    return this.http.get<DataEnvelope<Supplier>>(`${this.base}/suppliers/${id}`);
  }

  create(payload: SupplierPayload): Observable<DataEnvelope<Supplier>> {
    return this.http.post<DataEnvelope<Supplier>>(`${this.base}/suppliers`, payload);
  }

  update(id: number, payload: SupplierPayload): Observable<DataEnvelope<Supplier>> {
    return this.http.put<DataEnvelope<Supplier>>(`${this.base}/suppliers/${id}`, payload);
  }

  remove(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/suppliers/${id}`);
  }
}
