-- ============================================
-- Hedman-Garcia Pharmacy - Database Initialization Script
-- ============================================
-- Run this script to create a fresh database with sample data
-- Usage: mysql -u root -p < init_database.sql
-- ============================================

-- Drop database if exists (CAREFUL: This will delete all data!)
DROP DATABASE IF EXISTS FarmaciaHG;

-- Create database
CREATE DATABASE FarmaciaHG CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE FarmaciaHG;

-- ============================================
-- Table: Roles
-- ============================================
CREATE TABLE Roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default roles
INSERT INTO Roles (nombre_rol, descripcion) VALUES
    ('Administrador', 'Acceso total al sistema'),
    ('Cajero', 'Gestion de ventas y facturacion'),
    ('Inventario', 'Gestion de productos y stock');

-- ============================================
-- Table: Usuarios
-- ============================================
CREATE TABLE Usuarios (
    id INT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(255) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    roles VARCHAR(50) NOT NULL,
    codigo VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (roles) REFERENCES Roles(nombre_rol) ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_usuarios_correo ON Usuarios(correo);
CREATE INDEX idx_usuarios_roles ON Usuarios(roles);
CREATE INDEX idx_usuarios_codigo ON Usuarios(codigo);

-- ============================================
-- Table: Metodos_Pago
-- ============================================
CREATE TABLE Metodos_Pago (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formas_pago VARCHAR(50) NOT NULL UNIQUE,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO Metodos_Pago (formas_pago) VALUES
    ('Efectivo'),
    ('Tarjeta de Credito'),
    ('Tarjeta de Debito'),
    ('Transferencia');

-- ============================================
-- Table: Inventario
-- ============================================
CREATE TABLE Inventario (
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
    active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE INDEX idx_inventario_nombre ON Inventario(nombre_producto);
CREATE INDEX idx_inventario_precio ON Inventario(precio);
CREATE INDEX idx_inventario_fecha_venc ON Inventario(fecha_vencimiento);

-- ============================================
-- Table: Facturas
-- ============================================
CREATE TABLE Facturas (
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
    FOREIGN KEY (metodo_pago) REFERENCES Metodos_Pago(formas_pago) ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_facturas_fecha ON Facturas(fecha_hora);
CREATE INDEX idx_facturas_cliente ON Facturas(cliente);
CREATE INDEX idx_facturas_usuario ON Facturas(usuario_id);
CREATE INDEX idx_facturas_estado ON Facturas(estado);

-- ============================================
-- Table: Factura_Detalles
-- ============================================
CREATE TABLE Factura_Detalles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    factura_id INT NOT NULL,
    producto_id INT NOT NULL,
    nombre_producto VARCHAR(100) NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (factura_id) REFERENCES Facturas(id_factura) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES Inventario(id_producto) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE INDEX idx_factura_detalles_factura ON Factura_Detalles(factura_id);
CREATE INDEX idx_factura_detalles_producto ON Factura_Detalles(producto_id);

-- ============================================
-- Table: Shopping_Cart
-- ============================================
CREATE TABLE Shopping_Cart (
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
    UNIQUE KEY unique_user_product (usuario_id, producto_id)
) ENGINE=InnoDB;

CREATE INDEX idx_cart_usuario ON Shopping_Cart(usuario_id);
CREATE INDEX idx_cart_producto ON Shopping_Cart(producto_id);

-- ============================================
-- Table: Audit_Log (Optional)
-- ============================================
CREATE TABLE Audit_Log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    tabla_afectada VARCHAR(50),
    registro_id INT,
    detalles TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES Usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE INDEX idx_audit_usuario ON Audit_Log(usuario_id);
CREATE INDEX idx_audit_fecha ON Audit_Log(created_at);

-- ============================================
-- Triggers
-- ============================================
DELIMITER //

CREATE TRIGGER before_insert_shopping_cart
BEFORE INSERT ON Shopping_Cart
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END//

CREATE TRIGGER before_update_shopping_cart
BEFORE UPDATE ON Shopping_Cart
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END//

CREATE TRIGGER before_insert_factura_detalles
BEFORE INSERT ON Factura_Detalles
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END//

CREATE TRIGGER before_update_factura_detalles
BEFORE UPDATE ON Factura_Detalles
FOR EACH ROW
BEGIN
    SET NEW.subtotal = NEW.cantidad * NEW.precio_unitario;
END//

DELIMITER ;

-- ============================================
-- Views
-- ============================================
CREATE OR REPLACE VIEW productos_bajo_stock AS
SELECT id_producto, nombre_producto, cantidad_producto, precio, fecha_vencimiento
FROM Inventario
WHERE cantidad_producto <= 10 AND active = TRUE
ORDER BY cantidad_producto ASC;

CREATE OR REPLACE VIEW productos_por_vencer AS
SELECT id_producto, nombre_producto, cantidad_producto, precio, fecha_vencimiento,
       DATEDIFF(fecha_vencimiento, CURDATE()) as dias_restantes
FROM Inventario
WHERE fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
  AND fecha_vencimiento >= CURDATE()
  AND active = TRUE
ORDER BY fecha_vencimiento ASC;

CREATE OR REPLACE VIEW resumen_ventas_diarias AS
SELECT DATE(fecha_hora) as fecha, COUNT(*) as num_facturas,
       SUM(total) as total_ventas, AVG(total) as promedio_venta
FROM Facturas
WHERE estado = 'Completada'
GROUP BY DATE(fecha_hora)
ORDER BY fecha DESC;

-- ============================================
-- SAMPLE DATA - Admin User
-- ============================================
-- Password: Admin123! (hashed with bcrypt)
INSERT INTO Usuarios (id, nombre, apellido, correo, contrasena, roles) VALUES
(100001, 'Admin', 'Sistema', 'admin@farmacia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador');

-- Sample Cashier (Password: Cajero123!)
INSERT INTO Usuarios (id, nombre, apellido, correo, contrasena, roles) VALUES
(100002, 'Juan', 'Perez', 'cajero@farmacia.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cajero');

-- ============================================
-- SAMPLE DATA - Products
-- ============================================
INSERT INTO Inventario (id_producto, nombre_producto, descripcion, cantidad_producto, empaque_producto, precio, presentacion_producto, fecha_vencimiento, forma_administracion, almacenamiento) VALUES
(1001, 'Paracetamol 500mg', 'Analgesico y antipiretico para el alivio del dolor leve a moderado y reduccion de la fiebre', 100, 'Caja x 20 tabletas', 45.00, 'Tabletas', '2026-12-31', 'Oral', 'Temperatura ambiente'),
(1002, 'Ibuprofeno 400mg', 'Antiinflamatorio no esteroideo para dolor, inflamacion y fiebre', 75, 'Frasco x 30 tabletas', 65.00, 'Tabletas', '2026-06-30', 'Oral', 'Temperatura ambiente'),
(1003, 'Amoxicilina 500mg', 'Antibiotico de amplio espectro para infecciones bacterianas', 50, 'Caja x 21 capsulas', 120.00, 'Capsulas', '2025-12-31', 'Oral', 'Lugar fresco y seco'),
(1004, 'Omeprazol 20mg', 'Inhibidor de la bomba de protones para acidez y ulceras gastricas', 80, 'Caja x 14 capsulas', 55.00, 'Capsulas', '2026-08-15', 'Oral', 'Temperatura ambiente'),
(1005, 'Loratadina 10mg', 'Antihistaminico para alergias y rinitis', 60, 'Caja x 10 tabletas', 35.00, 'Tabletas', '2026-03-20', 'Oral', 'Temperatura ambiente'),
(1006, 'Metformina 850mg', 'Antidiabetico oral para el control de la glucosa', 90, 'Caja x 30 tabletas', 85.00, 'Tabletas', '2026-09-10', 'Oral', 'Temperatura ambiente'),
(1007, 'Losartan 50mg', 'Antihipertensivo para el control de la presion arterial', 70, 'Caja x 30 tabletas', 95.00, 'Tabletas', '2026-07-25', 'Oral', 'Temperatura ambiente'),
(1008, 'Diclofenaco 50mg', 'Antiinflamatorio para dolor muscular y articular', 85, 'Caja x 20 tabletas', 40.00, 'Tabletas', '2026-05-18', 'Oral', 'Temperatura ambiente'),
(1009, 'Cetirizina 10mg', 'Antihistaminico de segunda generacion para alergias', 55, 'Caja x 10 tabletas', 30.00, 'Tabletas', '2026-11-30', 'Oral', 'Temperatura ambiente'),
(1010, 'Atorvastatina 20mg', 'Estatina para reducir el colesterol', 40, 'Caja x 30 tabletas', 150.00, 'Tabletas', '2026-04-12', 'Oral', 'Temperatura ambiente');

-- ============================================
-- SUCCESS MESSAGE
-- ============================================
SELECT '===========================================' AS '';
SELECT 'Base de datos FarmaciaHG creada exitosamente!' AS 'MENSAJE';
SELECT '===========================================' AS '';
SELECT 'Usuario Admin:' AS '';
SELECT '  Email: admin@farmacia.com' AS '';
SELECT '  Password: Admin123!' AS '';
SELECT '' AS '';
SELECT 'Usuario Cajero:' AS '';
SELECT '  Email: cajero@farmacia.com' AS '';
SELECT '  Password: Admin123!' AS '';
SELECT '===========================================' AS '';
