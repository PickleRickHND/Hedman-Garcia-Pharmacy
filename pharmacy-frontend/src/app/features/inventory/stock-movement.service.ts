import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Paginated } from '../../core/models/api.model';
import { StockMovement, StockMovementFilters } from '../../core/models/stock-movement.model';

/** Kardex de movimientos de stock (solo lectura). */
@Injectable({ providedIn: 'root' })
export class StockMovementService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  list(filters: StockMovementFilters): Observable<Paginated<StockMovement>> {
    let params = new HttpParams();
    if (filters.product_id) params = params.set('product_id', filters.product_id);
    if (filters.type) params = params.set('type', filters.type);
    if (filters.date_from) params = params.set('date_from', filters.date_from);
    if (filters.date_to) params = params.set('date_to', filters.date_to);
    params = params.set('page', filters.page ?? 1).set('per_page', filters.per_page ?? 20);
    return this.http.get<Paginated<StockMovement>>(`${this.base}/stock-movements`, { params });
  }
}
