# Prompt para la siguiente sesión

Copiá y pegá esto al iniciar la próxima sesión de Claude Code en este repo:

---

Estoy migrando el sistema de farmacia de Laravel+Livewire a **Laravel API (headless) + Angular 20 SPA**. Lee primero `CLAUDE.md`, `docs/MIGRATION_PLAN.md` y `docs/API.md` para el contexto completo, el estado y las convenciones.

Resumen: el backend API REST (`pharmacy-app/`, Sanctum, 48 rutas) está **completo**. El frontend (`pharmacy-frontend/`, Angular 20 standalone) ya tiene: login, dashboard, y CRUD de Productos, Clientes, Proveedores, Categorías, Facturación/POS (con PDF y anulación), Caja y Devoluciones. Trabajo en el branch `feature/laravel-api-angular`.

Quiero continuar con los módulos pendientes, en este orden: **1) Usuarios** (CRUD, solo Administrador), **2) Inventario/Kardex** (solo lectura con filtros), **3) Reportes** (ventas, top productos, inventario; con gráficos). Seguí el patrón replicable ya establecido (model + service + list/ + form/, reusando `shared/` y las clases de `styles.scss`), reemplazando los placeholders `ComingSoon` en `app.routes.ts`.

Notas de entorno (críticas):
- El **puerto 8000 lo ocupa el proyecto petlab** — no lo toques. Para probar, corré el backend en `--port=8001` y apuntá `pharmacy-frontend/src/environments/environment.ts` a `http://localhost:8001/api` temporalmente, **revirtiendo a 8000 antes de commitear**.
- MySQL `root` / `DaHg10@2000`, DB `pharmacy`. Para E2E creá un usuario demo Administrador temporal y borralo al final.
- Verificá cada módulo con `ng build` (sin warnings) + smoke test E2E con Playwright (usando `form.requestSubmit()` / `window.ng.getComponent()` en headless), limpiá datos de prueba y commiteá al branch.

Empezá por **Usuarios**. Avísame el plan antes de implementar si tenés dudas; si no, procedé.

---
