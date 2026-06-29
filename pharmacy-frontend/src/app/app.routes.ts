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

      // Productos (conectado a la API).
      {
        path: 'products',
        loadComponent: () => import('./features/products/list/product-list').then((m) => m.ProductList),
      },
      {
        path: 'products/new',
        canActivate: [roleGuard],
        data: { roles: ADMIN },
        loadComponent: () => import('./features/products/form/product-form').then((m) => m.ProductForm),
      },
      {
        path: 'products/:id/edit',
        canActivate: [roleGuard],
        data: { roles: ADMIN },
        loadComponent: () => import('./features/products/form/product-form').then((m) => m.ProductForm),
      },

      // Clientes (conectado a la API).
      {
        path: 'customers',
        loadComponent: () => import('./features/customers/list/customer-list').then((m) => m.CustomerList),
      },
      {
        path: 'customers/new',
        canActivate: [roleGuard],
        data: { roles: STAFF },
        loadComponent: () => import('./features/customers/form/customer-form').then((m) => m.CustomerForm),
      },
      {
        path: 'customers/:id/edit',
        canActivate: [roleGuard],
        data: { roles: STAFF },
        loadComponent: () => import('./features/customers/form/customer-form').then((m) => m.CustomerForm),
      },

      // Proveedores (conectado a la API).
      {
        path: 'suppliers',
        loadComponent: () => import('./features/suppliers/list/supplier-list').then((m) => m.SupplierList),
      },
      {
        path: 'suppliers/new',
        canActivate: [roleGuard],
        data: { roles: ADMIN },
        loadComponent: () => import('./features/suppliers/form/supplier-form').then((m) => m.SupplierForm),
      },
      {
        path: 'suppliers/:id/edit',
        canActivate: [roleGuard],
        data: { roles: ADMIN },
        loadComponent: () => import('./features/suppliers/form/supplier-form').then((m) => m.SupplierForm),
      },

      // Categorías (conectado a la API).
      {
        path: 'categories',
        loadComponent: () => import('./features/categories/list/category-list').then((m) => m.CategoryList),
      },
      {
        path: 'categories/new',
        canActivate: [roleGuard],
        data: { roles: ADMIN },
        loadComponent: () => import('./features/categories/form/category-form').then((m) => m.CategoryForm),
      },
      {
        path: 'categories/:id/edit',
        canActivate: [roleGuard],
        data: { roles: ADMIN },
        loadComponent: () => import('./features/categories/form/category-form').then((m) => m.CategoryForm),
      },

      // Facturación / POS (conectado a la API). Toda la sección: Admin o Cajero.
      {
        path: 'invoices',
        canActivate: [roleGuard],
        data: { roles: STAFF },
        loadComponent: () => import('./features/billing/list/invoice-list').then((m) => m.InvoiceList),
      },
      {
        path: 'invoices/new',
        canActivate: [roleGuard],
        data: { roles: STAFF },
        loadComponent: () => import('./features/billing/pos/pos').then((m) => m.Pos),
      },
      {
        path: 'invoices/:id',
        canActivate: [roleGuard],
        data: { roles: STAFF },
        loadComponent: () => import('./features/billing/detail/invoice-detail').then((m) => m.InvoiceDetail),
      },

      // Módulos pendientes (placeholder hasta conectar su UI a la API).
      cs('stock-movements', 'Inventario', 'inventory', STAFF),
      cs('users', 'Usuarios', 'users', ADMIN),
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
