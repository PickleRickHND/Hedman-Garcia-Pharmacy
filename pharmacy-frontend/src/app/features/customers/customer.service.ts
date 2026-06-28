import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope, Paginated } from '../../core/models/api.model';
import { Customer, CustomerPayload } from '../../core/models/customer.model';

@Injectable({ providedIn: 'root' })
export class CustomerService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  list(query: { search?: string; page?: number; per_page?: number }): Observable<Paginated<Customer>> {
    let params = new HttpParams();
    if (query.search) params = params.set('search', query.search);
    params = params.set('page', query.page ?? 1).set('per_page', query.per_page ?? 12);
    return this.http.get<Paginated<Customer>>(`${this.base}/customers`, { params });
  }

  get(id: number): Observable<DataEnvelope<Customer>> {
    return this.http.get<DataEnvelope<Customer>>(`${this.base}/customers/${id}`);
  }

  create(payload: CustomerPayload): Observable<DataEnvelope<Customer>> {
    return this.http.post<DataEnvelope<Customer>>(`${this.base}/customers`, payload);
  }

  update(id: number, payload: CustomerPayload): Observable<DataEnvelope<Customer>> {
    return this.http.put<DataEnvelope<Customer>>(`${this.base}/customers/${id}`, payload);
  }

  remove(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/customers/${id}`);
  }
}
