import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope, Paginated } from '../../core/models/api.model';
import { Category, Supplier } from '../../core/models/catalog.model';
import { Product, ProductPayload } from '../../core/models/product.model';

export interface ProductQuery {
  search?: string;
  category_id?: number | null;
  low_stock?: boolean;
  page?: number;
  per_page?: number;
}

@Injectable({ providedIn: 'root' })
export class ProductService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  list(query: ProductQuery): Observable<Paginated<Product>> {
    let params = new HttpParams();
    if (query.search) params = params.set('search', query.search);
    if (query.category_id) params = params.set('category_id', query.category_id);
    if (query.low_stock) params = params.set('low_stock', '1');
    params = params.set('page', query.page ?? 1).set('per_page', query.per_page ?? 12);
    return this.http.get<Paginated<Product>>(`${this.base}/products`, { params });
  }

  get(id: number): Observable<DataEnvelope<Product>> {
    return this.http.get<DataEnvelope<Product>>(`${this.base}/products/${id}`);
  }

  create(payload: ProductPayload): Observable<DataEnvelope<Product>> {
    return this.http.post<DataEnvelope<Product>>(`${this.base}/products`, payload);
  }

  update(id: number, payload: ProductPayload): Observable<DataEnvelope<Product>> {
    return this.http.put<DataEnvelope<Product>>(`${this.base}/products/${id}`, payload);
  }

  remove(id: number): Observable<void> {
    return this.http.delete<void>(`${this.base}/products/${id}`);
  }

  /** Categorías (lista completa, no paginada) para selects. */
  categories(): Observable<DataEnvelope<Category[]>> {
    return this.http.get<DataEnvelope<Category[]>>(`${this.base}/categories`);
  }

  /** Proveedores para selects (pedimos un tope alto para traerlos todos). */
  suppliers(): Observable<Paginated<Supplier>> {
    const params = new HttpParams().set('per_page', 100);
    return this.http.get<Paginated<Supplier>>(`${this.base}/suppliers`, { params });
  }
}
