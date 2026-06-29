# Pharmacy Frontend — Angular 20

SPA del sistema de farmacia Hedman & Garcia. Consume la API REST de `pharmacy-app/`
(ver [../docs/API.md](../docs/API.md)).

## Requisitos

- Node 22.20+ (el proyecto está fijado en **Angular 20** por compatibilidad).
- Backend Laravel corriendo (ver [../CLAUDE.md](../CLAUDE.md)).

## Desarrollo

```bash
npm install
NG_CLI_ANALYTICS=false npx ng serve --port 4200
```

La URL de la API se configura en `src/environments/environment.ts` (`apiUrl`, default
`http://localhost:8000/api`). Si el backend corre en otro puerto (p. ej. 8001 por
conflicto con otro proyecto), ajustar ahí.

## Build

```bash
NG_CLI_ANALYTICS=false npx ng build --configuration development
```

## Arquitectura

```
src/app/
  core/            modelos, AuthService, interceptor Bearer, guards, ThemeService
  layout/shell/    shell del admin (sidebar + topbar, nav filtrado por rol)
  features/        módulos de negocio (auth, dashboard, products, customers,
                   suppliers, categories, billing, cash-register, returns)
  shared/          Icon, Toast, ConfirmDialog, Pagination, ComingSoon
```

- **Standalone components**, routing lazy con `loadComponent` y guards (`authGuard`,
  `guestGuard`, `roleGuard`).
- **Auth:** token Bearer en `localStorage`; el interceptor lo adjunta y, ante 401,
  limpia sesión y redirige a `/login`.
- **Diseño:** tokens claro/oscuro y clases utilitarias en `src/styles.scss`.
  Tipografías Plus Jakarta Sans / Inter / IBM Plex Mono (datos en monoespaciada).

## Módulos implementados

Login · Dashboard · Productos · Clientes · Proveedores · Categorías ·
Facturación/POS (con PDF y anulación) · Caja (arqueo) · Devoluciones.

Pendientes (placeholders): Inventario/Kardex, Reportes, Usuarios.
Ver el estado completo en [../docs/MIGRATION_PLAN.md](../docs/MIGRATION_PLAN.md).
