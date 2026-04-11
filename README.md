# 🏥 Hedman-Garcia Pharmacy Management System

Sistema de gestión integral para farmacias desarrollado en PHP con MySQL. Proporciona herramientas completas para la administración de inventario, facturación, usuarios y reportes.

[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11%20LTS-FF2D20?logo=laravel)](https://laravel.com/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?logo=mysql)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.0-7952B3?logo=bootstrap)](https://getbootstrap.com/)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

> **⚠️ Reescritura en progreso.** El repositorio contiene dos versiones
> conviviendo: la aplicación **legacy** (PHP procedural, root del repo) y el
> **rewrite** en Laravel 11 LTS + Livewire 3 dentro de `pharmacy-app/`. Al
> final del roadmap, `pharmacy-app/*` reemplazará el código legacy; mientras
> tanto, el legacy sigue operativo con parches de seguridad incrementales.
> Ver [Rewrite Laravel](#-rewrite-laravel-pharmacy-app) más abajo.

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

A partir de 2026, el proyecto inició una **reescritura completa** bajo
`pharmacy-app/` usando Laravel 11 LTS + Livewire 3, manteniendo el legacy
funcional en paralelo. El objetivo es eliminar la deuda técnica acumulada,
cerrar los findings de seguridad auditados, y entregar una interfaz
moderna sin perder los datos existentes.

### Stack del rewrite

| Capa | Tecnología |
|------|------------|
| Lenguaje | PHP 8.2+ |
| Framework | Laravel 11 LTS |
| Reactividad | Livewire 3 (+ Volt solo para páginas auth via Breeze) |
| CSS | Tailwind CSS + Alpine.js + Vite |
| Auth scaffold | Laravel Breeze (Livewire stack, dark mode) |
| Roles/ACL | spatie/laravel-permission 6 |
| PDF | barryvdh/laravel-dompdf 3 |
| Tests | Pest PHP |
| DB | MySQL 8 — base separada `pharmacy` (no toca `FarmaciaHG` del legacy) |

### Estructura

```
pharmacy-app/
├── app/
│   ├── Livewire/              ← componentes reactivos (Fase 2+)
│   ├── Models/                ← Eloquent
│   └── Providers/
├── config/
│   └── pharmacy.php           ← constantes de negocio (ISV, límites, TTLs)
├── database/migrations/       ← schema versionado
├── resources/views/
│   ├── layouts/app.blade.php  ← layout único compartido
│   ├── livewire/              ← componentes Livewire + Volt (auth)
│   └── components/            ← Blade components reutilizables
├── routes/
├── tests/                     ← Pest
└── .env.example               ← plantilla versionada
```

### Setup local del rewrite

```bash
cd pharmacy-app

# 1. Dependencias
composer install
npm install

# 2. Entorno
cp .env.example .env
php artisan key:generate
# editar .env con credenciales MySQL locales (DB_DATABASE=pharmacy)

# 3. Crear base de datos separada del legacy
mysql -u root -p -e "CREATE DATABASE pharmacy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# 4. Migraciones y assets
php artisan migrate
npm run build

# 5. Servidor local en puerto 8001 (el legacy usa 8000)
php artisan serve --port=8001
# abrir http://localhost:8001
```

### Roadmap por fases

- **Fase 0** — Backup, tag `v1.0-legacy`, scaffold Laravel. ✅
- **Fase 1** — Foundation: Breeze + Livewire + Tailwind + config/pharmacy.
  spatie Permission + dompdf + Pest + layout base. 🔄
- **Fase 2** — Módulo Usuarios y Dashboard con UI premium.
- **Fase 3** — Módulo Inventario con búsqueda live Livewire.
- **Fase 4** — Módulo Billing (carrito, factura transaccional, PDF).
- **Fase 5** — Comando Artisan para migrar `FarmaciaHG → pharmacy` + cutover.
- **Fase 6** — Polish, accesibilidad WCAG AA, documentación final.

### Tag de rollback

El código legacy en estado inmediatamente previo al rewrite está preservado
en el tag `v1.0-legacy`. Para inspeccionarlo:

```bash
git checkout v1.0-legacy
```

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

---

## 👥 Autores

- **Desarrollador Original** - Trabajo inicial
- **Claude AI** - Refactoring, seguridad, y completar módulo de facturación (2025)

---

## 📞 Soporte

Si encuentras algún problema o tienes preguntas:

1. Revisa la [documentación](#📋-tabla-de-contenidos)
2. Busca en [Issues](https://github.com/tu-usuario/Hedman-Garcia-Pharmacy/issues)
3. Crea un nuevo Issue si no encuentras solución

---

## 🙏 Agradecimientos

- [Bootstrap](https://getbootstrap.com/) - Framework CSS
- [jQuery](https://jquery.com/) - Librería JavaScript
- [SendGrid](https://sendgrid.com/) - Servicio de email
- [Composer](https://getcomposer.org/) - Dependency manager para PHP

---

**Made with ❤️ for Pharmacy Management**
