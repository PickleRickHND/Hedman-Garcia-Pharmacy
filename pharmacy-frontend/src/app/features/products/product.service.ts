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
  expiring_soon?: boolean;
  expired?: boolean;
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
    if (query.expiring_soon) params = params.set('expiring_soon', '1');
    if (query.expired) params = params.set('expired', '1');
    params = params.set('page', query.page ?? 1).set('per_page', query.per_page ?? 12);
    return this.http.get<Paginated<Product>>(`${this.base}/products`, { params });
  }

  get(id: number): Observable<DataEnvelope<Product>> {
    return this.http.get<DataEnvelope<Product>>(`${this.base}/products/${id}`);
  }

  create(payload: ProductPayload): Observable<DataEnvelope<Product>> {
    if (payload.image instanceof File) {
      return this.http.post<DataEnvelope<Product>>(`${this.base}/products`, this.toFormData(payload));
    }
    return this.http.post<DataEnvelope<Product>>(`${this.base}/products`, payload);
  }

  update(id: number, payload: ProductPayload): Observable<DataEnvelope<Product>> {
    if (payload.image instanceof File) {
      // PHP no parsea multipart en PUT: usamos POST con method spoofing (_method=PUT).
      const body = this.toFormData(payload);
      body.append('_method', 'PUT');
      return this.http.post<DataEnvelope<Product>>(`${this.base}/products/${id}`, body);
    }
    return this.http.put<DataEnvelope<Product>>(`${this.base}/products/${id}`, payload);
  }

  /** Serializa el payload a FormData para subir la imagen junto a los campos. */
  private toFormData(payload: ProductPayload): FormData {
    const fd = new FormData();
    for (const [key, value] of Object.entries(payload)) {
      if (key === 'image') continue;
      if (value === undefined) continue;
      // Los nulos se envían como cadena vacía para que el backend los limpie
      // (paridad con el path JSON; multipart no transmite null nativo).
      fd.append(key, value === null ? '' : String(value));
    }
    if (payload.image instanceof File) {
      fd.append('image', payload.image);
    }
    return fd;
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
