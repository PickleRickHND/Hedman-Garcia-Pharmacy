/** Alerta operativa en tiempo real (campana del topbar). */
export interface Alert {
  type: 'low_stock' | 'out_of_stock' | 'expired' | 'expiring' | 'cash_closed';
  label: string;
  count: number;
  variant: 'warning' | 'danger' | 'info';
}

/** Respuesta del endpoint GET /api/notifications. */
export interface AlertsResponse {
  data: Alert[];
  count: number;
}
