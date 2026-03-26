CREATE DATABASE IF NOT EXISTS hotelcore2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE hotelcore2;

SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    rol ENUM('super_admin', 'admin', 'editor', 'recepcionista') DEFAULT 'editor',
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_sesion DATETIME,
    ip_ultima_sesion VARCHAR(45),
    INDEX idx_email (email),
    INDEX idx_rol (rol)
);

CREATE TABLE sitio_ajustes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor LONGTEXT NOT NULL,
    tipo ENUM('texto', 'numero', 'json', 'booleano') DEFAULT 'texto',
    descripcion VARCHAR(500),
    INDEX idx_clave (clave)
);

CREATE TABLE tipos_habitacion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(500),
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_slug (slug)
);

CREATE TABLE habitaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_habitacion INT NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    tipo_habitacion_id INT NOT NULL,
    estado ENUM('disponible', 'ocupada', 'mantenimiento') DEFAULT 'disponible',
    capacidad_huespedes INT NOT NULL,
    num_camas INT NOT NULL,
    precio_base DECIMAL(10, 2) NOT NULL,
    activa BOOLEAN DEFAULT TRUE,
    -- Fase 2: Ratings y datos de integraciones
    rating DECIMAL(3,1) DEFAULT 0,
    numero_resenas INT DEFAULT 0,
    numero_reservas INT DEFAULT 0,
    sincronizar_booking BOOLEAN DEFAULT FALSE,
    sincronizar_airbnb BOOLEAN DEFAULT FALSE,
    id_externo_booking VARCHAR(255),
    id_externo_airbnb VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_habitacion_id) REFERENCES tipos_habitacion(id) ON DELETE RESTRICT,
    INDEX idx_slug (slug),
    INDEX idx_estado (estado),
    INDEX idx_tipo (tipo_habitacion_id)
);

CREATE TABLE habitaciones_idiomas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    descripcion LONGTEXT NOT NULL,
    slug VARCHAR(255) NOT NULL,
    seo_titulo VARCHAR(160),
    seo_descripcion VARCHAR(160),
    seo_palabras_clave VARCHAR(255),
    schema_json LONGTEXT,
    UNIQUE KEY unique_idioma (habitacion_id, idioma),
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_idioma (idioma)
);

CREATE TABLE habitacion_tarifas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    tipo_tarifa ENUM('baja', 'alta', 'corporativo') NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    fecha_inicio DATE,
    fecha_fin DATE,
    activa BOOLEAN DEFAULT TRUE,
    descripcion VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE,
    INDEX idx_habitacion_tipo (habitacion_id, tipo_tarifa),
    INDEX idx_fechas (fecha_inicio, fecha_fin)
);

CREATE TABLE habitacion_comodidades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    comodidades JSON NOT NULL,
    UNIQUE KEY unique_comodidad (habitacion_id, idioma),
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);

CREATE TABLE habitacion_amenities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    amenities JSON NOT NULL,
    UNIQUE KEY unique_amenity (habitacion_id, idioma),
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);

CREATE TABLE habitacion_configuracion_camas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    camas JSON NOT NULL,
    UNIQUE KEY unique_camas (habitacion_id),
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);

CREATE TABLE habitacion_imagenes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    imagen_id INT NOT NULL,
    es_principal BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0,
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (imagen_id) REFERENCES imagenes(id) ON DELETE CASCADE,
    INDEX idx_habitacion (habitacion_id),
    INDEX idx_principal (es_principal)
);

CREATE TABLE imagenes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    peso_original INT,
    peso_webp INT,
    ruta_original VARCHAR(500),
    ruta_webp VARCHAR(500),
    ruta_thumbnail VARCHAR(500),
    ruta_tiny VARCHAR(500),
    alt_text VARCHAR(255),
    etiquetas VARCHAR(500),
    tipo ENUM('habitacion', 'hotel', 'amenidades', 'vista', 'general') DEFAULT 'general',
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    subida_por INT,
    INDEX idx_slug (slug),
    INDEX idx_tipo (tipo),
    FOREIGN KEY (subida_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE paginas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(255) NOT NULL UNIQUE,
    activa BOOLEAN DEFAULT TRUE,
    mostrar_en_menu BOOLEAN DEFAULT TRUE,
    orden INT DEFAULT 0,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_activa (activa)
);

CREATE TABLE paginas_idiomas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pagina_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    contenido LONGTEXT NOT NULL,
    seo_titulo VARCHAR(160),
    seo_descripcion VARCHAR(160),
    seo_palabras_clave VARCHAR(255),
    schema_json LONGTEXT,
    UNIQUE KEY unique_idioma (pagina_id, idioma),
    FOREIGN KEY (pagina_id) REFERENCES paginas(id) ON DELETE CASCADE,
    INDEX idx_slug (slug)
);

CREATE TABLE paginas_versiones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pagina_id INT NOT NULL,
    campo_editado VARCHAR(100),
    valor_anterior LONGTEXT,
    valor_nuevo LONGTEXT,
    usuario_id INT,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pagina_id) REFERENCES paginas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_pagina (pagina_id),
    INDEX idx_fecha (fecha_cambio)
);

CREATE TABLE pagina_locks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pagina_id INT NOT NULL,
    usuario_id INT NOT NULL,
    timestamp_lock TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expira_en TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY unique_lock (pagina_id),
    FOREIGN KEY (pagina_id) REFERENCES paginas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_expira (expira_en)
);

CREATE TABLE paginas_legales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('privacidad', 'terminos', 'cookies', 'aviso_legal') NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE paginas_legales_idiomas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pagina_legal_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    contenido LONGTEXT NOT NULL,
    UNIQUE KEY unique_legal_idioma (pagina_legal_id, idioma),
    FOREIGN KEY (pagina_legal_id) REFERENCES paginas_legales(id) ON DELETE CASCADE
);

CREATE TABLE blog_entradas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slug VARCHAR(255) NOT NULL UNIQUE,
    autor_id INT NOT NULL,
    estado ENUM('borrador', 'publicada', 'archivada') DEFAULT 'borrador',
    fecha_publicacion DATETIME,
    destacada BOOLEAN DEFAULT FALSE,
    categoria_id INT,
    imagen_destacada_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (autor_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (categoria_id) REFERENCES blog_categorias(id) ON DELETE SET NULL,
    FOREIGN KEY (imagen_destacada_id) REFERENCES imagenes(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_publicacion)
);

CREATE TABLE blog_entradas_idiomas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entrada_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    contenido LONGTEXT NOT NULL,
    seo_titulo VARCHAR(160),
    seo_descripcion VARCHAR(160),
    seo_palabras_clave VARCHAR(500),
    schema_json LONGTEXT,
    UNIQUE KEY unique_idioma (entrada_id, idioma),
    FOREIGN KEY (entrada_id) REFERENCES blog_entradas(id) ON DELETE CASCADE,
    INDEX idx_slug (slug)
);

CREATE TABLE blog_categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(500),
    activa BOOLEAN DEFAULT TRUE,
    INDEX idx_slug (slug)
);

CREATE TABLE blog_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    INDEX idx_slug (slug)
);

CREATE TABLE blog_entrada_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entrada_id INT NOT NULL,
    tag_id INT NOT NULL,
    UNIQUE KEY unique_entrada_tag (entrada_id, tag_id),
    FOREIGN KEY (entrada_id) REFERENCES blog_entradas(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES blog_tags(id) ON DELETE CASCADE,
    INDEX idx_entrada (entrada_id)
);

CREATE TABLE resenas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('hotel', 'habitacion') NOT NULL,
    hotel_id INT,
    habitacion_id INT,
    nombre_cliente VARCHAR(255) NOT NULL,
    email_cliente VARCHAR(255),
    pais_cliente VARCHAR(100),
    puntuacion INT NOT NULL,
    titulo_resena VARCHAR(255),
    contenido_resena LONGTEXT,
    fecha_estancia DATE,
    mostrar_sitio BOOLEAN DEFAULT TRUE,
    moderada BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE,
    INDEX idx_tipo (tipo),
    INDEX idx_puntuacion (puntuacion),
    INDEX idx_mostrar (mostrar_sitio)
);

CREATE TABLE habitacion_faqs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    pregunta VARCHAR(255) NOT NULL,
    respuesta LONGTEXT NOT NULL,
    orden INT DEFAULT 0,
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE,
    INDEX idx_habitacion (habitacion_id)
);

CREATE TABLE hotel_faqs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idioma ENUM('es', 'en') NOT NULL,
    pregunta VARCHAR(255) NOT NULL,
    respuesta LONGTEXT NOT NULL,
    orden INT DEFAULT 0,
    INDEX idx_idioma (idioma)
);

CREATE TABLE formularios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    tipo ENUM('reserva', 'contacto', 'custom') DEFAULT 'custom',
    activo BOOLEAN DEFAULT TRUE,
    email_notificacion VARCHAR(255),
    redirigir_a VARCHAR(500),
    mensaje_exito VARCHAR(500),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_tipo (tipo)
);

CREATE TABLE formulario_campos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formulario_id INT NOT NULL,
    nombre_campo VARCHAR(100) NOT NULL,
    tipo_campo ENUM('texto', 'email', 'telefono', 'textarea', 'select', 'checkbox', 'fecha') NOT NULL,
    label VARCHAR(100) NOT NULL,
    label_en VARCHAR(100),
    placeholder VARCHAR(100),
    requerido BOOLEAN DEFAULT TRUE,
    validacion VARCHAR(500),
    opciones JSON,
    orden INT DEFAULT 0,
    FOREIGN KEY (formulario_id) REFERENCES formularios(id) ON DELETE CASCADE,
    INDEX idx_formulario (formulario_id)
);

CREATE TABLE formulario_respuestas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    formulario_id INT NOT NULL,
    datos JSON NOT NULL,
    leida BOOLEAN DEFAULT FALSE,
    respondida BOOLEAN DEFAULT FALSE,
    respuesta_admin LONGTEXT,
    respondida_por INT,
    fecha_respuesta DATETIME,
    ip_cliente VARCHAR(45),
    user_agent VARCHAR(500),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (formulario_id) REFERENCES formularios(id) ON DELETE CASCADE,
    FOREIGN KEY (respondida_por) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_formulario (formulario_id),
    INDEX idx_leida (leida),
    INDEX idx_fecha (fecha_creacion)
);

CREATE TABLE promociones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(255) NOT NULL,
    descripcion LONGTEXT,
    codigo_descuento VARCHAR(50),
    descuento_tipo ENUM('porcentaje', 'cantidad_fija') NOT NULL,
    descuento_valor DECIMAL(10, 2) NOT NULL,
    imagen_id INT,
    publicada BOOLEAN DEFAULT FALSE,
    fecha_inicio DATE,
    fecha_fin DATE,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (imagen_id) REFERENCES imagenes(id) ON DELETE SET NULL,
    INDEX idx_publicada (publicada),
    INDEX idx_fechas (fecha_inicio, fecha_fin)
);

CREATE TABLE promociones_idiomas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    promocion_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    titulo VARCHAR(255),
    descripcion LONGTEXT,
    UNIQUE KEY unique_idioma (promocion_id, idioma),
    FOREIGN KEY (promocion_id) REFERENCES promociones(id) ON DELETE CASCADE
);

CREATE TABLE promocion_habitaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    promocion_id INT NOT NULL,
    habitacion_id INT NOT NULL,
    UNIQUE KEY unique_promo_hab (promocion_id, habitacion_id),
    FOREIGN KEY (promocion_id) REFERENCES promociones(id) ON DELETE CASCADE,
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);

CREATE TABLE seo_geo (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('hotel', 'habitacion', 'articulo') NOT NULL,
    tipo_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    latitud DECIMAL(10, 8),
    longitud DECIMAL(11, 8),
    pais VARCHAR(100),
    region VARCHAR(100),
    ciudad VARCHAR(100),
    codigo_postal VARCHAR(20),
    direccion_completa VARCHAR(255),
    nombre_negocio VARCHAR(255),
    telefono VARCHAR(20),
    horario_apertura TIME,
    horario_cierre TIME,
    keywords_geo VARCHAR(500),
    google_my_business_url VARCHAR(500),
    UNIQUE KEY unique_geo (tipo, tipo_id, idioma),
    INDEX idx_ciudad (ciudad),
    INDEX idx_pais (pais)
);

CREATE TABLE ranking_keywords (
    id INT PRIMARY KEY AUTO_INCREMENT,
    keyword VARCHAR(255) NOT NULL,
    tipo ENUM('hotel', 'habitacion', 'articulo'),
    tipo_id INT,
    posicion_google INT,
    busquedas_mes INT,
    ctr_promedio DECIMAL(5, 2),
    fecha_registro DATE,
    INDEX idx_keyword (keyword),
    INDEX idx_fecha (fecha_registro)
);

CREATE TABLE blog_seo_basico (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entrada_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    titulo_auto VARCHAR(160),
    descripcion_auto VARCHAR(160),
    palabras_clave_auto VARCHAR(500),
    titulo_manual VARCHAR(160),
    descripcion_manual VARCHAR(160),
    palabras_clave_manual VARCHAR(500),
    titulo_activo VARCHAR(160),
    descripcion_activa VARCHAR(160),
    palabras_clave_activas VARCHAR(500),
    modo ENUM('auto', 'manual') DEFAULT 'auto',
    UNIQUE KEY unique_seo (entrada_id, idioma),
    FOREIGN KEY (entrada_id) REFERENCES blog_entradas(id) ON DELETE CASCADE
);

CREATE TABLE blog_seo_avanzado (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entrada_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    palabras_totales INT,
    palabras_unicas INT,
    densidad_palabra_clave DECIMAL(5, 2),
    legibilidad_score INT,
    tiene_h1 BOOLEAN,
    numero_h2 INT,
    numero_h3 INT,
    links_internos INT,
    links_externos INT,
    score_seo_total INT,
    sugerencias JSON,
    fecha_analisis DATETIME ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_seo_avanzado (entrada_id, idioma),
    FOREIGN KEY (entrada_id) REFERENCES blog_entradas(id) ON DELETE CASCADE
);

CREATE TABLE event_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    accion VARCHAR(100) NOT NULL,
    entidad VARCHAR(100),
    entidad_id INT,
    cambios JSON,
    ip VARCHAR(45),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    INDEX idx_accion (accion),
    INDEX idx_fecha (fecha),
    INDEX idx_usuario (usuario_id)
);

CREATE TABLE analytics (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo_pagina ENUM('habitacion', 'articulo', 'contacto', 'inicio'),
    pagina_id INT,
    idioma ENUM('es', 'en'),
    visitas INT DEFAULT 0,
    conversiones INT DEFAULT 0,
    fecha DATE,
    UNIQUE KEY unique_analytics (tipo_pagina, pagina_id, idioma, fecha),
    INDEX idx_fecha (fecha)
);

SET FOREIGN_KEY_CHECKS=1;
