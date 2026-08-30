# API REST — Hedman & Garcia Pharmacy

Backend Laravel 13 expuesto como API REST headless para el frontend Angular 22.
Base URL local: `http://localhost:8001/api` (ver nota de puertos en [CLAUDE.md](../CLAUDE.md)).

## Autenticación

Token Bearer con **Laravel Sanctum**. Tras el login, enviar en cada petición:

```
Authorization: Bearer <token>
Accept: application/json
```

| Método | Ruta | Descripción | Roles |
|---|---|---|---|
| POST | `/login` | Devuelve `{ token, user }` | Pública |
| POST | `/logout` | Revoca el token actual | Autenticado |
| GET | `/me` | Usuario autenticado | Autenticado |

`user` incluye: `id, name, email, initials, role, roles[], permissions[], must_change_password`.

## Convenciones

- Listados paginados devuelven `{ data, links, meta }` (paginación Laravel). `meta.current_page`, `meta.last_page`, `meta.total`.
- Recursos singulares y listas no paginadas devuelven `{ data }`.
- Errores de validación: **422** con `{ message, errors: { campo: [mensaje] } }`.
- Errores de negocio (stock insuficiente, caja ya abierta, etc.): **422** con `{ message }`.
- Sin permiso: **403**. Sin token / token inválido: **401**.
- Montos como string decimal (2 decimales); ISV 15%.

## Recursos

### Productos `/products` — escritura: Administrador
- `GET /products` — filtros: `search`, `category_id`, `low_stock`, `expiring_soon`, `per_page`, `page`
- `GET /products/{id}` · `POST /products` · `PUT /products/{id}` · `DELETE /products/{id}`

### Clientes `/customers` — escritura: Administrador o Cajero
- `GET /customers` (filtro `search`) · `GET/POST/PUT/DELETE /customers/{id}`

### Proveedores `/suppliers` — escritura: Administrador
- `GET /suppliers` (`search`, `active_only`) · `GET/POST/PUT/DELETE /suppliers/{id}`

### Categorías `/categories` — escritura: Administrador
- `GET /categories` (lista completa, no paginada) · `GET/POST/PUT/DELETE /categories/{id}`

### Facturación `/invoices` — Administrador o Cajero (anular: solo Administrador)
- `GET /invoices` — filtros: `search`, `payment_method_id`, `status` (`emitted|voided`), `date_filter` (`today|week|month`)
- `GET /invoices/{id}` — incluye `items[]`, `seller`, `payment_method`
- `POST /invoices` — body: `{ customer_name, customer_rtn?, customer_id?, payment_method_id, items: [{ product_id, quantity, discount_percent? }] }`. Valida stock, calcula ISV y descuenta inventario en una transacción.
- `POST /invoices/{id}/void` — body `{ reason }`. Revierte stock. **Solo Administrador**.
- `GET /invoices/{id}/pdf` — descarga el PDF (dompdf), `Content-Type: application/pdf`
- `GET /payment-methods` — métodos de pago activos

### Caja `/cash-registers` — Administrador o Cajero
- `GET /cash-registers` — historial paginado
- `GET /cash-registers/current` — caja abierta actual o `{ data: null }`
- `GET /cash-registers/{id}`
- `POST /cash-registers/open` — body `{ opening_amount }`. Falla si ya hay una abierta.
- `POST /cash-registers/{id}/close` — body `{ actual_amount, notes? }`. Calcula esperado vs. real y la diferencia (arqueo).

### Devoluciones `/returns` — ver: Admin/Cajero; crear: solo Administrador
- `GET /returns` (paginado) · `GET /returns/{id}`
- `POST /returns` — body `{ invoice_id, reason, items: [{ invoice_item_id, quantity, restock? }] }`. Valida máximo devolvible y reingresa stock si `restock`.

### Inventario / Kardex `/stock-movements` — Administrador o Cajero (solo lectura)
- `GET /stock-movements` — filtros: `product_id`, `type`, `date_from`, `date_to`

### Reportes `/reports` — solo Administrador
- `GET /reports/sales?from=&to=` — ventas por período (totales + por método de pago)
- `GET /reports/products?from=&to=&limit=&sort_by=quantity|revenue` — top productos
- `GET /reports/inventory` — snapshot de inventario

### Dashboard `/dashboard` — cualquier autenticado
- `GET /dashboard` — `{ metrics, alerts[], alerts_count }`

### Usuarios `/users` — solo Administrador
- `GET /users` (`search`, `role`) · `GET/POST/PUT/DELETE /users/{id}`
- `GET /roles` — nombres de roles disponibles
- Crear/editar: `{ name, email, password?, password_confirmation?, role, must_change_password? }`. No es posible auto-eliminarse.
