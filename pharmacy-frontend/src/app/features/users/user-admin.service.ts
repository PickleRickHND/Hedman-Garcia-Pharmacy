import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope, Paginated } from '../../core/models/api.model';
import { User, UserPayload } from '../../core/models/user.model';

/** Administración de usuarios y roles (solo Administrador). */
@Injectable({ providedIn: 'root' })
export class UserAdminService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  list(query: { search?: string; role?: string; page?: number; per_page?: number }): Observable<Paginated<User>> {
    let params = new HttpParams();
    if (query.search) params = params.set('search', query.search);
    if (query.role) params = params.set('role', query.role);
    params = params.set('page', query.page ?? 1).set('per_page', query.per_page ?? 12);
    return this.http.get<Paginated<User>>(`${this.base}/users`, { params });
  }

  get(id: number): Observable<DataEnvelope<User>> {
    return this.http.get<DataEnvelope<User>>(`${this.base}/users/${id}`);
  }

  create(payload: UserPayload): Observable<DataEnvelope<User>> {
    return this.http.post<DataEnvelope<User>>(`${this.base}/users`, payload);
  }

  update(id: number, payload: UserPayload): Observable<DataEnvelope<User>> {
    return this.http.put<DataEnvelope<User>>(`${this.base}/users/${id}`, payload);
  }

  remove(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/users/${id}`);
  }

  /** Nombres de roles disponibles para poblar el select del formulario. */
  roles(): Observable<DataEnvelope<string[]>> {
    return this.http.get<DataEnvelope<string[]>>(`${this.base}/roles`);
  }
}
