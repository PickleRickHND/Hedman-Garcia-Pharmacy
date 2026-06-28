import { Component, computed, inject, OnInit, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../../environments/environment';
import { AuthService } from '../../core/auth/auth.service';
import { Icon } from '../../shared/icon/icon';

interface DashboardMetrics {
  users_total: number;
  users_admins: number;
  users_cashiers: number;
  products_total: number;
  low_stock: number;
  expiring_soon: number;
  invoices_today: number;
  revenue_today: number;
}
interface DashboardAlert {
  type: string;
  label: string;
  count: number;
  variant: string;
}
interface DashboardData {
  metrics: DashboardMetrics;
  alerts: DashboardAlert[];
  alerts_count: number;
}

interface MetricCard {
  label: string;
  value: string;
  icon: string;
  tone?: 'accent' | 'warning' | 'danger';
}

@Component({
  selector: 'app-dashboard',
  imports: [Icon],
  templateUrl: './dashboard.html',
  styleUrl: './dashboard.scss',
})
export class Dashboard implements OnInit {
  private readonly http = inject(HttpClient);
  private readonly auth = inject(AuthService);

  readonly user = this.auth.user;
  readonly loading = signal(true);
  readonly error = signal<string | null>(null);
  readonly data = signal<DashboardData | null>(null);

  readonly cards = computed<MetricCard[]>(() => {
    const m = this.data()?.metrics;
    if (!m) return [];
    return [
      { label: 'Ingresos de hoy', value: this.money(m.revenue_today), icon: 'cash', tone: 'accent' },
      { label: 'Facturas de hoy', value: String(m.invoices_today), icon: 'billing' },
      { label: 'Productos', value: String(m.products_total), icon: 'products' },
      { label: 'Stock bajo', value: String(m.low_stock), icon: 'inventory', tone: m.low_stock > 0 ? 'warning' : undefined },
      { label: 'Por vencer', value: String(m.expiring_soon), icon: 'alert', tone: m.expiring_soon > 0 ? 'warning' : undefined },
      { label: 'Usuarios', value: String(m.users_total), icon: 'users' },
    ];
  });

  ngOnInit(): void {
    this.load();
  }

  load(): void {
    this.loading.set(true);
    this.error.set(null);
    this.http.get<DashboardData>(`${environment.apiUrl}/dashboard`).subscribe({
      next: (d) => {
        this.data.set(d);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('No pudimos cargar el panel. Intenta de nuevo.');
        this.loading.set(false);
      },
    });
  }

  greeting(): string {
    const h = new Date().getHours();
    return h < 12 ? 'Buenos días' : h < 19 ? 'Buenas tardes' : 'Buenas noches';
  }

  private money(value: number): string {
    return new Intl.NumberFormat('es-HN', { style: 'currency', currency: 'HNL' }).format(value);
  }
}
