-- Remodelacion exigida Módulo 6
DROP TABLE IF EXISTS habitacion_tarifas;

CREATE TABLE IF NOT EXISTS temporadas_globales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo_temporada ENUM('baja', 'regular', 'alta') NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    descripcion VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS tipo_habitacion_tarifas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo_habitacion_id INT NOT NULL,
    precio_baja DECIMAL(10,2) DEFAULT 0,
    precio_regular DECIMAL(10,2) DEFAULT 0,
    precio_alta DECIMAL(10,2) DEFAULT 0,
    precio_manual DECIMAL(10,2) DEFAULT 0,
    tarifa_manual_activa BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (tipo_habitacion_id) REFERENCES tipos_habitacion(id) ON DELETE CASCADE,
    UNIQUE KEY (tipo_habitacion_id)
);
