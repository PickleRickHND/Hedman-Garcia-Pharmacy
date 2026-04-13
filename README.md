# 🏥 Hedman-Garcia Pharmacy Management System

Sistema de gestión integral para farmacias desarrollado en PHP con MySQL. Proporciona herramientas completas para la administración de inventario, facturación, usuarios y reportes.

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11%20LTS-FF2D20?logo=laravel)](https://laravel.com/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.0-7952B3?logo=bootstrap)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-Proprietary-red.svg)](LICENSE)

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Requisitos del Sistema](#-requisitos-del-sistema)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Módulos](#-módulos)
- [Base de Datos](#-base-de-datos)
- [Seguridad](#-seguridad)
- [API Endpoints](#-api-endpoints)
- [Rewrite Laravel](#-rewrite-laravel-pharmacy-app)
- [Contribuir](#-contribuir)
- [Licencia](#-licencia)

---

## ✨ Características

### 🔐 Autenticación y Usuarios
- Sistema de login seguro con hash de contraseñas (bcrypt)
- Recuperación de contraseña vía email (SendGrid)
- Gestión de usuarios con roles (Administrador, Cajero, Inventario)
- Perfiles de usuario personalizables

### 📦 Gestión de Inventario
- CRUD completo de productos farmacéuticos
- Control de stock en tiempo real
- Alertas de productos con bajo stock
- Alertas de productos próximos a vencer
- Búsqueda avanzada de productos (por nombre, ID, precio, presentación)
- Campos detallados: descripción, cantidad, precio, presentación, fecha de vencimiento, forma de administración, almacenamiento

### 💰 Módulo de Facturación
- Generación de facturas/recibos
- Carrito de compras unificado por usuario
- Cálculo automático de totales
- Múltiples métodos de pago (Efectivo, Tarjeta de Crédito/Débito, Transferencia)
- Actualización automática de inventario al facturar
- Visualización detallada de facturas
- Eliminación de facturas con restauración de inventario

### 📊 Reportes y Vistas
- Productos con bajo stock
- Productos próximos a vencer (30 días)
- Resumen de ventas diarias
- Historial completo de transacciones

### 🔒 Seguridad
- **Prepared Statements** en todas las consultas SQL (protección contra SQL Injection)
- Validación de inputs del lado del servidor
- Sanitización de datos
- Manejo de errores con try-catch
- Transacciones de base de datos para integridad de datos
- Session management seguro

---

## 💻 Requisitos del Sistema

### Software Requerido

- **PHP** >= 7.4
- **MySQL** >= 5.7 o **MariaDB** >= 10.3
- **Apache** o **Nginx**
- **Composer** (para dependencias PHP)

### Extensiones PHP Necesarias

```bash
php-mysqli
php-mbstring
php-json
php-curl
```

### Dependencias (via Composer)

- `sendgrid/sendgrid: ^8.0` - Envío de emails
- `ext-mysqli: *` - Conexión a MySQL

---

## 🚀 Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/tu-usuario/Hedman-Garcia-Pharmacy.git
cd Hedman-Garcia-Pharmacy
```

### 2. Instalar Dependencias

```bash
composer install
```

### 3. Crear Base de Datos

```bash
mysql -u root -p < database/schema.sql
```

**Nota:** Si tienes tablas antiguas `ShoppingCartUser_X`, ejecuta también el script de migración:

```bash
mysql -u root -p FarmaciaHG
```

Luego dentro de MySQL:

```sql
source database/migration_shopping_cart.sql
CALL MigrateShoppingCartTables();
CALL DropOldShoppingCartTables();
```

### 4. Configurar Credenciales

Crea el archivo `settings/config.ini` con el siguiente contenido:

```ini
[Database]
server = "localhost"
user_db = "tu_usuario_mysql"
password_db = "tu_contraseña_mysql"
db = "FarmaciaHG"

[SendGrid]
apikey = "tu_sendgrid_api_key"
```

**⚠️ IMPORTANTE:** Asegúrate de que `settings/config.ini` esté en `.gitignore` para no compartir credenciales.

### 5. Configurar Permisos

```bash
chmod 755 -R controllers/
chmod 755 -R screens/
chmod 600 settings/config.ini
```

### 6. Configurar Virtual Host (Opcional)

#### Apache

```apache
<VirtualHost *:80>
    ServerName farmacia.local
    DocumentRoot "/ruta/a/Hedman-Garcia-Pharmacy"

    <Directory "/ruta/a/Hedman-Garcia-Pharmacy">
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/farmacia-error.log
    CustomLog ${APACHE_LOG_DIR}/farmacia-access.log combined
</VirtualHost>
```

#### Nginx

```nginx
server {
    listen 80;
    server_name farmacia.local;
    root /ruta/a/Hedman-Garcia-Pharmacy;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 7. Acceder al Sistema

Abre tu navegador y ve a:

```
http://localhost/Hedman-Garcia-Pharmacy/index.php
```

O si configuraste un virtual host:

```
http://farmacia.local
```

---

## ⚙️ Configuración

### Usuario por Defecto (Opcional)

Si deseas crear un usuario administrador inicial, ejecuta en MySQL:

```sql
USE FarmaciaHG;

INSERT INTO Usuarios (id, nombre, apellido, correo, contrasena, roles)
VALUES (
    1,
    'Admin',
    'Sistema',
    'admin@farmacia.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- Password: Admin123!
    'Administrador'
);
```

**Credenciales de prueba:**
- Email: `admin@farmacia.com`
- Password: `Admin123!`

**⚠️ IMPORTANTE:** Cambia esta contraseña inmediatamente en producción.

### SendGrid Configuration

1. Crea una cuenta en [SendGrid](https://sendgrid.com/)
2. Genera una API Key
3. Agrega la API Key en `settings/config.ini`
4. Verifica el email del remitente en SendGrid

---

## 📁 Estructura del Proyecto

```
Hedman-Garcia-Pharmacy/
│
├── index.php                   # Página de login
├── composer.json               # Dependencias PHP
├── composer.lock               # Versiones bloqueadas
├── README.md                   # Documentación
├── .gitignore                  # Archivos ignorados por Git
│
├── controllers/                # Lógica de negocio y endpoints
│   ├── validations.php         # Validaciones principales (550 líneas)
│   ├── functions.js            # Funciones JavaScript (450+ líneas)
│   ├── add_product_bill.php    # Agregar al carrito (AJAX)
│   ├── remove_product_cart.php # Eliminar del carrito (AJAX)
│   ├── view_invoice_details.php # Ver detalles de factura (AJAX)
│   ├── delete_invoice.php      # Eliminar factura (AJAX)
│   ├── product_search.php      # Búsqueda de productos (AJAX)
│   ├── delete_product.php      # Eliminar producto
│   ├── delete_user.php         # Eliminar usuario
│   ├── force_reset_password.php # Reseteo forzado de contraseña
│   ├── user_logout.php         # Cerrar sesión
│   └── check_table_exists.php  # DEPRECATED (redirige a add_product_bill.php)
│
├── screens/                    # Vistas/Templates (1,900+ líneas)
│   ├── home.php                # Dashboard principal
│   ├── billing.php             # Módulo de facturación (502 líneas)
│   ├── inventory_control.php   # Gestión de inventario (293 líneas)
│   ├── user_management.php     # Administración de usuarios (195 líneas)
│   ├── account_settings.php    # Configuración de cuenta (116 líneas)
│   ├── change_password.php     # Cambiar contraseña
│   ├── reset_password.php      # Recuperar contraseña
│   ├── code_validation.php     # Validar código de recuperación
│   ├── new_password.php        # Ingresar nueva contraseña
│   ├── edit_product.php        # Editar producto
│   ├── edit_user.php           # Editar usuario
│   └── error_page.php          # Página de error
│
├── settings/                   # Configuración
│   ├── db_connection.php       # Conexión a MySQL (10 líneas)
│   └── config.ini              # Credenciales (NOT in Git)
│
├── database/                   # Scripts SQL
│   ├── schema.sql              # Esquema completo de BD
│   └── migration_shopping_cart.sql # Migración de tablas antiguas
│
├── css/                        # Estilos personalizados
│   └── styles.css
│
├── images/                     # Assets
│   ├── icon.png
│   ├── loginImage.png
│   └── homeImage.png
│
└── vendor/                     # Dependencias de Composer
    └── sendgrid/
```

---

## 🧩 Módulos

### 1. **Autenticación** (`index.php`, `controllers/validations.php`)

**Funciones:**
- Login con email y contraseña
- Logout
- Recuperación de contraseña via email
- Validación de código de recuperación
- Cambio de contraseña

**Endpoints:**
- `POST /index.php` - Login
- `POST /screens/reset_password.php` - Solicitar código
- `POST /screens/code_validation.php` - Validar código
- `POST /screens/new_password.php` - Establecer nueva contraseña
- `GET /controllers/user_logout.php` - Cerrar sesión

### 2. **Gestión de Usuarios** (`screens/user_management.php`)

**Funciones:**
- Crear nuevos usuarios
- Editar usuarios existentes
- Eliminar usuarios
- Resetear contraseña (admin)
- Asignar roles

**Endpoints:**
- `POST /screens/user_management.php` - Crear usuario
- `POST /screens/edit_user.php` - Editar usuario
- `GET /controllers/delete_user.php?id={user_id}` - Eliminar usuario
- `GET /controllers/force_reset_password.php?id={user_id}` - Reset password

### 3. **Inventario** (`screens/inventory_control.php`)

**Funciones:**
- Agregar productos
- Editar productos
- Eliminar productos
- Búsqueda en tiempo real (AJAX)
- Visualización paginada

**Endpoints:**
- `POST /screens/inventory_control.php` - Crear producto
- `POST /screens/edit_product.php` - Editar producto
- `GET /controllers/delete_product.php?id_producto={id}` - Eliminar producto
- `POST /controllers/product_search.php` - Buscar productos (AJAX)

### 4. **Facturación** (`screens/billing.php`)

**Funciones:**
- Gestión de carrito de compras
- Generación de facturas
- Visualización de facturas
- Eliminación de facturas
- Cálculo automático de totales

**Endpoints:**
- `POST /controllers/add_product_bill.php` - Agregar al carrito (AJAX)
- `POST /controllers/remove_product_cart.php` - Quitar del carrito (AJAX)
- `POST /screens/billing.php` - Generar factura
- `GET /controllers/view_invoice_details.php?factura_id={id}` - Ver detalles (AJAX)
- `POST /controllers/delete_invoice.php` - Eliminar factura (AJAX)

---

## 🗄️ Base de Datos

### Esquema de Tablas

#### **Usuarios**
```sql
id INT PRIMARY KEY
nombre VARCHAR(100)
apellido VARCHAR(100)
correo VARCHAR(255) UNIQUE
contrasena VARCHAR(255)
roles VARCHAR(50) FK -> Roles
codigo VARCHAR(6)
created_at TIMESTAMP
updated_at TIMESTAMP
last_login TIMESTAMP
active BOOLEAN
```

#### **Roles**
```sql
id INT PRIMARY KEY AUTO_INCREMENT
nombre_rol VARCHAR(50) UNIQUE
descripcion VARCHAR(255)
created_at TIMESTAMP
```

#### **Inventario**
```sql
id_producto INT PRIMARY KEY
nombre_producto VARCHAR(100)
descripcion TEXT
cantidad_producto INT
empaque_producto VARCHAR(50)
precio DECIMAL(10,2)
presentacion_producto VARCHAR(50)
fecha_vencimiento DATE
forma_administracion VARCHAR(50)
almacenamiento VARCHAR(100)
created_at TIMESTAMP
updated_at TIMESTAMP
active BOOLEAN
```

#### **Facturas**
```sql
id_factura INT PRIMARY KEY AUTO_INCREMENT
fecha_hora TIMESTAMP
cliente VARCHAR(255)
rtn VARCHAR(20)
cajero VARCHAR(255)
usuario_id INT FK -> Usuarios
estado VARCHAR(20)
metodo_pago VARCHAR(50) FK -> Metodos_Pago
total DECIMAL(10,2)
created_at TIMESTAMP
updated_at TIMESTAMP
```

#### **Factura_Detalles**
```sql
id INT PRIMARY KEY AUTO_INCREMENT
factura_id INT FK -> Facturas (CASCADE)
producto_id INT FK -> Inventario
nombre_producto VARCHAR(100)
cantidad INT
precio_unitario DECIMAL(10,2)
subtotal DECIMAL(10,2)
created_at TIMESTAMP
```

#### **Shopping_Cart** (Nueva tabla unificada)
```sql
id INT PRIMARY KEY AUTO_INCREMENT
usuario_id INT FK -> Usuarios (CASCADE)
producto_id INT FK -> Inventario (CASCADE)
nombre_producto VARCHAR(100)
cantidad INT
precio_unitario DECIMAL(10,2)
subtotal DECIMAL(10,2)
added_at TIMESTAMP
UNIQUE(usuario_id, producto_id)
```

#### **Metodos_Pago**
```sql
id INT PRIMARY KEY AUTO_INCREMENT
formas_pago VARCHAR(50) UNIQUE
activo BOOLEAN
created_at TIMESTAMP
```

### Triggers

- `before_insert_shopping_cart` - Calcula subtotal automáticamente
- `before_update_shopping_cart` - Recalcula subtotal al actualizar
- `before_insert_factura_detalles` - Calcula subtotal de línea
- `before_update_factura_detalles` - Recalcula subtotal de línea

### Stored Procedures

- `GetCartTotal(user_id, OUT cart_total)` - Obtiene total del carrito
- `ClearUserCart(user_id)` - Limpia carrito de usuario
- `CheckProductAvailability(product_id, quantity, OUT available, OUT stock)` - Verifica stock

### Vistas

- `productos_bajo_stock` - Productos con cantidad <= 10
- `productos_por_vencer` - Productos que vencen en 30 días
- `resumen_ventas_diarias` - Resumen de ventas por fecha

---

## 🔒 Seguridad

### Medidas Implementadas

#### 1. **SQL Injection Protection**
- ✅ **Prepared Statements** en todas las consultas
- ✅ Binding de parámetros con tipos específicos
- ✅ No hay concatenación directa de SQL

**Ejemplo:**
```php
// ❌ ANTES (vulnerable)
$query = "SELECT * FROM Usuarios WHERE correo='$correo'";

// ✅ AHORA (seguro)
$stmt = $connection->prepare("SELECT * FROM Usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
```

#### 2. **Password Security**
- Hash con `password_hash()` usando `PASSWORD_DEFAULT` (bcrypt)
- Verificación con `password_verify()`
- Nunca se almacenan contraseñas en texto plano

#### 3. **Input Validation**
- Validación de tipos (intval, floatval)
- Validación de formato (regex)
- Sanitización de strings
- Validación de longitud de campos

#### 4. **Session Management**
- Session IDs generados automáticamente
- Logout limpia sesión completamente
- Verificación de sesión en todas las páginas protegidas

#### 5. **Error Handling**
- Try-catch en operaciones críticas
- Transacciones de BD para integridad
- Rollback automático en caso de error
- Mensajes de error user-friendly (sin exponer detalles técnicos)

#### 6. **Database Transactions**
```php
try {
    $connection->begin_transaction();
    // Operaciones múltiples
    $connection->commit();
} catch (Exception $e) {
    $connection->rollback();
}
```

### Recomendaciones Adicionales

Para producción, considera implementar:

- [ ] CSRF Protection (tokens)
- [ ] Rate Limiting en login
- [ ] HTTPS obligatorio
- [ ] Logging de auditoría
- [ ] Two-Factor Authentication (2FA)
- [ ] Content Security Policy (CSP) headers
- [ ] Regular security audits

---

## 🌐 API Endpoints

### AJAX Endpoints (JSON Response)

#### **Shopping Cart**

```javascript
// Add product to cart
POST /controllers/add_product_bill.php
Content-Type: application/x-www-form-urlencoded

id_product=1001
&product_name=Paracetamol
&price_product=45.00
&quantityToAdd=2

Response:
{
  "success": true,
  "message": "Producto agregado al carrito exitosamente.",
  "cart_total": 90.00
}
```

```javascript
// Remove product from cart
POST /controllers/remove_product_cart.php

producto_id=1001

Response:
{
  "success": true,
  "message": "Producto eliminado del carrito exitosamente.",
  "cart_total": 0.00
}
```

#### **Invoices**

```javascript
// View invoice details
GET /controllers/view_invoice_details.php?factura_id=1

Response:
{
  "success": true,
  "message": "Datos obtenidos exitosamente.",
  "invoice": {
    "id_factura": 1,
    "fecha_hora": "2025-11-23 10:30:00",
    "cliente": "Juan Pérez",
    "rtn": "0801-1990-12345",
    "cajero": "María López",
    "estado": "Completada",
    "metodo_pago": "Efectivo",
    "total": "90.00"
  },
  "items": [
    {
      "producto_id": 1001,
      "nombre_producto": "Paracetamol 500mg",
      "cantidad": 2,
      "precio_unitario": "45.00",
      "subtotal": "90.00"
    }
  ]
}
```

```javascript
// Delete invoice
POST /controllers/delete_invoice.php

factura_id=1

Response:
{
  "success": true,
  "message": "Factura #1 eliminada exitosamente. Inventario restaurado."
}
```

#### **Product Search**

```javascript
// Search products
POST /controllers/product_search.php

searchText=paracetamol

Response:
[
  {
    "id_producto": 1001,
    "nombre_producto": "Paracetamol 500mg",
    "descripcion": "Analgésico y antipirético",
    "cantidad_producto": 100,
    "empaque_producto": "Caja x 20 tabletas",
    "precio": "45.00",
    "presentacion_producto": "Tabletas",
    "fecha_vencimiento": "2026-12-31",
    "forma_administracion": "Oral",
    "almacenamiento": "Temperatura ambiente"
  }
]
```

---

## 🤝 Contribuir

Las contribuciones son bienvenidas! Si deseas contribuir:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

### Guía de Contribución

- Usa prepared statements para TODAS las consultas SQL
- Documenta funciones con PHPDoc
- Sigue PSR-12 coding standards
- Escribe tests para nuevas funcionalidades
- Actualiza el README si es necesario

---

## 📝 Changelog

### [3.0.0] - 2026-04-13

#### Added (Fase 7 — 10 features de gestión farmacéutica)
- Categorías de productos con CRUD inline, badge color, filtro en inventario
- Proveedores con CRUD completo, toggle activo/inactivo, vinculación a productos
- Kardex de inventario: trazabilidad completa de movimientos de stock (venta, compra, devolución, anulación, ajuste, merma)
- Gestión de clientes: CRUD, historial de compras, autocompletado en POS
- Descuentos en facturación: % por línea con límite configurable, ISV sobre monto descontado
- Anulación de facturas: reversión de stock, modal con motivo, marca de agua en PDF
- Notificaciones/Alertas: campana con badge en navbar, alertas de stock bajo/vencidos/por vencer
- Devoluciones: parcial/total, reingreso a inventario, cálculo con precio efectivo (respeta descuentos)
- Corte de caja: apertura/cierre atómico, desglose por método de pago, arqueo de efectivo
- Reportes: ventas por periodo, productos más vendidos, snapshot de inventario
- 67 tests nuevos (159 total, 373 assertions, 0 failures)
- 13 nuevas migraciones, 7 nuevos modelos, 6 nuevos servicios, 20+ componentes Livewire

#### Security
- `lockForUpdate` en generación de números de factura y devolución (previene duplicados)
- Apertura de caja atómica con transacción + lock (previene doble apertura)
- Precisión financiera: `round()` en cada paso de acumulación monetaria
- Cálculo de refund en devoluciones con precio efectivo (respeta descuentos aplicados)

### [2.0.0] - 2025-11-23

#### Added
- ✅ Nueva tabla unificada `Shopping_Cart` reemplazando tablas dinámicas
- ✅ Tabla `Factura_Detalles` para líneas de factura
- ✅ Triggers automáticos para cálculo de subtotales
- ✅ Stored procedures para operaciones comunes
- ✅ Vistas para reportes (bajo stock, por vencer, ventas diarias)
- ✅ Endpoints AJAX para carrito y facturas
- ✅ Funciones JavaScript para manejo de carrito
- ✅ Validación client-side de formulario de facturación
- ✅ Modal dinámico para ver detalles de factura
- ✅ Script de migración de datos

#### Changed
- 🔒 Migración completa a Prepared Statements (SQL Injection protection)
- 🔒 Try-catch en todos los controllers
- 🔒 Transacciones de BD para integridad de datos
- 📝 Mensajes de error informativos
- 🐛 Fix bug JavaScript: mes off by 1 en `actualDate()`
- 📚 Documentación completa en README.md
- 📚 Comentarios en código PHP

#### Deprecated
- ⚠️ Tablas dinámicas `ShoppingCartUser_X`
- ⚠️ Archivo `check_table_exists.php` (usa `add_product_bill.php`)

#### Security
- 🔒 Todas las consultas SQL ahora usan prepared statements
- 🔒 Validación y sanitización de todos los inputs
- 🔒 Manejo seguro de errores sin exponer detalles técnicos

### [1.0.0] - 2023

#### Initial Release
- Login/Logout
- Gestión de usuarios
- Inventario básico
- Facturación (incompleta)

---

## 🚀 Rewrite Laravel (`pharmacy-app/`)

Reescritura completa del sistema bajo `pharmacy-app/` usando **Laravel 11
LTS + Livewire 3 + Tailwind CSS**. El rewrite cierra todas las
vulnerabilidades de seguridad identificadas en la auditoría del legacy,
introduce arquitectura moderna con tests automatizados, y presenta una
interfaz editorial premium diseñada con Fraunces + Inter.

**Estado:** Fases 0–7 completadas. Sistema de gestión farmacéutica completo con 10 módulos funcionales, 159 tests y 373 assertions.

### Stack

| Capa | Tecnología | Versión |
|------|------------|---------|
| Lenguaje | PHP | 8.2+ |
| Framework | Laravel (LTS) | 11.x |
| Reactividad | Livewire + Volt (auth) | 3.x |
| CSS | Tailwind CSS + Alpine.js | 4 / 3 |
| Build | Vite | 8.x |
| Auth | Laravel Breeze (Livewire stack, dark mode) | 2.x |
| Roles/ACL | spatie/laravel-permission | 6.x |
| PDF | barryvdh/laravel-dompdf | 3.x |
| Tests | Pest PHP | 4.x |
| DB | MySQL | 8 |
| Tipografía | Fraunces (display serif) + Inter (body) | — |

### Módulos implementados

#### Usuarios (Fase 2)
- CRUD completo con Livewire: tabla paginada, búsqueda live con debounce,
  filtro por rol, ordenamiento por columna
- Force reset de contraseña: genera password aleatorio con `Str::random(12)`,
  marca `must_change_password = true`, se muestra al admin una sola vez
- Middleware `ForceChangePassword` que intercepta en el próximo login y
  redirige a cambio obligatorio
- Protección: `role:Administrador` en middleware + doble check en cada acción
  (no auto-delete, no auto-reset)

#### Inventario (Fase 3)
- Product model con scopes `lowStock`, `outOfStock`, `expiringSoon`, `expired`,
  `search` y accessors `isLowStock`, `isExpired`, `isExpiringSoon`
- Soft deletes para preservar historial
- Badges semánticos: "Agotado" (rojo), "Bajo" (amber), "Por vencer" (amber),
  "Vencido" (rojo)
- `InventoryService` con transacciones y `lockForUpdate`: `adjustStock`,
  `increment`, `decrement` con validación de stock resultante
- Factory con estados `lowStock`, `outOfStock`, `expiringSoon`, `expired`

#### Facturación (Fase 4)
- POS (Punto de Venta) con Livewire: buscador de productos con stock > 0,
  carrito en memoria con increment/decrement/remove/clear, cálculo reactivo
  de subtotal + ISV (15%) + total
- `BillingService::issueInvoice` transaccional: `lockForUpdate` por producto,
  validación de stock item a item, creación de Invoice + InvoiceItems con
  snapshot de nombre/SKU/precio, decremento de stock vía `InventoryService`,
  numeración secuencial `FHG-NNNNNN`
- Histórico de facturas: tabla paginada con filtros por fecha (hoy/semana/mes),
  método de pago, búsqueda por número/cliente/RTN
- Detalle de factura + descarga PDF (dompdf, template editorial con branding)

#### Dashboard
- Tarjetas de métricas reales: usuarios (total/admins/cajeros), inventario
  (productos/stock bajo/próximos a vencer), facturación (facturas hoy/ingresos)
- Secciones numeradas 01–04 en Fraunces italic (estilo revista médica)
- Estados vacíos diferenciados: counts en 0 muestran "—" en gris con mensaje
  contextual; alertas con dot pulsante cuando hay items que requieren atención
- Accesos rápidos con flecha animada en hover y subtítulo descriptivo

#### UI/Design System (Fase 6)
- Estilo "Editorial Pharmacy": Fraunces para headings, monospace para metadata,
  Inter para body, retículas sutiles de fondo, ritmo editorial de revista
- Login con split layout: panel brand-900 con headline serif italic a la
  izquierda, formulario con inputs border-bottom + toggle password Alpine +
  botón blackout con spinner wire:loading
- Componente `<x-auth-shell>` reutilizable aplicado a las 6 pantallas auth
  (login/register/forgot/reset/verify/confirm) con props `step/eyebrow/title`
- Welcome page con hero Fraunces 5.5rem, ficha técnica del stack, feature
  strip con 3 pilares numerados
- Error pages custom (403/404/419/500/503) con componente `<x-errors.minimal>`
- 7 componentes UI base: `button`, `card`, `badge`, `alert`, `input`, `table`,
  `empty-state` con variantes y dark mode completo
- Favicon SVG custom con la cápsula de marca
- Skip link accesible + focus-visible + aria-labels

#### Categorías de Productos (Fase 7)
- CRUD inline en una sola página: crear, editar y eliminar sin navegación
- Badge con color personalizable (hex) en la tabla de inventario
- Filtro dropdown por categoría en el listado de productos
- Selector de categoría en formularios de crear/editar producto
- Seeder con 11 categorías farmacéuticas (Analgesicos, Antibioticos, etc.)
- `nullOnDelete`: eliminar categoría deja productos sin clasificar

#### Proveedores (Fase 7)
- CRUD completo: Index con búsqueda, Create, Edit
- Toggle activo/inactivo sin eliminar (soft state)
- Scope `search()` por nombre, contacto, teléfono, email
- Scope `active()` para filtrar proveedores vigentes
- Selector de proveedor en formularios de producto
- Conteo de productos por proveedor en la tabla

#### Kardex de Inventario (Fase 7)
- Tabla `stock_movements` con trazabilidad completa: producto, usuario,
  tipo, cantidad (+/-), stock antes/después, referencia, motivo
- Tipos de movimiento: `sale`, `purchase`, `return`, `void`, `adjustment`, `loss`
- Integración automática: cada operación de `InventoryService` registra movimiento
- `BillingService` pasa referencia de factura en cada decremento de stock
- Vista con filtros por producto, tipo de movimiento, rango de fechas
- Badges semánticos por tipo (rojo=venta/merma, verde=compra, azul=devolución)

#### Gestión de Clientes (Fase 7)
- CRUD completo: Index con búsqueda, Create, Edit, Show (detalle con historial)
- Soft deletes para preservar integridad con facturas históricas
- Autocompletado en POS: buscar cliente por nombre/RTN/teléfono al facturar
- Selección rápida que rellena nombre + RTN automáticamente
- Detalle de cliente: datos + tabla de facturas vinculadas + total gastado
- `customer_id` opcional en facturas (backwards compatible)

#### Descuentos en Facturación (Fase 7)
- Descuento por línea (porcentaje, 0–30% configurable en `config/pharmacy.php`)
- ISV calculado sobre monto ya descontado (correcto fiscalmente)
- Solo rol Administrador puede aplicar descuentos (Cajero ve precio fijo)
- Campos: `discount_percent` y `discount_amount` en invoice_items,
  `discount_total` en invoices
- UI: campo numérico % por línea en el carrito, línea "Descuento" en resumen
- PDF y detalle actualizados con columna de descuento
- Precisión financiera: `round()` en cada paso de acumulación

#### Anulación de Facturas (Fase 7)
- `BillingService::voidInvoice()` transaccional: cambia status, revierte stock,
  registra movimientos tipo `void` en el kardex
- Campos: `voided_at`, `voided_by`, `void_reason` en invoices
- Modal de confirmación con motivo obligatorio (min 5 caracteres)
- Solo Administrador puede anular
- Badge "Anulada" en listado de facturas + alerta en detalle
- Marca de agua "ANULADA" diagonal en PDF (CSS opacity)
- Scopes: `emitted()` excluye anuladas de reportes y dashboard

#### Notificaciones / Alertas (Fase 7)
- `NotificationService` calcula alertas en tiempo real (sin tabla extra)
- Alertas: stock bajo, productos vencidos, productos por vencer
- Componente Livewire `Bell` con `wire:poll.60s` para refresco automático
- Campana en navbar con badge numérico rojo cuando hay alertas
- Dropdown con alertas agrupadas por tipo, clickeables (navegan a inventario
  con filtro preseleccionado)
- Visible para Administrador y Cajero

#### Devoluciones (Fase 7)
- `ReturnService::processReturn()` transaccional: valida factura no anulada,
  valida cantidades máximas devolvibles, reingresa stock si `restock=true`
- Devolución parcial: seleccionar qué items y cuántas unidades devolver
- Checkbox "Reingresar al inventario" por item (para productos dañados)
- Cálculo de refund con precio efectivo (respeta descuentos aplicados)
- Numeración secuencial `DEV-NNNNNN` con `lockForUpdate`
- Movimientos tipo `return` en el kardex
- Botón "Devolución" en detalle de factura (solo Admin)
- Vistas: Index (historial), Create (desde factura), Show (detalle)

#### Corte de Caja (Fase 7)
- `CashRegisterService` con apertura/cierre atómico (transacción + lock)
- Validación: solo una caja abierta a la vez
- Cierre calcula automáticamente: facturas emitidas, anuladas, total ventas,
  desglose por método de pago (efectivo, tarjeta, transferencia)
- Arqueo: monto inicial + ventas efectivo = esperado vs real contado = diferencia
- Diferencia positiva (sobrante) o negativa (faltante) con indicador visual
- Historial de cortes con paginación
- Detalle completo: periodo, facturas, desglose, arqueo, notas

#### Reportes Básicos (Fase 7)
- Hub de reportes con 3 cards de acceso rápido (solo Administrador)
- **Ventas por periodo**: rango de fechas, total facturas, ingresos, descuentos,
  ISV, promedio diario, desglose por método de pago con barras CSS
- **Productos más vendidos**: top N por cantidad o ingresos, barras de ranking,
  selector de criterio y periodo
- **Inventario actual**: snapshot completo con valor total del inventario
  (precio × stock), alertas de stock bajo/agotado/vencido, tabla detallada
  con categoría y fecha de vencimiento
- `ReportService` con queries optimizadas (aggregates, GROUP BY)
- Facturas anuladas excluidas de todos los reportes

### Tests

```
159 tests, 373 assertions, 0 failures
```

| Suite | Tests | Cobertura |
|-------|-------|-----------|
| Auth (Breeze) | 10 | Login, register, logout, password reset/update, email verification |
| ForceChangePassword | 5 | Redirect, acceso normal, cambio exitoso, validación mismatch |
| Dashboard | 4 | Guards, métricas, accesos rápidos condicionales por rol |
| Users/Index | 9 | Guards, listar, search, filtro rol, force reset, delete, auto-delete bloqueado |
| Users/Create | 6 | Guards, create happy path, validación required/unique |
| Products/Index | 9 | Guards, listar, search, filtros low/expired, soft delete |
| Products/Create | 7 | Guards, create, validación sku/stock/fecha |
| Categories | 7 | Guards, CRUD inline, unique name, delete nullifica productos, product count |
| Suppliers | 8 | Guards, CRUD, toggle active, delete nullifica productos, product count |
| InventoryService | 5 | Increment, decrement, stock insuficiente, delta cero, cantidad negativa |
| StockMovements | 6 | Movimientos en adjustStock, issueInvoice, filtros vista, guards |
| Customers | 9 | Guards, CRUD, unique RTN, soft delete, historial, autocompletado POS |
| BillingService | 5 | Emisión atómica, rechazo vacío/sin cliente, rollback stock, numeración |
| Billing/NewInvoice | 11 | Guards, carrito, límites stock, happy path, validación |
| Billing/InvoiceList | 6 | Guards, listar, detalle, PDF download, rechazo por rol |
| Discounts | 4 | Descuento aplicado, cap max %, backwards compatible, customer_id linkeo |
| VoidInvoice | 6 | Void + restore stock, kardex void, doble anulación, motivo requerido, UI, guards |
| Notifications | 5 | Zero alerts, low stock, expired, expiring, bell component render |
| Returns | 6 | Devolución parcial + stock, kardex return, exceso rechazado, voided rechazado, guards |
| CashRegister | 8 | Apertura, doble apertura bloqueada, cierre, doble cierre bloqueado, guards, UI |
| Reports | 8 | salesByPeriod, topProducts, inventorySnapshot, hub guards, 3 report pages |
| Smoke | 2 | Welcome page, example |

### Estructura del rewrite

```
pharmacy-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/Billing/InvoicePdfController.php
│   │   └── Middleware/ForceChangePassword.php
│   ├── Livewire/
│   │   ├── Auth/ChangePasswordRequired.php
│   │   ├── Billing/{InvoiceList,NewInvoice,Show}.php
│   │   ├── CashRegister/{Index,Close,Show}.php
│   │   ├── Categories/Index.php
│   │   ├── Customers/{Index,Create,Edit,Show}.php
│   │   ├── Dashboard.php
│   │   ├── Inventory/StockMovements.php
│   │   ├── Notifications/Bell.php
│   │   ├── Products/{Index,Create,Edit}.php
│   │   ├── Reports/{Index,SalesReport,ProductsReport,InventoryReport}.php
│   │   ├── Returns/{Index,Create,Show}.php
│   │   ├── Suppliers/{Index,Create,Edit}.php
│   │   └── Users/{Index,Create,Edit}.php
│   ├── Models/
│   │   ├── {User,Product,Invoice,InvoiceItem,PaymentMethod}.php
│   │   ├── {Category,Supplier,Customer}.php
│   │   ├── {StockMovement,CashRegister}.php
│   │   └── {ReturnOrder,ReturnItem}.php
│   └── Services/
│       ├── {BillingService,InventoryService}.php
│       ├── {ReturnService,CashRegisterService}.php
│       ├── {ReportService,NotificationService}.php
├── config/pharmacy.php                ← constantes de negocio (ISV, descuentos, TTLs)
├── database/
│   ├── factories/{ProductFactory,InvoiceFactory}.php
│   ├── migrations/                    ← 21 migraciones
│   └── seeders/{Role,PaymentMethod,Category,Supplier,User,Product}Seeder.php
├── resources/views/
│   ├── components/
│   │   ├── auth-shell.blade.php
│   │   ├── errors/minimal.blade.php
│   │   └── ui/{button,card,badge,alert,input,table,empty-state}.blade.php
│   ├── errors/{403,404,419,500,503}.blade.php
│   ├── layouts/{app,guest}.blade.php
│   ├── livewire/
│   │   ├── billing/{invoice-list,new-invoice,show}.blade.php
│   │   ├── cash-register/{index,close,show}.blade.php
│   │   ├── categories/index.blade.php
│   │   ├── customers/{index,create,edit,show}.blade.php
│   │   ├── dashboard.blade.php
│   │   ├── inventory/stock-movements.blade.php
│   │   ├── notifications/bell.blade.php
│   │   ├── pages/auth/{login,register,forgot-password,...}.blade.php
│   │   ├── products/{index,create,edit}.blade.php
│   │   ├── reports/{index,sales,products,inventory}.blade.php
│   │   ├── returns/{index,create,show}.blade.php
│   │   ├── suppliers/{index,create,edit}.blade.php
│   │   └── users/{index,create,edit}.blade.php
│   ├── pdf/invoice.blade.php
│   └── welcome.blade.php
├── public/favicon.svg
├── routes/web.php
├── tests/Feature/                     ← 159 tests Pest
└── .env.example
```

### Setup local

```bash
cd pharmacy-app

# 1. Dependencias
composer install
npm install

# 2. Entorno
cp .env.example .env
php artisan key:generate
# editar .env: DB_DATABASE=pharmacy, DB_USERNAME, DB_PASSWORD
# Timezone ya configurado: America/Tegucigalpa

# 3. Crear base de datos
mysql -u root -p -e "CREATE DATABASE pharmacy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# 4. Migraciones, seeders y assets
php artisan migrate --seed
npm run build

# 5. Servidor local (puerto 8001 para no chocar con el legacy en 8000)
php artisan serve --port=8001
# abrir http://localhost:8001
```

### Roadmap completado

| Fase | Descripción | Estado |
|------|-------------|--------|
| **0** | Pre-work: backup, tag `v1.0-legacy`, reconstrucción DB legacy | ✅ |
| **1** | Foundation: Laravel 11, Breeze, Livewire, Tailwind, Spatie Permission, dompdf, Pest, config/pharmacy, design system UI components | ✅ |
| **2** | Módulo Usuarios + Dashboard + ForceChangePassword middleware | ✅ |
| **3** | Módulo Inventario con búsqueda live, alertas, soft delete, InventoryService | ✅ |
| **4** | Módulo Billing: POS, factura transaccional, PDF, histórico | ✅ |
| **5** | Migración datos legacy | ⏭️ Saltada (proyecto personal sin datos legacy) |
| **6** | Polish: UI editorial premium con Fraunces, auth redesign, welcome, error pages, favicon, accesibilidad | ✅ |
| **7** | 10 features de gestión farmacéutica: categorías, proveedores, kardex, clientes, descuentos, anulaciones, notificaciones, devoluciones, corte de caja, reportes | ✅ |

### Tag de rollback

El código legacy en estado previo al rewrite está preservado en el tag
`v1.0-legacy`:

```bash
git checkout v1.0-legacy
```

---

## 📄 Licencia

**Proprietary — All Rights Reserved.** Este código es de solo lectura para
evaluación de portafolio. Queda prohibido copiar, distribuir, modificar,
usar comercialmente o presentar como trabajo académico. Ver [`LICENSE`](LICENSE)
para los términos completos.

---

## 👥 Autor

- **Douglas Hedman** — Diseño, desarrollo y arquitectura

---

## 📞 Soporte

Si encuentras algún problema o tienes preguntas:

1. Revisa la [documentación](#-tabla-de-contenidos)
2. Busca en [Issues](https://github.com/PickleRickHND/Hedman-Garcia-Pharmacy/issues)
3. Crea un nuevo Issue si no encuentras solución

---

## 🙏 Agradecimientos

- [Laravel](https://laravel.com/) — Framework PHP
- [Livewire](https://livewire.laravel.com/) — Componentes reactivos
- [Tailwind CSS](https://tailwindcss.com/) — Utility-first CSS
- [Spatie Permission](https://spatie.be/docs/laravel-permission) — Roles y permisos
- [Fraunces](https://fonts.google.com/specimen/Fraunces) — Tipografía display serif
- [SendGrid](https://sendgrid.com/) — Servicio de email
- [Pest PHP](https://pestphp.com/) — Framework de testing
