import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../../environments/environment';
import { DataEnvelope, Paginated } from '../../core/models/api.model';
import { CashRegister } from '../../core/models/cash-register.model';

@Injectable({ providedIn: 'root' })
export class CashRegisterService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  list(page = 1): Observable<Paginated<CashRegister>> {
    const params = new HttpParams().set('page', page).set('per_page', 10);
    return this.http.get<Paginated<CashRegister>>(`${this.base}/cash-registers`, { params });
  }

  current(): Observable<DataEnvelope<CashRegister | null>> {
    return this.http.get<DataEnvelope<CashRegister | null>>(`${this.base}/cash-registers/current`);
  }

  open(openingAmount: number): Observable<DataEnvelope<CashRegister>> {
    return this.http.post<DataEnvelope<CashRegister>>(`${this.base}/cash-registers/open`, {
      opening_amount: openingAmount,
    });
  }

  close(id: number, actualAmount: number, notes: string | null): Observable<DataEnvelope<CashRegister>> {
    return this.http.post<DataEnvelope<CashRegister>>(`${this.base}/cash-registers/${id}/close`, {
      actual_amount: actualAmount,
      notes,
    });
  }
}
