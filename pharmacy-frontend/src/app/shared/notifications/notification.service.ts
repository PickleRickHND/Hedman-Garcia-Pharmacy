import { computed, inject, Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Alert, AlertsResponse } from '../../core/models/notification.model';

/**
 * Alertas operativas (stock, vencimientos, caja) calculadas en el backend.
 *
 * Son estado EN VIVO: una alerta permanece visible mientras la condición exista
 * (p. ej. hay productos agotados) y desaparece sola cuando se resuelve, al
 * siguiente refresco. No se "descartan" en memoria: el badge siempre refleja la
 * situación operativa real y vuelve a avisar si surge una condición nueva.
 */
@Injectable({ providedIn: 'root' })
export class NotificationService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  /** Alertas vigentes según el backend. */
  private readonly _alerts = signal<Alert[]>([]);

  /** Alertas a mostrar en el panel y para el badge. */
  readonly alerts = this._alerts.asReadonly();
  readonly count = computed(() => this._alerts().length);

  /** Pide las alertas al backend y refresca el estado. */
  refresh(): Observable<AlertsResponse> {
    return this.http
      .get<AlertsResponse>(`${this.base}/notifications`)
      .pipe(tap((res) => this._alerts.set(res.data ?? [])));
  }
}
