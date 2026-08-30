<div align="center">
  <img src="public/logo.png" alt="Logo de Hedman-Garcia Pharmacy" width="120">
  <h1>Frontend Hedman-Garcia Pharmacy</h1>
  <p><strong>SPA operativa para inventario, ventas, caja, devoluciones, reportes y administración.</strong></p>
</div>

---

Frontend principal del sistema de farmacia. La aplicación Angular consume la API REST de `../pharmacy-app/`, aplica navegación por rol y organiza cada dominio como un módulo independiente con rutas diferidas.

## Stack

| Capa | Tecnología |
| --- | --- |
| Runtime | Node.js 24 LTS y npm 11 |
| Framework | Angular 22.1 standalone |
| Lenguaje | TypeScript 6.0 |
| Reactividad | Signals, RxJS 7.8 y Zone.js 0.16 |
| Estilos | SCSS y tokens propios |
| Unit testing | Karma 6.4 y Jasmine 6.3 |
| E2E | Playwright 1.62 |

## Requisitos

- Node.js 24.15 o posterior dentro de la línea 24 LTS.
- Backend Laravel disponible en el puerto 8001 para pruebas E2E.

## Desarrollo

```bash
npm ci
NG_CLI_ANALYTICS=false npx ng serve --configuration e2e --port 4200
```

La configuración `e2e` reemplaza el entorno mediante `angular.json` y apunta a `http://localhost:8001/api`; no es necesario editar `environment.ts`.

## Verificación

```bash
NG_CLI_ANALYTICS=false npm run build -- --configuration development
NG_CLI_ANALYTICS=false npm run test:unit
npm run e2e
npm audit
```

La suite actual cubre 59 pruebas unitarias y 6 escenarios E2E para login, caja, devoluciones y facturación. El setup crea usuarios y productos temporales; el teardown comprueba que no queden facturas, movimientos, productos ni usuarios de prueba.

## Arquitectura

```text
src/app/
  core/            modelos, autenticación, interceptor, guards y tema
  layout/shell/    navegación principal filtrada por rol
  features/        módulos de negocio y servicios HTTP
  shared/          controles, diálogos, toast, paginación y gráficos
```

- Componentes standalone y rutas lazy con `loadComponent`.
- Token Bearer almacenado en `localStorage` y agregado por el interceptor.
- Roles Administrador y Cajero aplicados por guards y validados nuevamente por Laravel.
- Change Detection explícito para conservar el comportamiento de Angular 20 durante la migración a Angular 22.

## Módulos implementados

- Login, recuperación de contraseña y sesión.
- Dashboard y notificaciones operativas.
- Productos, clientes, proveedores y categorías.
- Facturación, POS, PDF y anulación.
- Caja, arqueo y movimientos.
- Devoluciones.
- Inventario y Kardex.
- Reportes y gráficos.
- Usuarios y roles.

## Documentación relacionada

- [Referencia de API](../docs/API.md)
- [Plan de migración](../docs/MIGRATION_PLAN.md)
- [Guía general del repositorio](../README.md)

## Licencia

Código disponible únicamente para evaluación. Consulta la [licencia propietaria](../LICENSE).
