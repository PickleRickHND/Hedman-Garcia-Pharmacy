# Prompt para la siguiente sesión

Copiá y pegá este contexto al iniciar una nueva sesión de trabajo en el repositorio:

---

Estoy manteniendo Hedman-Garcia Pharmacy, una plataforma con backend Laravel 13 y frontend Angular 22. Lee primero `CLAUDE.md`, `docs/MIGRATION_PLAN.md` y `docs/API.md`.

La API REST en `pharmacy-app/` es la fuente de verdad. La SPA en `pharmacy-frontend/` ya cubre login, dashboard, productos, clientes, proveedores, categorías, facturación, caja, devoluciones, usuarios, Kardex y reportes. Livewire 3 y Volt 1 permanecen únicamente como interfaz de transición.

Reglas de entorno:

- No tocar el puerto 8000 porque pertenece a PetLab.
- Para pruebas usa Laravel en 8001 y Angular con la configuración `e2e` en 4200.
- Configura MySQL y cualquier credencial únicamente mediante archivos `.env` ignorados.
- El E2E versionado crea y elimina sus propios usuarios y productos temporales.
- No ejecutes migraciones ni modifiques datos sin autorización explícita.

Antes de cerrar cambios ejecuta:

```bash
cd pharmacy-app
composer audit --locked
php artisan test
npm run build
npm audit

cd ../pharmacy-frontend
npm run build -- --configuration development
npm run test:unit
npm run e2e
npm audit
```

Trabaja en español, usa Conventional Commits y presenta un plan antes de cambios estructurales.

---
