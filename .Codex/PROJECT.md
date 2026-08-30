# Hedman-Garcia Pharmacy

## Technology refresh, 2026-08-30

Status: locally complete; commit and push pending owner authorization.

### Runtime and dependencies

- Legacy PHP: PHP 8.2-compatible metadata and SendGrid 8.1.11.
- Backend: PHP 8.4+, Laravel 13.29, Sanctum 4.3, Spatie Permission 8.3, Pest 5.1, PHPUnit 13.3, Livewire 3.8 and Volt 1.11.
- Backend assets: Node.js 24 LTS, npm 11, Vite 8.2, Laravel Vite Plugin 3.2, Axios 1.20 and Tailwind CSS 3.4.
- Frontend: Node.js 24 LTS, npm 11, Angular 22.1, TypeScript 6.0, RxJS 7.8, Zone.js 0.16, Jasmine 6.3 and Playwright 1.62.
- Composer and npm audits report zero known vulnerabilities in all three dependency trees.

### Compatibility decisions

- Laravel migrated incrementally from 11 to 12 and then 13.
- Angular migrated incrementally from 20 to 21 and then 22 through official CLI migrations.
- Session serialization remains `php` to preserve active sessions. Moving to JSON is a separate rollout because it invalidates existing sessions.
- Arbitrary PHP class deserialization from cache is disabled.
- Sanctum uses Laravel 13's `PreventRequestForgery` middleware.
- Livewire 3 and Volt 1 remain because they support Laravel 13 and represent a transitional UI while Angular is the target frontend.
- Tailwind 3 remains on the Livewire frontend to avoid an unrelated visual migration.
- No database migration or remote configuration change is part of this refresh.

### Verification record

- Legacy Composer validation and audit pass.
- Backend Composer validation and audit pass; el inventario contiene 51 rutas API.
- Backend: 222 tests and 601 assertions pass against SQLite in memory.
- Backend Vite build passes and its npm audit is clean.
- Angular development build passes.
- Angular: 59 Karma/Jasmine tests pass.
- Playwright: 6 critical scenarios pass; teardown confirms no residual invoices, movements, products or demo user.
- Angular npm audit is clean.
- Browser verification: login renders correctly at 1440x900 and an emulated 390x844 mobile viewport, with no console warnings or errors, broken images, overlays or horizontal overflow.

### Known external warning

The workstation PHP configuration still declares deprecated session INI settings. They are outside this repository and do not fail the application or test suite.

### Rollback

Before commit, restore the tracked diff. After publication, revert the technology-refresh commit. Dependency and configuration changes are additive to source control and do not mutate production data.
