# SCHEMA COMPLETO BASE DE DATOS - FASE 1

## TABLA 1: USUARIOS

```sql
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
```

**Campos:**
- `id`: Identificador único
- `email`: Email único para login
- `password_hash`: Contraseña hasheada con bcrypt
- `nombre_completo`: Nombre del usuario (ej: "Juan Pérez")
- `rol`: Super Admin | Admin | Editor | Recepcionista
- `activo`: Activo = puede entrar, Inactivo = bloqueado
- `fecha_creacion`: Cuándo se creó la cuenta
- `ultima_sesion`: Última vez que entró
- `ip_ultima_sesion`: IP desde donde entró

---

## TABLA 2: SITIO AJUSTES

```sql
CREATE TABLE sitio_ajustes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    clave VARCHAR(100) UNIQUE NOT NULL,
    valor LONGTEXT NOT NULL,
    tipo ENUM('texto', 'numero', 'json', 'booleano') DEFAULT 'texto',
    descripcion VARCHAR(500),
    INDEX idx_clave (clave)
);
```

**Ejemplos de claves (datos que se guardan aquí):**

```
BRANDING:
├─ nombre_hotel: "Hotel Paradise"
├─ descripcion_hotel: "Hotel 4 estrellas en el centro"
├─ logo_id: 5 (ID de imagen en galería)
├─ favicon_id: 6
├─ color_primario: "#425363"
├─ color_secundario: "#A90101"

CONTACTO:
├─ direccion: "Calle Principal 123, Madrid"
├─ telefono: "+34 912 345 678"
├─ email_contacto: "info@hotel.com"
├─ whatsapp: "+34 612 345 678"
├─ horario_atencion: "09:00 - 22:00"

SEO GLOBAL:
├─ seo_titulo_global: "Hotel Paradise | Alojamiento 4 estrellas"
├─ seo_descripcion_global: "Descubre nuestro hotel..."
├─ seo_keywords_global: "hotel madrid, alojamiento"
├─ google_analytics_id: "G-XXXXXXXXXX"
├─ google_search_console_token: "xxxxx"

REDES SOCIALES:
├─ facebook_url: "https://facebook.com/hotelparadise"
├─ instagram_url: "https://instagram.com/hotelparadise"
├─ tripadvisor_url: "https://tripadvisor.com/..."
├─ google_my_business_url: "https://goo.gl/..."

LEGAL:
├─ razon_social: "Hotel Paradise SL"
├─ rfc_cif: "B12345678"

INTEGRACIONES:
├─ mostrar_whatsapp: true (booleano)
├─ whatsapp_numero: "+34 612 345 678"
├─ sendgrid_api_key: "SG.xxxxx"
├─ recaptcha_site_key: "6Lc..."
├─ recaptcha_secret_key: "6Lc..."
```

---

## TABLA 3: TIPOS DE HABITACIÓN

```sql
CREATE TABLE tipos_habitacion (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(500),
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_slug (slug)
);
```

**Ejemplos:**
- id: 1, nombre: "Simple", slug: "simple"
- id: 2, nombre: "Doble", slug: "doble"
- id: 3, nombre: "Suite", slug: "suite"
- id: 4, nombre: "Deluxe", slug: "deluxe"

---

## TABLA 4: HABITACIONES

```sql
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
```

**Campos:**
- `id`: Identificador único
- `numero_habitacion`: Número físico (ej: 101, 202)
- `slug`: URL-friendly (ej: "suite-deluxe-101")
- `tipo_habitacion_id`: Referencia a tabla tipos_habitacion
- `estado`: Disponible | Ocupada | Mantenimiento
- `capacidad_huespedes`: Cuántas personas máximo (ej: 4)
- `num_camas`: Total de camas (ej: 2)
- `precio_base`: Precio base sin temporada (informativo)
- `activa`: Se muestra en web o no
- `rating`: Promedio de estrellas (1-5)
- `numero_resenas`: Total de reseñas
- `numero_reservas`: Total de reservas (histórico)

---

## TABLA 5: HABITACIONES POR IDIOMA

```sql
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
```

**Campos:**
- `habitacion_id`: FK a habitaciones
- `idioma`: es | en
- `nombre`: Nombre traducido (ej: "Suite Deluxe" en ES, "Deluxe Suite" en EN)
- `descripcion`: HTML enriquecido (de TinyMCE)
- `slug`: Traducido (ej: "suite-deluxe" en ES, "deluxe-suite" en EN)
- `seo_titulo`: Meta title (máx 160 caracteres)
- `seo_descripcion`: Meta description (máx 160 caracteres)
- `seo_palabras_clave`: Keywords separadas por comas
- `schema_json`: LodgingBusiness schema en JSON

---

## TABLA 6: TARIFAS POR HABITACIÓN

```sql
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
```

**Ejemplo de datos:**
```
habitacion_id: 1, tipo_tarifa: "baja", precio: 50, 
fecha_inicio: 2024-01-01, fecha_fin: 2024-04-30

habitacion_id: 1, tipo_tarifa: "alta", precio: 120, 
fecha_inicio: 2024-07-01, fecha_fin: 2024-08-31

habitacion_id: 1, tipo_tarifa: "corporativo", precio: 85, 
fecha_inicio: 2024-01-01, fecha_fin: 2024-12-31
```

---

## TABLA 7: COMODIDADES POR HABITACIÓN

```sql
CREATE TABLE habitacion_comodidades (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    comodidades JSON NOT NULL,
    UNIQUE KEY unique_comodidad (habitacion_id, idioma),
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);
```

**Contenido JSON:**
```json
[
  {
    "id": "uuid-1",
    "nombre": "WiFi Gratuito",
    "icono": "wifi",
    "activa": true
  },
  {
    "id": "uuid-2",
    "nombre": "Aire Acondicionado",
    "icono": "wind",
    "activa": true
  },
  {
    "id": "uuid-3",
    "nombre": "TV por Cable",
    "icono": "tv",
    "activa": true
  },
  {
    "id": "uuid-4",
    "nombre": "Baño Privado",
    "icono": "droplet",
    "activa": true
  }
]
```

---

## TABLA 8: AMENITIES POR HABITACIÓN

```sql
CREATE TABLE habitacion_amenities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    amenities JSON NOT NULL,
    UNIQUE KEY unique_amenity (habitacion_id, idioma),
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);
```

**Contenido JSON (mismo formato que comodidades):**
```json
[
  {
    "id": "uuid-1",
    "nombre": "Desayuno Incluido",
    "icono": "coffee",
    "activa": true
  },
  {
    "id": "uuid-2",
    "nombre": "Parking Gratuito",
    "icono": "map-pin",
    "activa": true
  },
  {
    "id": "uuid-3",
    "nombre": "Acceso a Spa",
    "icono": "heart",
    "activa": true
  }
]
```

---

## TABLA 9: DISTRIBUCIÓN DE CAMAS

```sql
CREATE TABLE habitacion_configuracion_camas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    habitacion_id INT NOT NULL,
    camas JSON NOT NULL,
    UNIQUE KEY unique_camas (habitacion_id),
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);
```

**Contenido JSON:**
```json
{
  "camas": [
    {
      "id": "cama-1",
      "tipo": "queen",
      "cantidad": 1,
      "descripcion": "Cama Queen Size 1.60x2.00m"
    },
    {
      "id": "cama-2",
      "tipo": "individual",
      "cantidad": 1,
      "descripcion": "Cama Individual 0.90x1.90m"
    }
  ],
  "capacidad_total_personas": 3,
  "numero_camas_totales": 2
}
```

**Tipos de cama disponibles:**
- individual
- matrimonial
- queen
- king
- cama_y_media
- litera
- sofacama
- futton

---

## TABLA 10: IMÁGENES DE HABITACIÓN (Relación)

```sql
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
```

**Ejemplo:**
```
habitacion_id: 1, imagen_id: 15, es_principal: true, orden: 1
habitacion_id: 1, imagen_id: 16, es_principal: false, orden: 2
habitacion_id: 1, imagen_id: 17, es_principal: false, orden: 3
```

---

## TABLA 11: GALERÍA DE IMÁGENES CENTRALIZADA

```sql
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
```

**Ejemplo de imagen:**
```
id: 15
nombre_original: "Suite_Deluxe_Main_2024.jpg"
nombre_archivo: "suite-deluxe-main-2024-xyw9z.webp"
slug: "suite-deluxe-main"
peso_original: 2500000 (bytes)
peso_webp: 650000
ruta_original: "/public/uploads/originals/suite-deluxe-main-2024-xyw9z.webp"
ruta_webp: "/public/uploads/webp/suite-deluxe-main-2024-xyw9z.webp"
ruta_thumbnail: "/public/uploads/thumbnails/suite-deluxe-main-2024-xyw9z.webp"
ruta_tiny: "/public/uploads/tiny/suite-deluxe-main-2024-xyw9z.webp"
alt_text: "Suite Deluxe con vista a la ciudad"
etiquetas: "suite, deluxe, principal"
tipo: "habitacion"
```

---

## TABLA 12: PÁGINAS ESTÁTICAS

```sql
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
```

**Ejemplo de páginas:**
```
id: 1, slug: "inicio", activa: true, mostrar_en_menu: true
id: 2, slug: "nosotros", activa: true, mostrar_en_menu: true
id: 3, slug: "contacto", activa: true, mostrar_en_menu: true
id: 4, slug: "privacidad", activa: true, mostrar_en_menu: false
```

---

## TABLA 13: CONTENIDO DE PÁGINAS POR IDIOMA

```sql
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
```

**Campos:**
- `pagina_id`: FK a paginas
- `idioma`: es | en
- `titulo`: Título de la página
- `slug`: URL slug traducido
- `contenido`: HTML enriquecido (editable en vivo)
- `seo_titulo`: Meta title
- `seo_descripcion`: Meta description
- `seo_palabras_clave`: Keywords
- `schema_json`: Schema.org JSON

---

## TABLA 14: VERSIONES DE PÁGINAS (Historial de ediciones)

```sql
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
```

---

## TABLA 15: LOCKS DE EDICIÓN (Evitar conflictos)

```sql
CREATE TABLE pagina_locks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pagina_id INT NOT NULL,
    usuario_id INT NOT NULL,
    timestamp_lock TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expira_en TIMESTAMP,
    UNIQUE KEY unique_lock (pagina_id),
    FOREIGN KEY (pagina_id) REFERENCES paginas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_expira (expira_en)
);
```

---

## TABLA 16: PÁGINAS LEGALES

```sql
CREATE TABLE paginas_legales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo ENUM('privacidad', 'terminos', 'cookies', 'aviso_legal') NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    activa BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultima_actualizacion DATETIME ON UPDATE CURRENT_TIMESTAMP
);
```

---

## TABLA 17: CONTENIDO DE PÁGINAS LEGALES POR IDIOMA

```sql
CREATE TABLE paginas_legales_idiomas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pagina_legal_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    contenido LONGTEXT NOT NULL,
    UNIQUE KEY unique_legal_idioma (pagina_legal_id, idioma),
    FOREIGN KEY (pagina_legal_id) REFERENCES paginas_legales(id) ON DELETE CASCADE
);
```

---

## TABLA 18: BLOG - ENTRADAS

```sql
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
```

---

## TABLA 19: BLOG - CONTENIDO POR IDIOMA

```sql
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
```

---

## TABLA 20: BLOG - CATEGORÍAS

```sql
CREATE TABLE blog_categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(500),
    activa BOOLEAN DEFAULT TRUE,
    INDEX idx_slug (slug)
);
```

---

## TABLA 21: BLOG - TAGS

```sql
CREATE TABLE blog_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    INDEX idx_slug (slug)
);
```

---

## TABLA 22: BLOG - RELACIÓN ENTRADA-TAGS

```sql
CREATE TABLE blog_entrada_tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    entrada_id INT NOT NULL,
    tag_id INT NOT NULL,
    UNIQUE KEY unique_entrada_tag (entrada_id, tag_id),
    FOREIGN KEY (entrada_id) REFERENCES blog_entradas(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES blog_tags(id) ON DELETE CASCADE,
    INDEX idx_entrada (entrada_id)
);
```

---

## TABLA 23: RESEÑAS Y RATINGS

```sql
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
```

**Campos:**
- `tipo`: hotel | habitacion
- `puntuacion`: 1-5 estrellas
- `titulo_resena`: Resumen de la reseña
- `contenido_resena`: HTML o texto de la reseña
- `mostrar_sitio`: Se muestra en web o no
- `moderada`: Admin la aprobó

---

## TABLA 24: FAQs DE HABITACIONES

```sql
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
```

---

## TABLA 25: FAQs DEL HOTEL

```sql
CREATE TABLE hotel_faqs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    idioma ENUM('es', 'en') NOT NULL,
    pregunta VARCHAR(255) NOT NULL,
    respuesta LONGTEXT NOT NULL,
    orden INT DEFAULT 0,
    INDEX idx_idioma (idioma)
);
```

---

## TABLA 26: FORMULARIOS

```sql
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
```

---

## TABLA 27: CAMPOS DE FORMULARIOS

```sql
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
```

**Campos:**
- `nombre_campo`: Nombre técnico (ej: "nombre_completo")
- `tipo_campo`: texto, email, telefono, textarea, select, checkbox, fecha
- `label`: Etiqueta en español
- `label_en`: Etiqueta en inglés
- `placeholder`: Texto de ayuda
- `requerido`: Campo obligatorio o no
- `opciones`: JSON si es select/checkbox

---

## TABLA 28: RESPUESTAS DE FORMULARIOS

```sql
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
```

**Contenido JSON:**
```json
{
  "nombre_completo": "Juan Pérez",
  "email": "juan@email.com",
  "telefono": "+34 612 345 678",
  "fecha_llegada": "2024-03-15",
  "fecha_salida": "2024-03-18",
  "numero_huespedes": 2,
  "mensaje": "¿Tienen cuna disponible para bebé?"
}
```

---

## TABLA 29: PROMOCIONES

```sql
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
```

**Ejemplo:**
```
id: 1
titulo: "Verano 2024"
codigo_descuento: "VERANO20"
descuento_tipo: "porcentaje"
descuento_valor: 20
publicada: true
fecha_inicio: 2024-07-01
fecha_fin: 2024-08-31
```

---

## TABLA 30: PROMOCIONES POR IDIOMA

```sql
CREATE TABLE promociones_idiomas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    promocion_id INT NOT NULL,
    idioma ENUM('es', 'en') NOT NULL,
    titulo VARCHAR(255),
    descripcion LONGTEXT,
    UNIQUE KEY unique_idioma (promocion_id, idioma),
    FOREIGN KEY (promocion_id) REFERENCES promociones(id) ON DELETE CASCADE
);
```

---

## TABLA 31: PROMOCIONES POR HABITACIÓN (Relación)

```sql
CREATE TABLE promocion_habitaciones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    promocion_id INT NOT NULL,
    habitacion_id INT NOT NULL,
    UNIQUE KEY unique_promo_hab (promocion_id, habitacion_id),
    FOREIGN KEY (promocion_id) REFERENCES promociones(id) ON DELETE CASCADE,
    FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);
```

---

## TABLA 32: SEO/GEO AVANZADO

```sql
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
```

---

## TABLA 33: RANKING DE KEYWORDS

```sql
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
```

---

## TABLA 34: SEO BÁSICO - BLOG

```sql
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
```

---

## TABLA 35: SEO AVANZADO - BLOG

```sql
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
```

---

## TABLA 36: EVENT LOGS (Auditoría)

```sql
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
```

**Ejemplo:**
```
usuario_id: 5
accion: "editar_habitacion"
entidad: "habitaciones"
entidad_id: 1
cambios: {
  "nombre": {"anterior": "Suite", "nuevo": "Suite Deluxe"},
  "precio": {"anterior": 100, "nuevo": 120}
}
ip: "192.168.1.1"
```

---

## TABLA 37: ANALYTICS

```sql
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
```

---

## RESUMEN - TOTAL DE TABLAS

**Total: 37 tablas**

### Core
- 1: usuarios
- 2: sitio_ajustes

### Habitaciones
- 3: tipos_habitacion
- 4: habitaciones
- 5: habitaciones_idiomas
- 6: habitacion_tarifas
- 7: habitacion_comodidades
- 8: habitacion_amenities
- 9: habitacion_configuracion_camas
- 10: habitacion_imagenes
- 11: imagenes

### Páginas
- 12: paginas
- 13: paginas_idiomas
- 14: paginas_versiones
- 15: pagina_locks
- 16: paginas_legales
- 17: paginas_legales_idiomas

### Blog
- 18: blog_entradas
- 19: blog_entradas_idiomas
- 20: blog_categorias
- 21: blog_tags
- 22: blog_entrada_tags

### Reseñas y FAQs
- 23: resenas
- 24: habitacion_faqs
- 25: hotel_faqs

### Formularios
- 26: formularios
- 27: formulario_campos
- 28: formulario_respuestas

### Promociones
- 29: promociones
- 30: promociones_idiomas
- 31: promocion_habitaciones

### SEO/GEO/Analytics
- 32: seo_geo
- 33: ranking_keywords
- 34: blog_seo_basico
- 35: blog_seo_avanzado
- 36: event_logs
- 37: analytics

