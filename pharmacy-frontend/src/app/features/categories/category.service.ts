import { inject, Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope } from '../../core/models/api.model';
import { Category, CategoryPayload } from '../../core/models/catalog.model';

@Injectable({ providedIn: 'root' })
export class CategoryService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  /** Listado completo (la API de categorías no es paginada). */
  list(): Observable<DataEnvelope<Category[]>> {
    return this.http.get<DataEnvelope<Category[]>>(`${this.base}/categories`);
  }

  get(id: number): Observable<DataEnvelope<Category>> {
    return this.http.get<DataEnvelope<Category>>(`${this.base}/categories/${id}`);
  }

  create(payload: CategoryPayload): Observable<DataEnvelope<Category>> {
    return this.http.post<DataEnvelope<Category>>(`${this.base}/categories`, payload);
  }

  update(id: number, payload: CategoryPayload): Observable<DataEnvelope<Category>> {
    return this.http.put<DataEnvelope<Category>>(`${this.base}/categories/${id}`, payload);
  }

  remove(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/categories/${id}`);
  }
}
