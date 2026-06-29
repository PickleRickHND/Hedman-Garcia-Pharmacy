import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope, Paginated } from '../../core/models/api.model';
import { CreateReturnPayload, ReturnOrder } from '../../core/models/return.model';

@Injectable({ providedIn: 'root' })
export class ReturnService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  list(page = 1): Observable<Paginated<ReturnOrder>> {
    const params = new HttpParams().set('page', page).set('per_page', 12);
    return this.http.get<Paginated<ReturnOrder>>(`${this.base}/returns`, { params });
  }

  get(id: number): Observable<DataEnvelope<ReturnOrder>> {
    return this.http.get<DataEnvelope<ReturnOrder>>(`${this.base}/returns/${id}`);
  }

  create(payload: CreateReturnPayload): Observable<DataEnvelope<ReturnOrder>> {
    return this.http.post<DataEnvelope<ReturnOrder>>(`${this.base}/returns`, payload);
  }
}
