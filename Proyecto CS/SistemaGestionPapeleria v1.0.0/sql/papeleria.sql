CREATE DATABASE IF NOT EXISTS papeleria;
USE papeleria;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    rol VARCHAR(50) DEFAULT 'usuario',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    proveedor_id INT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO productos (nombre, descripcion, precio, stock, proveedor_id)
VALUES 
('Cuaderno A4', 'Cuaderno con 100 hojas, tamaño A4', 2.50, 100, 1),
('Bolígrafo', 'Bolígrafo con tinta azul', 1.20, 200, 1),
('Lápiz', 'Lápiz de grafito, paquete de 12', 0.80, 150, 2);

CREATE TABLE IF NOT EXISTS venta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unit DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) AS (cantidad * precio_unit) STORED
);
INSERT INTO venta (factura_id, producto_id, cantidad, precio_unit)
VALUES 
(1, 1, 2, 2.50),  
(1, 2, 5, 1.20),  
(2, 3, 3, 0.80), 
(3, 1, 10, 2.50); 

CREATE TABLE facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_factura VARCHAR(50) NOT NULL UNIQUE,
    fecha DATE NOT NULL,
    
    -- Datos del cliente (sin tabla separada)
    cliente_nombre VARCHAR(200) NOT NULL,
    cliente_identificacion VARCHAR(50) NOT NULL,
    cliente_direccion TEXT,
    cliente_telefono VARCHAR(20),
    cliente_email VARCHAR(200),
    
    -- Montos
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    impuesto DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    
    -- Estado y notas
    estado ENUM('pendiente', 'pagada', 'anulada') DEFAULT 'pendiente',
    notas TEXT,
    
    -- Auditoría
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_numero_factura (numero_factura),
    INDEX idx_fecha (fecha),
    INDEX idx_estado (estado),
    INDEX idx_cliente_identificacion (cliente_identificacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de detalles de factura (líneas de productos)
CREATE TABLE detalle_facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    producto_nombre VARCHAR(200) NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (factura_id) REFERENCES facturas(id) ON DELETE CASCADE,
    INDEX idx_factura_id (factura_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




CREATE TABLE IF NOT EXISTS proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    empresa VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100) NOT NULL UNIQUE,
    direccion TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO usuarios (nombre, correo, clave, rol)
VALUES (
    'Administrador',
    'admin@papeleria.com',
    '$2y$10$ZLw/6B6lXvO04WZC5N36qOeEiJJPgHf4aIu3zqxwHInDj06e3ilvq',  -- contraseña: admin123
    'admin'
);
UPDATE usuarios
SET clave = '$2y$10$zAcT/ZS.10j5OIcO3vl9s.UVAY3/b5hw6mkRlMfW1ucUjvYO8qz/K'
WHERE correo = 'admin@papeleria.com';

show variables;