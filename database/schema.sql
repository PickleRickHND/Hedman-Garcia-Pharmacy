-- ============================================
-- Hedman-Garcia Pharmacy Database Schema
-- ============================================
-- Created: 2025-11-23
-- Description: Complete database schema for pharmacy management system
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS FarmaciaHG;
USE FarmaciaHG;

-- ============================================
-- Table: Roles
-- Description: User role definitions
-- ============================================
CREATE TABLE IF NOT EXISTS Roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default roles
INSERT INTO Roles (nombre_rol, descripcion) VALUES
    ('Administrador', 'Acceso total al sistema'),
    ('Cajero', 'Gestión de ventas y facturación'),
    ('Inventario', 'Gestión de productos y stock')
ON DUPLICATE KEY UPDATE nombre_rol=nombre_rol;

-- ============================================
-- Table: Usuarios
-- Description: System users with authentication
-- ============================================
CREATE TABLE IF NOT EXISTS Usuarios (
    id INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(255) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    roles VARCHAR(50) NOT NULL,
    codigo VARCHAR(6) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (roles) REFERENCES Roles(nombre_rol) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for better performance
CREATE INDEX idx_usuarios_correo ON Usuarios(correo);
CREATE INDEX idx_usuarios_roles ON Usuarios(roles);
CREATE INDEX idx_usuarios_codigo ON Usuarios(codigo);

-- ============================================
-- Table: Metodos_Pago
-- Description: Available payment methods
-- ============================================
CREATE TABLE IF NOT EXISTS Metodos_Pago (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formas_pago VARCHAR(50) NOT NULL UNIQUE,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default payment methods
INSERT INTO Metodos_Pago (formas_pago) VALUES
    ('Efectivo'),
    ('Tarjeta de Crédito'),
    ('Tarjeta de Débito'),
    ('Transferencia')
ON DUPLICATE KEY UPDATE formas_pago=formas_pago;

-- ============================================
-- Table: Inventario
-- Description: Product inventory management
-- ============================================
CREATE TABLE IF NOT EXISTS Inventario (
    id_producto INT PRIMARY KEY,
    nombre_producto VARCHAR(100) NOT NULL,
    descripcion TEXT,
    cantidad_producto INT NOT NULL DEFAULT 0,
    empaque_producto VARCHAR(50),
    precio DECIMAL(10,2) NOT NULL,
    presentacion_producto VARCHAR(50),
    fecha_vencimiento DATE,
    forma_administracion VARCHAR(50),
    almacenamiento VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    active BOOLEAN DEFAULT TRUE,
    CHECK (cantidad_producto >= 0),
    CHECK (precio >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for better search performance
CREATE INDEX idx_inventario_nombre ON Inventario(nombre_producto);
CREATE INDEX idx_inventario_precio ON Inventario(precio);
CREATE INDEX idx_inventario_fecha_venc ON Inventario(fecha_vencimiento);

-- ============================================
-- Table: Facturas
-- Description: Receipt/Invoice headers
-- ============================================
CREATE TABLE IF NOT EXISTS Facturas (
    id_factura INT PRIMARY KEY AUTO_INCREMENT,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cliente VARCHAR(255) NOT NULL,
    rtn VARCHAR(20),
    cajero VARCHAR(255) NOT NULL,
    usuario_id INT NOT NULL,
    estado VARCHAR(20) DEFAULT 'Completada',
    metodo_pago VARCHAR(50) NOT NULL,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (metodo_pago) REFERENCES Metodos_Pago(formas_pago) ON UPDATE CASCADE,
    CHECK (total >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for better performance
CREATE INDEX idx_facturas_fecha ON Facturas(fecha_hora);
CREATE INDEX idx_facturas_cliente ON Facturas(cliente);
CREATE INDEX idx_facturas_usuario ON Facturas(usuario_id);
CREATE INDEX idx_facturas_estado ON Facturas(estado);

-- ============================================
-- Table: Factura_Detalles
-- Description: Receipt/Invoice line items
-- ============================================
CREATE TABLE IF NOT EXISTS Factura_Detalles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    factura_id INT NOT NULL,
    producto_id INT NOT NULL,
    nombre_producto VARCHAR(100) NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (factura_id) REFERENCES Facturas(id_factura) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES Inventario(id_producto) ON DELETE RESTRICT,
    CHECK (cantidad > 0),
    CHECK (precio_unitario >= 0),
    CHECK (subtotal >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for better performance
CREATE INDEX idx_factura_detalles_factura ON Factura_Detalles(factura_id);
CREATE INDEX idx_factura_detalles_producto ON Factura_Detalles(producto_id);

-- ============================================
-- Table: Shopping_Cart
-- Description: Unified shopping cart for all users
-- REPLACES: Dynamic ShoppingCartUser_X tables
-- ============================================
CREATE TABLE IF NOT EXISTS Shopping_Cart (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    producto_id INT NOT NULL,
    nombre_producto VARCHAR(100) NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES Inventario(id_producto) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (usuario_id, producto_id),
    CHECK (cantidad > 0),
    CHECK (precio_unitario >= 0),
    CHECK (subtotal >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for better performance
CREATE INDEX idx_cart_usuario ON Shopping_Cart(usuario_id);
CREATE INDEX idx_cart_producto ON Shopping_Cart(producto_id);

-- ============================================
-- Table: Audit_Log (Optional - for security)
-- Description: Track important operations
-- ============================================
CREATE TABLE IF NOT EXISTS Audit_Log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50),
    registro_id INT,
    detalles TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create indexes for audit log
CREATE INDEX idx_audit_usuario ON Audit_Log(usuario_id);
CREATE INDEX idx_audit_fecha ON Audit_Log(created_at);
CREATE INDEX idx_audit_accion ON Audit_Log(accion);

-- ============================================
-- Triggers for automatic calculations
-- ============================================

-- Trigger: Update subtotal in Shopping_Cart before insert
DELIMITER //
CREATE TRIGGER before_insert_shopping_cart
BEFORE INSERT ON Shopping_Cart
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END//
DELIMITER ;

-- Trigger: Update subtotal in Shopping_Cart before update
DELIMITER //
CREATE TRIGGER before_update_shopping_cart
BEFORE UPDATE ON Shopping_Cart
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END//
DELIMITER ;

-- Trigger: Update subtotal in Factura_Detalles before insert
DELIMITER //
CREATE TRIGGER before_insert_factura_detalles
BEFORE INSERT ON Factura_Detalles
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END//
DELIMITER ;

-- Trigger: Update subtotal in Factura_Detalles before update
DELIMITER //
CREATE TRIGGER before_update_factura_detalles
BEFORE UPDATE ON Factura_Detalles
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END//
DELIMITER ;

-- ============================================
-- Stored Procedures
-- ============================================

-- Procedure: Get cart total for a user
DELIMITER //
CREATE PROCEDURE GetCartTotal(IN user_id INT, OUT cart_total DECIMAL(10,2))
BEGIN
    SELECT COALESCE(SUM(subtotal), 0.00) INTO cart_total
    FROM Shopping_Cart
    WHERE usuario_id = user_id;
END//
DELIMITER ;

-- Procedure: Clear user shopping cart
DELIMITER //
CREATE PROCEDURE ClearUserCart(IN user_id INT)
BEGIN
    DELETE FROM Shopping_Cart WHERE usuario_id = user_id;
END//
DELIMITER ;

-- Procedure: Check product availability
DELIMITER //
CREATE PROCEDURE CheckProductAvailability(
    IN p_product_id INT,
    IN p_quantity INT,
    OUT p_available BOOLEAN,
    OUT p_stock INT
)
BEGIN
    SELECT cantidad_producto INTO p_stock
    FROM Inventario
    WHERE id_producto = p_product_id AND active = TRUE;

    IF p_stock IS NULL THEN
        SET p_available = FALSE;
        SET p_stock = 0;
    ELSEIF p_stock >= p_quantity THEN
        SET p_available = TRUE;
    ELSE
        SET p_available = FALSE;
    END IF;
END//
DELIMITER ;

-- ============================================
-- Views for common queries
-- ============================================

-- View: Products with low stock
CREATE OR REPLACE VIEW productos_bajo_stock AS
SELECT
    id_producto,
    nombre_producto,
    cantidad_producto,
    precio,
    fecha_vencimiento
FROM Inventario
WHERE cantidad_producto <= 10 AND active = TRUE
ORDER BY cantidad_producto ASC;

-- View: Products about to expire (within 30 days)
CREATE OR REPLACE VIEW productos_por_vencer AS
SELECT
    id_producto,
    nombre_producto,
    cantidad_producto,
    precio,
    fecha_vencimiento,
    DATEDIFF(fecha_vencimiento, CURDATE()) as dias_restantes
FROM Inventario
WHERE fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    AND fecha_vencimiento >= CURDATE()
    AND active = TRUE
ORDER BY fecha_vencimiento ASC;

-- View: Sales summary by date
CREATE OR REPLACE VIEW resumen_ventas_diarias AS
SELECT
    DATE(fecha_hora) as fecha,
    COUNT(*) as num_facturas,
    SUM(total) as total_ventas,
    AVG(total) as promedio_venta
FROM Facturas
WHERE estado = 'Completada'
GROUP BY DATE(fecha_hora)
ORDER BY fecha DESC;

-- ============================================
-- Sample Data (Optional - for testing)
-- ============================================
-- Note: Uncomment to insert sample data

/*
-- Sample admin user (password: Admin123!)
INSERT INTO Usuarios (id, nombre, apellido, correo, contrasena, roles)
VALUES (
    1,
    'Admin',
    'Sistema',
    'admin@farmacia.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Administrador'
);

-- Sample products
INSERT INTO Inventario (id_producto, nombre_producto, descripcion, cantidad_producto, empaque_producto, precio, presentacion_producto, fecha_vencimiento, forma_administracion, almacenamiento) VALUES
(1001, 'Paracetamol 500mg', 'Analgésico y antipirético', 100, 'Caja x 20 tabletas', 45.00, 'Tabletas', '2026-12-31', 'Oral', 'Temperatura ambiente'),
(1002, 'Ibuprofeno 400mg', 'Antiinflamatorio no esteroideo', 75, 'Frasco x 30 tabletas', 65.00, 'Tabletas', '2026-06-30', 'Oral', 'Temperatura ambiente'),
(1003, 'Amoxicilina 500mg', 'Antibiótico de amplio espectro', 50, 'Caja x 21 cápsulas', 120.00, 'Cápsulas', '2025-12-31', 'Oral', 'Lugar fresco y seco');
*/

-- ============================================
-- Migration Note
-- ============================================
-- IMPORTANT: This schema replaces dynamic ShoppingCartUser_X tables
-- with a unified Shopping_Cart table. Run the migration script to
-- transfer data from old tables if they exist.
--
-- Migration steps:
-- 1. Backup existing database
-- 2. Create new Shopping_Cart table
-- 3. Migrate data from ShoppingCartUser_X tables
-- 4. Drop old ShoppingCartUser_X tables
-- 5. Update application code to use new table
-- ============================================
