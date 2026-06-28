import { Routes } from '@angular/router';
import { authGuard, guestGuard, roleGuard } from './core/auth/auth.guard';

const ADMIN = ['Administrador'];
const STAFF = ['Administrador', 'Cajero'];

export const routes: Routes = [
  {
    path: 'login',
    canActivate: [guestGuard],
    loadComponent: () => import('./features/auth/login/login').then((m) => m.Login),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadComponent: () => import('./layout/shell/shell').then((m) => m.Shell),
    children: [
      { path: '', pathMatch: 'full', redirectTo: 'dashboard' },
      {
        path: 'dashboard',
        loadComponent: () => import('./features/dashboard/dashboard').then((m) => m.Dashboard),
      },

      // Módulos pendientes (placeholder hasta conectar su UI a la API).
      cs('products', 'Productos', 'products'),
      cs('categories', 'Categorías', 'categories', ADMIN),
      cs('suppliers', 'Proveedores', 'suppliers', ADMIN),
      cs('stock-movements', 'Inventario', 'inventory', STAFF),
      cs('customers', 'Clientes', 'customers'),
      cs('users', 'Usuarios', 'users', ADMIN),
      cs('invoices', 'Facturación', 'billing', STAFF),
      cs('cash-registers', 'Caja', 'cash', STAFF),
      cs('returns', 'Devoluciones', 'returns', STAFF),
      cs('reports', 'Reportes', 'reports', ADMIN),
    ],
  },
  { path: '**', redirectTo: '' },
];

/** Helper: ruta a placeholder ComingSoon con título/ícono y, opcionalmente, roles. */
function cs(path: string, title: string, icon: string, roles?: string[]) {
  return {
    path,
    data: { title, icon, roles },
    canActivate: roles ? [roleGuard] : [],
    loadComponent: () => import('./shared/coming-soon/coming-soon').then((m) => m.ComingSoon),
  };
}
