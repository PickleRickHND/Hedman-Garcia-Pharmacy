import { computed, inject, Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { environment } from '../../../environments/environment';
import { Alert, AlertsResponse } from '../../core/models/notification.model';

/**
 * Alertas operativas (stock, vencimientos, caja) calculadas en el backend.
 * Mantiene un estado reactivo para el badge de la campana. Las alertas vistas o
 * accionadas se descartan en memoria de sesión (no reaparecen hasta recargar).
 */
@Injectable({ providedIn: 'root' })
export class NotificationService {
  private readonly http = inject(HttpClient);
  private readonly base = environment.apiUrl;

  /** Alertas crudas del backend. */
  private readonly raw = signal<Alert[]>([]);
  /** Tipos de alerta descartados en esta sesión (vistos o accionados). */
  private readonly dismissed = signal<ReadonlySet<string>>(new Set());

  /** Alertas pendientes (no descartadas) — para el panel y el badge. */
  readonly alerts = computed(() => this.raw().filter((a) => !this.dismissed().has(a.type)));
  readonly count = computed(() => this.alerts().length);

  /** Pide las alertas al backend y refresca el estado. */
  refresh(): Observable<AlertsResponse> {
    return this.http
      .get<AlertsResponse>(`${this.base}/notifications`)
      .pipe(tap((res) => this.raw.set(res.data)));
  }

  /** Descarta una alerta concreta (al accionarla). */
  dismiss(type: string): void {
    this.dismissed.update((set) => new Set(set).add(type));
  }

  /** Marca como vistas todas las alertas pendientes (al cerrar el panel). */
  dismissAll(): void {
    const types = this.alerts().map((a) => a.type);
    if (types.length === 0) return;
    this.dismissed.update((set) => {
      const next = new Set(set);
      types.forEach((t) => next.add(t));
      return next;
    });
  }
}
