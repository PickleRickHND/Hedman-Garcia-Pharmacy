import { Component, computed, inject, signal, ChangeDetectionStrategy } from '@angular/core';
import { FormControl, ReactiveFormsModule } from '@angular/forms';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { AuthService } from '../../core/auth/auth.service';
import { ThemeService } from '../../core/theme/theme.service';
import { Icon } from '../../shared/icon/icon';
import { NotificationsBell } from '../../shared/notifications/notifications-bell';
import { ToastHost } from '../../shared/toast/toast-host';

interface NavItem {
  label: string;
  path: string;
  icon: string;
  roles?: string[];
}
interface NavGroup {
  label: string;
  items: NavItem[];
}

@Component({
  selector: 'app-shell',
  imports: [RouterOutlet, RouterLink, RouterLinkActive, ReactiveFormsModule, Icon, ToastHost, NotificationsBell],
  templateUrl: './shell.html',
  changeDetection: ChangeDetectionStrategy.Eager,
  styleUrl: './shell.scss',
})
export class Shell {
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);
  readonly theme = inject(ThemeService);

  readonly user = this.auth.user;
  readonly collapsed = signal(false);
  readonly mobileOpen = signal(false);
  readonly globalSearch = new FormControl('', { nonNullable: true });

  private readonly groups: NavGroup[] = [
    {
      label: 'General',
      items: [{ label: 'Dashboard', path: '/dashboard', icon: 'dashboard' }],
    },
    {
      label: 'Operación',
      items: [
        { label: 'Facturación', path: '/invoices', icon: 'billing', roles: ['Administrador', 'Cajero'] },
        { label: 'Caja', path: '/cash-registers', icon: 'cash', roles: ['Administrador', 'Cajero'] },
        { label: 'Devoluciones', path: '/returns', icon: 'returns', roles: ['Administrador', 'Cajero'] },
      ],
    },
    {
      label: 'Catálogo',
      items: [
        { label: 'Productos', path: '/products', icon: 'products' },
        { label: 'Categorías', path: '/categories', icon: 'categories', roles: ['Administrador'] },
        { label: 'Proveedores', path: '/suppliers', icon: 'suppliers', roles: ['Administrador'] },
        { label: 'Inventario', path: '/stock-movements', icon: 'inventory', roles: ['Administrador', 'Cajero'] },
      ],
    },
    {
      label: 'Personas',
      items: [
        { label: 'Clientes', path: '/customers', icon: 'customers' },
        { label: 'Usuarios', path: '/users', icon: 'users', roles: ['Administrador'] },
      ],
    },
    {
      label: 'Análisis',
      items: [{ label: 'Reportes', path: '/reports', icon: 'reports', roles: ['Administrador'] }],
    },
  ];

  readonly navGroups = computed<NavGroup[]>(() => {
    const roles = this.user()?.roles ?? [];
    const canSee = (item: NavItem) => !item.roles || item.roles.some((r) => roles.includes(r));
    return this.groups
      .map((g) => ({ ...g, items: g.items.filter(canSee) }))
      .filter((g) => g.items.length > 0);
  });

  /** Búsqueda global: navega al catálogo filtrado por el término. */
  submitGlobalSearch(): void {
    const term = this.globalSearch.value.trim();
    if (term === '') return;
    this.router.navigate(['/products'], { queryParams: { search: term } });
    this.globalSearch.reset();
  }

  toggleSidebar(): void {
    this.collapsed.update((v) => !v);
  }

  toggleMobile(): void {
    this.mobileOpen.update((v) => !v);
  }

  logout(): void {
    this.auth.logout().subscribe({
      next: () => this.router.navigate(['/login']),
      error: () => this.router.navigate(['/login']),
    });
  }
}
