<div align="center">
  <img src="pharmacy-frontend/public/logo.png" alt="Logo de Hedman-Garcia Pharmacy" width="130">
  <h1>Hedman-Garcia Pharmacy</h1>
  <p><strong>Plataforma integral para inventario, facturación, caja, devoluciones y operación farmacéutica.</strong></p>
</div>

---

Sistema de gestión para farmacias que evoluciona desde una aplicación PHP legacy hacia una arquitectura desacoplada con API REST en Laravel y una SPA en Angular. El backend centraliza reglas de negocio y autorización; el frontend ofrece una interfaz modular para los flujos operativos.

[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11.31-FF2D20?logo=laravel)](https://laravel.com/)
[![Sanctum](https://img.shields.io/badge/Sanctum-4-FF2D20?logo=laravel)](https://laravel.com/docs/11.x/sanctum)
[![Angular](https://img.shields.io/badge/Angular-20.3-DD0031?logo=angular)](https://angular.dev/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.9-3178C6?logo=typescript)](https://www.typescriptlang.org/)
[![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql)](https://www.mysql.com/)
[![Playwright](https://img.shields.io/badge/E2E-Playwright-2EAD33?logo=playwright)](https://playwright.dev/)
[![License](https://img.shields.io/badge/License-Proprietary-red.svg)](LICENSE)

## Tabla de contenidos

- [Estado y alcance](#estado-y-alcance)
- [Funcionalidades](#funcionalidades)
- [Stack tecnológico](#stack-tecnológico)
- [Arquitectura](#arquitectura)
- [Módulos activos](#módulos-activos)
- [Seguridad](#seguridad)
- [Estructura del repositorio](#estructura-del-repositorio)
- [Desarrollo local](#desarrollo-local)
- [Pruebas](#pruebas)
- [Documentación](#documentación)
- [Licencia](#licencia)

---

## Estado y alcance

El repositorio contiene tres generaciones claramente separadas:

| Ubicación            | Tecnología                                           | Estado                                |
| -------------------- | ---------------------------------------------------- | ------------------------------------- |
| Raíz                 | PHP plano, MySQLi, Bootstrap y jQuery                | Histórico, conservado como referencia |
| `pharmacy-app/`      | Laravel 11, Sanctum, servicios de dominio y API REST | Backend activo y fuente de verdad     |
| `pharmacy-frontend/` | Angular 20 standalone, SCSS y rutas lazy             | Frontend activo                       |

La dirección vigente es Laravel como API headless y Angular como SPA. Las vistas Livewire del backend continúan disponibles durante la transición, pero no representan la arquitectura objetivo.

## Funcionalidades

| Área         | Capacidades                                                             |
| ------------ | ----------------------------------------------------------------------- |
| Acceso       | Login, tokens Sanctum, perfiles, roles Administrador y Cajero           |
| Inventario   | Productos, categorías, proveedores, stock, vencimientos y alertas       |
| Facturación  | Punto de venta, carrito, cálculo de ISV, recibos y anulación controlada |
| Caja         | Apertura, movimientos, cierre y conciliación operativa                  |
| Devoluciones | Flujo autorizado con reintegro de inventario                            |
| Reportes     | Ventas, inventario, caja y métricas administrativas                     |
| Usuarios     | Gestión de cuentas y permisos desde la SPA                              |
| Experiencia  | Dashboard, tablas, formularios, confirmaciones y notificaciones         |

## Stack tecnológico

### Backend

| Capa                | Tecnología                  |
| ------------------- | --------------------------- |
| Runtime             | PHP 8.2 o superior          |
| Framework           | Laravel 11.31               |
| API y autenticación | REST + Laravel Sanctum 4    |
| Autorización        | spatie/laravel-permission 6 |
| Persistencia        | MySQL y Eloquent ORM        |
| Documentos          | dompdf 3                    |
| Pruebas             | Pest                        |

### Frontend

| Capa         | Tecnología                     |
| ------------ | ------------------------------ |
| Framework    | Angular 20.3 standalone        |
| Lenguaje     | TypeScript 5.9                 |
| Reactividad  | Signals y RxJS 7.8             |
| Estilos      | SCSS con sistema visual propio |
| Unit testing | Karma + Jasmine                |
| E2E          | Playwright 1.61                |

## Arquitectura

```text
Angular SPA :4200
      |
      | Bearer token
      v
Laravel REST API :8001
      |
      +-- Middleware de autenticación y roles
      +-- Form Requests y controladores API
      +-- Services de dominio
      |     +-- BillingService
      |     +-- InventoryService
      |     +-- CashRegisterService
      |     +-- ReturnService
      |     +-- ReportService
      +-- Eloquent + transacciones
              |
              v
            MySQL
```

Los cálculos fiscales y las reglas críticas residen en el backend. La SPA consume modelos tipados y servicios HTTP; no replica decisiones de negocio sensibles.

## Módulos activos

- Dashboard y resumen operativo.
- Productos, categorías, proveedores e inventario.
- Punto de venta y facturación.
- Caja y movimientos.
- Devoluciones.
- Reportes.
- Usuarios y autorización.
- Perfil y sesión.

Las rutas Angular se cargan de forma diferida y utilizan guards de autenticación y rol. Los endpoints aplican la misma política en el servidor.

## Seguridad

- Autenticación mediante tokens Bearer de Sanctum.
- Roles validados tanto en la API como en el router del frontend.
- Validación de payloads con Form Requests.
- Consultas mediante Eloquent o parámetros preparados.
- CORS restringido al origen configurado del frontend.
- Contraseñas, claves y credenciales fuera del repositorio.
- Operaciones críticas protegidas por transacciones.

El README no publica usuarios ni contraseñas de demostración. Los datos de prueba se crean de forma temporal mediante los harnesses correspondientes.

## Estructura del repositorio

```text
Hedman-Garcia-Pharmacy/
├── pharmacy-app/               # Laravel API, dominio, migraciones y pruebas Pest
├── pharmacy-frontend/          # Angular SPA, unit tests y Playwright
├── docs/                       # Plan de migración y documentación técnica
├── controllers/                # Controladores del sistema PHP histórico
├── screens/                    # Pantallas del sistema PHP histórico
├── database/                   # Schema y migraciones legacy
└── README.md                   # Punto de entrada actualizado
```

## Desarrollo local

### Backend

```bash
cd pharmacy-app
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve --port=8001
```

Configura una base MySQL de desarrollo y `FRONTEND_URL=http://localhost:4200`. No uses credenciales reales en `.env`.

### Frontend

```bash
cd pharmacy-frontend
npm install
NG_CLI_ANALYTICS=false npx ng serve --configuration e2e --port 4200
```

La configuración `e2e` apunta al backend local en el puerto 8001 sin modificar la configuración normal del proyecto.

## Pruebas

```bash
# Backend
cd pharmacy-app
php artisan test

# Frontend unit
cd ../pharmacy-frontend
NG_CLI_ANALYTICS=false npx ng test --watch=false --browsers=ChromeHeadless

# Flujos E2E
npm run e2e
```

La cobertura incluye pruebas de API, servicios de dominio, componentes Angular y flujos críticos de login, inventario y facturación.

## Documentación

- [Plan de migración](docs/MIGRATION_PLAN.md)
- [Referencia de API](docs/API.md)
- [Guía de implementación](IMPLEMENTATION_GUIDE.md)
- [Backend Laravel](pharmacy-app/README.md)
- [Frontend Angular](pharmacy-frontend/README.md)

## Licencia

Código disponible únicamente para evaluación. Consulta la [licencia propietaria](LICENSE) para conocer las restricciones de uso, copia y distribución.
