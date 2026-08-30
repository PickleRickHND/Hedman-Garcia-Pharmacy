<div align="center">
  <img src="../pharmacy-frontend/public/logo.png" alt="Logo de Hedman-Garcia Pharmacy" width="120">
  <h1>Backend Hedman-Garcia Pharmacy</h1>
  <p><strong>API REST, servicios de dominio y compatibilidad Livewire para la operación farmacéutica.</strong></p>
</div>

---

Backend principal del sistema de farmacia. Laravel concentra autenticación, autorización, inventario, facturación, caja, devoluciones, reportes y usuarios. Angular consume la API REST; Livewire se conserva temporalmente como interfaz paralela durante la transición.

## Stack

| Capa | Tecnología |
| --- | --- |
| Runtime | PHP 8.4 o superior |
| Framework | Laravel 13.29 |
| Autenticación | Sanctum 4.3 |
| Roles | spatie/laravel-permission 8.3 |
| UI transitoria | Livewire 3.8 y Volt 1.11 |
| PDF | dompdf 3.1 |
| Assets | Vite 8.2, Laravel Vite Plugin 3.2 y Tailwind CSS 3.4 |
| Pruebas | Pest 5 sobre PHPUnit 13 |

## Desarrollo local

```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve --port=8001
```

El puerto 8001 evita interferir con otros proyectos locales. Configura MySQL y `FRONTEND_URL=http://localhost:4200` mediante `.env`; nunca escribas credenciales reales en documentación o archivos rastreados.

## Comandos

```bash
php artisan route:list --path=api
php artisan test
npm run build
composer audit --locked
npm audit
```

Las pruebas usan SQLite en memoria y no modifican MySQL. La configuración mantiene serialización PHP para preservar sesiones existentes y bloquea la deserialización de clases arbitrarias desde caché.

## Arquitectura

```text
Angular SPA
    |
    | Bearer token
    v
Laravel API
    +-- Form Requests
    +-- Controladores API
    +-- Services de dominio
    +-- Eloquent y transacciones
            |
            v
          MySQL
```

La referencia completa de endpoints vive en [docs/API.md](../docs/API.md). El estado de la transición se documenta en [docs/MIGRATION_PLAN.md](../docs/MIGRATION_PLAN.md).

## Compatibilidad

- Livewire 3 y Volt 1 se mantienen porque soportan Laravel 13 y representan una interfaz de transición.
- La interfaz objetivo es la SPA Angular de `../pharmacy-frontend/`.
- Tailwind 3 se conserva para evitar un rediseño accidental del frontend Livewire.
- No se requieren migraciones de base de datos para esta actualización tecnológica.

## Licencia

Código disponible únicamente para evaluación. Consulta la [licencia propietaria](../LICENSE).
