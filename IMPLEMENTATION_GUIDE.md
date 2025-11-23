# 🚀 Guía de Implementación - Hedman-Garcia Pharmacy

Esta guía te ayudará a poner en marcha el sistema de farmacia actualizado con todas las nuevas funcionalidades.

---

## 📋 Índice

1. [Requisitos Previos](#requisitos-previos)
2. [Instalación de Base de Datos](#instalación-de-base-de-datos)
3. [Migración de Datos Antiguos (Opcional)](#migración-de-datos-antiguos)
4. [Configuración](#configuración)
5. [Verificación](#verificación)
6. [Uso del Sistema](#uso-del-sistema)
7. [Troubleshooting](#troubleshooting)

---

## ✅ Requisitos Previos

Antes de empezar, asegúrate de tener:

- [x] PHP 7.4 o superior
- [x] MySQL 5.7 o superior / MariaDB 10.3+
- [x] Apache o Nginx
- [x] Composer instalado
- [x] Acceso a la base de datos con permisos CREATE, INSERT, UPDATE, DELETE
- [x] Cuenta de SendGrid (para recuperación de contraseña)

---

## 🗄️ Instalación de Base de Datos

### Opción 1: Nueva Instalación (Recomendado para Testing)

Si estás instalando desde cero o en un ambiente de pruebas:

```bash
# 1. Entrar a MySQL
mysql -u root -p

# 2. Ejecutar el script completo
source /ruta/a/Hedman-Garcia-Pharmacy/database/schema.sql
```

Esto creará:
- ✅ Base de datos `FarmaciaHG`
- ✅ Todas las tablas necesarias
- ✅ Triggers automáticos
- ✅ Stored procedures
- ✅ Vistas para reportes
- ✅ Índices optimizados

### Opción 2: Actualización desde Versión Antigua

Si ya tienes datos en el sistema antiguo con tablas `ShoppingCartUser_X`:

```bash
# 1. IMPORTANTE: Hacer backup primero
mysqldump -u root -p FarmaciaHG > backup_farmacia_$(date +%Y%m%d).sql

# 2. Entrar a MySQL
mysql -u root -p FarmaciaHG

# 3. Crear las nuevas tablas (solo las que no existen)
# Ejecuta manualmente del schema.sql solo las líneas de:
# - Shopping_Cart
# - Factura_Detalles
# - Triggers
# - Stored Procedures
# - Vistas

# 4. Ejecutar migración
source /ruta/a/Hedman-Garcia-Pharmacy/database/migration_shopping_cart.sql

# 5. Migrar datos
CALL MigrateShoppingCartTables();

# 6. Verificar que se migró correctamente
SELECT usuario_id, COUNT(*) as items, SUM(subtotal) as total
FROM Shopping_Cart
GROUP BY usuario_id;

# 7. Si todo está bien, eliminar tablas antiguas
CALL DropOldShoppingCartTables();

# 8. Limpiar procedimientos de migración
DROP PROCEDURE IF EXISTS MigrateShoppingCartTables;
DROP PROCEDURE IF EXISTS DropOldShoppingCartTables;
```

---

## ⚙️ Configuración

### 1. Instalar Dependencias PHP

```bash
cd /ruta/a/Hedman-Garcia-Pharmacy
composer install
```

### 2. Configurar Base de Datos

Crea el archivo `settings/config.ini`:

```bash
cp settings/config.ini.example settings/config.ini
nano settings/config.ini
```

Contenido del archivo:

```ini
[Database]
server = "localhost"
user_db = "tu_usuario_mysql"
password_db = "tu_password_mysql"
db = "FarmaciaHG"

[SendGrid]
apikey = "SG.xxxxxxxxxxxxxxxxxxxxx"
```

⚠️ **IMPORTANTE**: Nunca compartas este archivo ni lo subas a Git.

### 3. Permisos de Archivos

```bash
# En Linux/Mac:
chmod 600 settings/config.ini
chmod 755 controllers/
chmod 755 screens/
```

### 4. Configurar SendGrid

1. Ve a [SendGrid.com](https://sendgrid.com)
2. Crea una cuenta gratuita (100 emails/día gratis)
3. Ve a Settings > API Keys
4. Crea una nueva API Key con permisos de "Mail Send"
5. Copia la key y pégala en `settings/config.ini`
6. Verifica tu email de remitente en SendGrid

---

## ✔️ Verificación

### Verificar Tablas Creadas

```sql
USE FarmaciaHG;

-- Ver todas las tablas
SHOW TABLES;

-- Debe mostrar:
-- - Usuarios
-- - Roles
-- - Inventario
-- - Facturas
-- - Factura_Detalles
-- - Shopping_Cart
-- - Metodos_Pago
-- - Audit_Log (opcional)

-- Verificar triggers
SHOW TRIGGERS;

-- Verificar stored procedures
SHOW PROCEDURE STATUS WHERE Db = 'FarmaciaHG';

-- Verificar vistas
SHOW FULL TABLES WHERE Table_type = 'VIEW';
```

### Crear Usuario Administrador de Prueba

```sql
USE FarmaciaHG;

-- Password: Admin123!
INSERT INTO Usuarios (id, nombre, apellido, correo, contrasena, roles)
VALUES (
    1,
    'Admin',
    'Sistema',
    'admin@farmacia.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Administrador'
);
```

**Credenciales:**
- Email: `admin@farmacia.com`
- Password: `Admin123!`

⚠️ **Cambia esta contraseña inmediatamente después del primer login!**

### Agregar Productos de Prueba

```sql
INSERT INTO Inventario (id_producto, nombre_producto, descripcion, cantidad_producto, empaque_producto, precio, presentacion_producto, fecha_vencimiento, forma_administracion, almacenamiento) VALUES
(1001, 'Paracetamol 500mg', 'Analgésico y antipirético', 100, 'Caja x 20 tabletas', 45.00, 'Tabletas', '2026-12-31', 'Oral', 'Temperatura ambiente'),
(1002, 'Ibuprofeno 400mg', 'Antiinflamatorio no esteroideo', 75, 'Frasco x 30 tabletas', 65.00, 'Tabletas', '2026-06-30', 'Oral', 'Temperatura ambiente'),
(1003, 'Amoxicilina 500mg', 'Antibiótico de amplio espectro', 50, 'Caja x 21 cápsulas', 120.00, 'Cápsulas', '2025-12-31', 'Oral', 'Lugar fresco y seco');
```

---

## 🎮 Uso del Sistema

### Módulo de Facturación (NUEVO)

#### Crear una Factura

1. **Acceder al Módulo**
   - Login con tus credenciales
   - Click en "Billing Module" desde el home

2. **Generar Nueva Factura**
   - Click en "Generate New Receipt"
   - Se abre el modal de facturación

3. **Llenar Información del Cliente**
   - **Nombre**: Nombre del cliente
   - **RTN**: Registro Tributario (opcional)
   - **Fecha/Hora**: Click en el icono de calendario para seleccionar fecha actual
   - **Cajero**: Se llena automáticamente con tu usuario
   - **Método de Pago**: Selecciona Efectivo, Tarjeta, etc.

4. **Agregar Productos al Carrito**
   - Busca productos usando la barra de búsqueda
   - O navega por la lista de productos
   - **Para cada producto:**
     - Ingresa la cantidad en el campo de cantidad
     - Click en el botón del carrito (🛒)
   - Los productos se agregan al "Current Shopping Cart"

5. **Revisar el Carrito**
   - Verifica los productos agregados en la tabla "Current Shopping Cart"
   - Puedes eliminar productos con el botón de basura (🗑️)
   - El total se calcula automáticamente

6. **Generar Factura**
   - Una vez que tengas todos los productos
   - Click en "Generate Receipt"
   - El sistema:
     - ✅ Valida que el carrito no esté vacío
     - ✅ Verifica stock disponible
     - ✅ Crea la factura
     - ✅ Actualiza el inventario automáticamente
     - ✅ Limpia el carrito
   - Recibirás confirmación con el número de factura

#### Ver Detalles de Factura

1. En la tabla de "Billing History"
2. Click en el botón del ojo (👁️) en la columna "Actions"
3. Se abre un modal con:
   - Información del cliente
   - Lista de productos con cantidades y precios
   - Total de la factura

#### Eliminar Factura

1. En la tabla de "Billing History"
2. Click en el botón de eliminar (❌)
3. Confirma la eliminación
4. El sistema:
   - ✅ Elimina la factura y sus detalles
   - ✅ **Restaura el inventario** automáticamente

⚠️ **Nota**: Al eliminar una factura, los productos vuelven al inventario.

### Gestión de Inventario

#### Agregar Producto

1. Ve a "Inventory Control"
2. Click en "Add New Product"
3. Llena todos los campos:
   - ID Producto (único)
   - Nombre
   - Descripción
   - Cantidad
   - Empaque (ej: "Caja x 20")
   - Precio
   - Presentación (ej: "Tabletas")
   - Fecha de Vencimiento
   - Forma de Administración (ej: "Oral")
   - Almacenamiento (ej: "Temperatura ambiente")
4. Click en "Save Product"

#### Editar/Eliminar Producto

- **Editar**: Click en el botón de lápiz (✏️)
- **Eliminar**: Click en el botón de basura (🗑️)

### Gestión de Usuarios

#### Crear Usuario

1. Ve a "User Management"
2. Click en "Add New User"
3. Llena:
   - Nombre
   - Apellido
   - Email (único)
   - Rol (Administrador, Cajero, Inventario)
   - Contraseña (mínimo 8 caracteres)
4. Click en "Save User"

---

## 🔍 Troubleshooting

### Problema 1: "Error connecting to database"

**Solución:**
```bash
# Verificar que MySQL está corriendo
sudo service mysql status

# Verificar credenciales en config.ini
cat settings/config.ini

# Probar conexión manual
mysql -u tu_usuario -p FarmaciaHG
```

### Problema 2: "Shopping_Cart table not found"

**Solución:**
```sql
-- Ejecutar schema.sql de nuevo, específicamente la parte de Shopping_Cart
USE FarmaciaHG;
source database/schema.sql
```

### Problema 3: "El carrito está vacío" al generar factura

**Causas posibles:**
1. No agregaste productos al carrito
2. Los productos se agregaron pero no están en la base de datos

**Solución:**
```sql
-- Verificar si hay items en el carrito
SELECT * FROM Shopping_Cart WHERE usuario_id = TU_ID_USUARIO;

-- Si está vacío, verifica que la tabla exista
SHOW TABLES LIKE 'Shopping_Cart';
```

### Problema 4: Bootstrap no se ve correctamente

**Solución:**
- Verifica que tienes conexión a internet (usa CDN)
- O descarga Bootstrap localmente
- Verifica que no hay errores de JavaScript en la consola (F12)

### Problema 5: Botones Ver/Eliminar no funcionan

**Solución:**
```bash
# Verifica que functions.js está cargado
# Abre la consola del navegador (F12) y escribe:
typeof viewInvoice
# Debe devolver "function", no "undefined"

# Si devuelve undefined, verifica que:
# 1. functions.js está en controllers/
# 2. Se importa al final de billing.php:
#    <script src="../controllers/functions.js"></script>
```

### Problema 6: SendGrid no envía emails

**Solución:**
1. Verifica la API Key en `settings/config.ini`
2. Verifica que verificaste tu email de remitente en SendGrid
3. Revisa los logs de SendGrid en su dashboard
4. Prueba con un email de SendGrid verificado primero

### Problema 7: "Stock insuficiente" pero hay stock

**Solución:**
```sql
-- Verificar stock real
SELECT id_producto, nombre_producto, cantidad_producto, active
FROM Inventario
WHERE id_producto = PRODUCTO_ID;

-- Si active = FALSE, activar:
UPDATE Inventario SET active = TRUE WHERE id_producto = PRODUCTO_ID;
```

---

## 📊 Verificar que Todo Funciona

### Checklist de Funcionalidades

- [ ] **Login funciona**
- [ ] **Crear usuario funciona**
- [ ] **Agregar producto al inventario funciona**
- [ ] **Buscar productos funciona**
- [ ] **Agregar producto al carrito funciona**
  - [ ] El producto aparece en "Current Shopping Cart"
  - [ ] El total se actualiza
- [ ] **Eliminar producto del carrito funciona**
- [ ] **Generar factura funciona**
  - [ ] La factura se crea
  - [ ] El inventario se actualiza (resta la cantidad vendida)
  - [ ] El carrito se limpia
  - [ ] Aparece en "Billing History"
- [ ] **Ver detalles de factura funciona**
  - [ ] El modal se abre
  - [ ] Muestra los productos correctos
  - [ ] El total es correcto
- [ ] **Eliminar factura funciona**
  - [ ] La factura se elimina
  - [ ] El inventario se restaura
- [ ] **Recuperación de contraseña funciona** (requiere SendGrid)

---

## 🔐 Seguridad en Producción

Antes de poner en producción:

### Obligatorio

- [ ] Cambiar contraseña del usuario admin
- [ ] Cambiar credenciales de base de datos
- [ ] Habilitar HTTPS (SSL/TLS)
- [ ] Configurar permisos de archivos correctamente
- [ ] Configurar firewall para MySQL
- [ ] Hacer backup regular de la base de datos

### Recomendado

- [ ] Implementar rate limiting en login
- [ ] Agregar CSRF protection
- [ ] Configurar logs de auditoría
- [ ] Monitoring de errores
- [ ] Backup automático diario

---

## 📞 Soporte

Si encuentras problemas:

1. Revisa esta guía primero
2. Revisa el README.md para más detalles técnicos
3. Revisa los logs de errores de PHP
4. Revisa la consola del navegador (F12)

---

## 🎉 ¡Listo!

Si completaste todos los pasos, tu sistema de farmacia debería estar funcionando completamente.

**Disfruta de tu sistema mejorado con:**
- ✅ Carrito de compras unificado
- ✅ Facturación completa
- ✅ Inventario automático
- ✅ Seguridad mejorada (prepared statements)
- ✅ Gestión completa de usuarios
- ✅ Reportes y vistas útiles

---

**Última actualización:** 2025-11-23
**Versión:** 2.0.0
