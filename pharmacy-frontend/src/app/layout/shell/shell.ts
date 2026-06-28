import { Component, computed, inject, signal } from '@angular/core';
import { Router, RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { AuthService } from '../../core/auth/auth.service';
import { ThemeService } from '../../core/theme/theme.service';
import { Icon } from '../../shared/icon/icon';

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
  imports: [RouterOutlet, RouterLink, RouterLinkActive, Icon],
  templateUrl: './shell.html',
  styleUrl: './shell.scss',
})
export class Shell {
  private readonly auth = inject(AuthService);
  private readonly router = inject(Router);
  readonly theme = inject(ThemeService);

  readonly user = this.auth.user;
  readonly collapsed = signal(false);
  readonly mobileOpen = signal(false);

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
