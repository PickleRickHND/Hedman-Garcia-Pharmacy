# CLAUDE.md — Hedman & Garcia Pharmacy

Guía para trabajar en este repositorio. Idioma del proyecto: **español** (UI, comentarios, mensajes de commit).

## Estructura del repositorio (3 partes)

| Carpeta | Qué es | Estado |
|---|---|---|
| Raíz (`/`) | Sistema **legacy** en PHP plano (sin framework) + MySQLi + Bootstrap/jQuery | Histórico, no se desarrolla |
| `pharmacy-app/` | Backend **Laravel 13** (API REST headless + Livewire legado) | Activo — la API es la fuente de verdad |
| `pharmacy-frontend/` | Frontend **Angular 22** (SPA que consume la API) | Activo — módulos de negocio completos |

La migración en curso: de **Laravel + Livewire** a **Laravel API (headless) + Angular SPA**. El Livewire de `pharmacy-app/` sigue funcionando en paralelo; la API y el frontend Angular son el camino nuevo. Ver [docs/MIGRATION_PLAN.md](docs/MIGRATION_PLAN.md) para el plan completo y el estado.

## Stack

- **Backend:** PHP 8.4+, Laravel 13.29, Sanctum 4.3 (token Bearer), spatie/laravel-permission 8.3, Livewire 3.8, Volt 1.11, dompdf 3.1 y MySQL. Lógica de negocio en `app/Services/` (BillingService, InventoryService, CashRegisterService, ReturnService, ReportService, NotificationService).
- **Frontend:** Node.js 24 LTS, Angular 22.1 standalone, TypeScript 6.0, RxJS 7.8 y SCSS, con routing lazy y guards. Sin librería de componentes; UI propia con tokens en `src/styles.scss`. Gráficos con `BarChart` propio.
- **Testing:** Pest (backend, incluye tests de la API en `tests/Feature/Api/`), Karma + Jasmine (unit frontend, `HttpTestingController`), Playwright **versionado** (`pharmacy-frontend/e2e/`, configuración `e2e` → :8001) cubriendo login + flujos críticos.
- **Marca:** monograma **HG** (teal `#12a594 → #0b6557`). Assets en `pharmacy-frontend/public/` (`logo.png`, `favicon.ico`, `apple-touch-icon.png`); login y sidebar usan el logo.

## Comandos

**Backend** (`cd pharmacy-app`):
```bash
php artisan serve --port=8001     # ⚠ usar 8001: el 8000 lo ocupa el proyecto petlab
php artisan route:list --path=api # listar rutas API
php -l <archivo.php>              # verificar sintaxis
php artisan tinker --execute="…"  # consultas rápidas
```

**Frontend** (`cd pharmacy-frontend`):
```bash
NG_CLI_ANALYTICS=false npm run build -- --configuration development      # verificar compilación
NG_CLI_ANALYTICS=false npx ng serve --configuration e2e --port 4200      # dev server apuntando a :8001 (no toca environment.ts)
CHROME_BIN="…/Google Chrome" NG_CLI_ANALYTICS=false npm run test:unit    # unit (Karma/Jasmine)
npm run e2e                                                              # E2E Playwright (levanta backend :8001 + Angular)
```
`ng` no está global; usar `npx` o los scripts npm. Los dos proyectos Node están fijados en Node.js 24 LTS mediante `.nvmrc` y `engines`.

## Entorno local (importante)

- **MySQL:** configura host, usuario, contraseña y la base `pharmacy` exclusivamente mediante `pharmacy-app/.env`. La base legacy es `FarmaciaHG`. Nunca escribas credenciales en documentación o archivos rastreados.
- **Puerto 8000 ocupado por petlab** (`/Development/petlabhn`). Para correr/probar la farmacia: backend en **8001**. `ng serve --configuration e2e` apunta la API a :8001 vía `fileReplacements` (`environment.e2e.ts`) **sin tocar** `environment.ts`. Nunca matar el server de petlab.
- **CORS:** el backend permite el origen del frontend vía `FRONTEND_URL` (`.env`, default `http://localhost:4200`).
- **Usuarios:** `admin@pharmacy.hn` (rol Administrador). Roles: **Administrador**, **Cajero**. Para pruebas E2E se crea un usuario demo temporal y se borra al final.

## Convenciones del dominio

- **Moneda:** Lempira (HNL). En el frontend: `Intl.NumberFormat('es-HN', {style:'currency', currency:'HNL'})`.
- **ISV:** 15% (`config('pharmacy.tax.isv_rate')`). Los totales se calculan en el backend (`BillingService`); el POS muestra un preview con la misma fórmula.
- **Roles en API:** escritura de catálogo/productos/proveedores/categorías/usuarios/reportes = `Administrador`; ventas/caja/devoluciones (lectura+algunas escrituras) = `Administrador|Cajero`; anular factura y crear devolución = solo `Administrador`. Aplicado con middleware `role:` en rutas y `roleGuard` en Angular.

## Patrón de módulos del frontend (replicable)

Cada módulo de negocio sigue: `core/models/<x>.model.ts` + `features/<x>/<x>.service.ts` + `features/<x>/list/` (tabla, búsqueda con debounce, paginación, borrado con `ConfirmDialog`) + `features/<x>/form/` (alta/edición, mapeo de errores 422). Componentes compartidos en `shared/`: `Icon`, `ToastService`+`ToastHost`, `ConfirmDialog`, `Pagination`. Estilos comunes de listado/formulario en `styles.scss` (`.page-head`, `.toolbar`, `.table-card`, `.form-grid`, etc.).

**Reactividad:** no usar `FormControl.valid` dentro de un `computed` (no es señal). Para habilitar botones según validez, derivar con `toSignal(control.valueChanges)`.

## Verificación

- **Backend:** `php -l` + `php artisan route:list` + `php artisan test` (222 tests; los de la API viven en `tests/Feature/Api/`). Las pruebas usan SQLite en memoria.
- **Frontend:** `npm run build -- --configuration development` + `npm run test:unit` (59 pruebas). E2E: harness **versionado** `npm run e2e` (6 escenarios; `global-setup`/`teardown` crean y borran usuario demo + productos `E2E-*`). En headless el click sobre `button[type=submit]` no siempre propaga; usar `form.requestSubmit()` o `window.ng.getComponent(host)`. Para smoke ad-hoc, limpiar datos y artefactos temporales.

## Git

- Rama principal: `production` (antes `master`; renombrada). La migración Angular ya fue integrada; no asumas una rama de trabajo sin verificar `git branch --show-current`.
- Conventional Commits en español. **No** incluir footer "Generated with Claude Code" ni "Co-Authored-By".
- Escanear secretos antes de commitear; `.env` está ignorado.
