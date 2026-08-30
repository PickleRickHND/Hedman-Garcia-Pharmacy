# Plan de migración a Angular 22: Hedman & Garcia Pharmacy

> Documento de handoff. Resume el plan acordado, lo hecho y lo pendiente para
> continuar en una próxima sesión. Última actualización: 2026-08-30.

## 1. Contexto y decisión

El sistema de farmacia (`pharmacy-app/`) estaba hecho en Laravel con Livewire.
El objetivo es **migrar el frontend a Angular** (proyecto de portafolio: demostrar
Angular full-stack y arquitectura desacoplada, muy demandada en ofertas).

**Decisión de arquitectura (clave):** NO se reescribe el backend. Se reutiliza todo
lo existente — modelos, migraciones y la capa de **Services** — exponiéndolo como
**API REST con Sanctum (token Bearer)**. Angular consume esa API. El Livewire/Blade
queda intacto y funciona en paralelo durante la transición.

Esto fue acertado porque el backend ya tenía la lógica de negocio separada en
Services (`BillingService`, `InventoryService`, `CashRegisterService`,
`ReturnService`, `ReportService`, `NotificationService`): los controllers de la API
solo validan y delegan, sin duplicar lógica.

```
Antes:  Laravel + Livewire + Blade  ──►  MySQL
Ahora:  Angular 22 SPA  ──HTTP/JSON──►  Laravel 13 API (Sanctum) ──► Services ──► MySQL
```

## 2. Estado actual

### Backend — API REST: COMPLETO (51 rutas)
Sanctum + CORS + `routes/api.php`. Recursos: auth, products, customers, suppliers,
categories, invoices (+PDF +void +payment-methods), cash-registers, returns,
stock-movements, reports, dashboard, users/roles. Detalle en
[API.md](API.md). Autorización por rol con middleware `role:` (paridad con los
checks de los componentes Livewire).

### Frontend — Angular 22: MÓDULOS DE NEGOCIO COMPLETOS
Proyecto en `pharmacy-frontend/`. Implementado y verificado E2E:

| Módulo | Estado |
|---|---|
| Login + sesión (token, guards, interceptor) | ✅ |
| Shell admin (sidebar nav por rol, topbar, tema claro/oscuro) | ✅ |
| Dashboard (métricas + alertas) | ✅ |
| Productos (CRUD + filtros) | ✅ |
| Clientes (CRUD) | ✅ |
| Proveedores (CRUD + activo/inactivo) | ✅ |
| Categorías (CRUD + color) | ✅ |
| Facturación / POS (carrito, ISV, emisión, PDF, anulación) | ✅ |
| Caja (abrir/cerrar con arqueo + historial) | ✅ |
| Devoluciones (lista, crear sobre factura, detalle) | ✅ |
| Usuarios (CRUD + roles, filtro por rol) | ✅ |
| Inventario / Kardex (solo lectura, filtros producto/tipo/fechas) | ✅ |
| Reportes (ventas, top productos, inventario; con gráficos) | ✅ |

Rama principal vigente: `production`.

Con esto **todos los módulos de negocio están migrados**. El frontend Angular
cubre la funcionalidad del Livewire.

## 3. Pendiente (próximos pasos sugeridos, en orden)

1. **Pulido final:** estados de carga/skeleton consistentes, responsive del POS y
   tablas en móvil, selección de cliente existente en el POS (hoy es texto libre),
   `fileReplacements` en `angular.json` para un `environment.prod` real.
2. **Tests:** unit Karma/Jasmine de servicios + `BarChart`, tests de API (Pest) y E2E
   Playwright versionado (`pharmacy-frontend/e2e/`, config `e2e` → :8001) cubriendo
   login + flujos críticos (Caja, POS con PDF y anulación, Devoluciones), con datos
   aislados (`E2E-*`) y teardown completo. Pendiente: cobertura E2E de descuentos por
   línea y casos negativos.
3. Eventual **retiro del Livewire** una vez el frontend cubra todo.

## 4. Cómo levantar el entorno (para probar)

> ⚠ El puerto **8000 lo ocupa el proyecto petlab** (`/Development/petlabhn`). No matarlo.

```bash
# Backend (en pharmacy-app/)
php artisan serve --port=8001

# Frontend (en pharmacy-frontend/)
NG_CLI_ANALYTICS=false npx ng serve --configuration e2e --port 4200
```

- MySQL: configura las credenciales y la base `pharmacy` únicamente mediante `pharmacy-app/.env`.
- Usuario admin: `admin@pharmacy.hn` (rol Administrador; password no documentada —
  para pruebas crear un usuario demo temporal vía `tinker` y borrarlo al final).
- CORS permite `http://localhost:4200` (`FRONTEND_URL` en `.env`).

## 5. Convenciones a respetar

- Idioma **español** en UI, comentarios y commits. Conventional Commits, sin footer
  de Claude.
- Moneda **HNL**, **ISV 15%** (los totales los calcula el backend).
- Roles: ver tabla en [CLAUDE.md](../CLAUDE.md). Escritura de catálogo y reportes =
  Administrador; ventas/caja/devoluciones = Administrador o Cajero; anular/devolver =
  solo Administrador.
- **Patrón de módulo frontend:** `model` + `service` + `list/` + `form/`, reusando
  `shared/` (Icon, Toast, ConfirmDialog, Pagination) y las clases de `styles.scss`.
- **Reactividad:** derivar validez de formularios para `computed`/botones con
  `toSignal(control.valueChanges)`, nunca leer `FormControl.valid` dentro de un
  `computed` (no es señal).

## 6. Verificación antes de cerrar cada módulo

1. `npm run build -- --configuration development` sin warnings.
2. `npm run test:unit` con 59 pruebas verdes.
3. `npm run e2e`: login → operar el módulo contra datos temporales.
   En headless usar `form.requestSubmit()` o `window.ng.getComponent(host)` para
   conducir el componente (el click sobre submit no siempre propaga).
4. Limpiar datos/usuarios/tokens de prueba; el teardown debe confirmar cero residuos.
5. Confirmar que petlab sigue vivo en :8000 y que no se colaron artefactos
   (`.playwright-mcp`, `*.png`) — ya están en `.gitignore`.
6. Commit + push a la rama autorizada.
