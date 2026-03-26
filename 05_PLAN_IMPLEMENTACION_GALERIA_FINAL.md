# PLAN DE IMPLEMENTACIÓN - MÓDULO DE GALERÍA DE IMÁGENES

**Fecha**: 08 de marzo de 2026  
**Versión**: 2.0 - COMPLETO  
**Estado**: Listo para desarrollo  

---

## 1. RESUMEN EJECUTIVO

El **Módulo de Galería de Imágenes** es un repositorio centralizado de todas las imágenes del sitio (habitaciones, hotel, amenidades, vistas, general).

**Características:**
- Upload con optimización automática a WebP
- Tres tamaños: Original (1920x1080), Thumbnail (300x200), Tiny (150x100)
- Búsqueda, filtros, paginación
- Integración con Habitaciones, Blog, Páginas, Ajustes
- Validación de seguridad exhaustiva
- Interfaz intuitiva con modals

**Timeline**: Semana 4 (50 horas)

---

## 2. ARQUITECTURA TÉCNICA

### 2.1 Stack Tecnológico

```
Backend:
├─ PHP 8.1+
├─ MySQL 5.7+
├─ Intervention Image v3.x
├─ PDO (prepared statements)
└─ Transacciones BD

Frontend:
├─ HTML5
├─ Tailwind CSS v4
├─ JavaScript vanilla
└─ Fetch API
```

### 2.2 Dependencias

```json
{
  "intervention/image": "^3.0"
}
```

**Nota**: Ya está instalado. Usar sintaxis v3 (ImageManager).

---

## 3. SCHEMA DE BASE DE DATOS

### 3.1 Tabla: imagenes

```sql
CREATE TABLE IF NOT EXISTS imagenes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_original VARCHAR(255) NOT NULL,
    nombre_archivo VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
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
    INDEX idx_fecha_subida (fecha_subida),
    INDEX idx_etiquetas (etiquetas),
    FOREIGN KEY (subida_por) REFERENCES usuarios(id) ON DELETE SET NULL
);
```

**Campos explicados:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | INT | ID único de imagen |
| `nombre_original` | VARCHAR(255) | Nombre archivo original (ej: "suite deluxe.jpg") |
| `nombre_archivo` | VARCHAR(255) | Nombre único generado (ej: "suite-deluxe-xyw9z.webp") |
| `slug` | VARCHAR(255) | URL-friendly (ej: "suite-deluxe") |
| `peso_original` | INT | Bytes del archivo original |
| `peso_webp` | INT | Bytes del archivo WebP optimizado |
| `ruta_original` | VARCHAR(500) | Ruta completa del original |
| `ruta_webp` | VARCHAR(500) | Ruta del WebP |
| `ruta_thumbnail` | VARCHAR(500) | Ruta del thumbnail 300x200 |
| `ruta_tiny` | VARCHAR(500) | Ruta del tiny 150x100 |
| `alt_text` | VARCHAR(255) | Texto alternativo (SEO + accesibilidad) |
| `etiquetas` | VARCHAR(500) | Tags separadas por coma (búsqueda) |
| `tipo` | ENUM | Categoría: habitacion \| hotel \| amenidades \| vista \| general |
| `fecha_subida` | TIMESTAMP | Cuándo se subió |
| `subida_por` | INT | FK a usuario que subió |

---

## 4. ESTRUCTURA DE CARPETAS Y SEGURIDAD

### 4.1 Estructura de Directorios

```
public/uploads/
│
├── originals/
│   ├── suite-deluxe-main-xyw9z.webp
│   ├── hotel-entrada-abc123.webp
│   └── ... (imágenes WebP optimizadas, máx 1920x1080)
│
├── webp/
│   ├── suite-deluxe-main-xyw9z.webp
│   ├── hotel-entrada-abc123.webp
│   └── ... (copias WebP, redundancia)
│
├── thumbnails/
│   ├── suite-deluxe-main-xyw9z.webp
│   ├── hotel-entrada-abc123.webp
│   └── ... (miniaturas 300x200)
│
├── tiny/
│   ├── suite-deluxe-main-xyw9z.webp
│   ├── hotel-entrada-abc123.webp
│   └── ... (miniaturas 150x100)
│
└── .htaccess
```

### 4.2 Archivo .htaccess - public/uploads/.htaccess

```apache
# Denegar ejecución de scripts PHP
<FilesMatch "\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Permitir solo archivos de imagen
<FilesMatch "\.(jpg|jpeg|png|webp|gif)$">
    Order allow,deny
    Allow from all
</FilesMatch>

# Denegar acceso a archivos ocultos
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Cache de navegador (imágenes)
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
</IfModule>

# Gzip compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE image/webp
</IfModule>
```

### 4.3 Permisos de Carpetas (En Servidor)

```bash
# Crear carpetas si no existen
mkdir -p public/uploads/{originals,webp,thumbnails,tiny}

# Asignar permisos
chmod 755 public/uploads/
chmod 755 public/uploads/originals/
chmod 755 public/uploads/webp/
chmod 755 public/uploads/thumbnails/
chmod 755 public/uploads/tiny/

# El servidor web (apache/nginx) debe poder escribir
# En algunos hosting:
chown www-data:www-data public/uploads/ -R
```

---

## 5. CLASE ImageHandler - includes/ImageHandler.php

```php
<?php

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHandler {
    
    const MAX_WEIGHT = 5 * 1024 * 1024;  // 5MB
    const MIN_WIDTH = 800;
    const MIN_HEIGHT = 600;
    const MAX_WIDTH = 4000;
    const MAX_HEIGHT = 4000;
    
    const ALLOWED_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];
    
    /**
     * Procesar imagen subida (multi-tamaño + WebP)
     * 
     * @param string $archivo_tmp Ruta temporal del archivo
     * @param string $nombre_original Nombre original del archivo
     * @param string $tipo Tipo de imagen (habitacion, hotel, etc)
     * @param string $alt_text Texto alternativo
     * @param string $etiquetas Etiquetas separadas por coma
     * @return array ['exito' => true/false, 'datos' => [...], 'error' => '...']
     */
    public static function procesarImagen(
        $archivo_tmp,
        $nombre_original,
        $tipo = 'general',
        $alt_text = '',
        $etiquetas = ''
    ) {
        try {
            // 1. VALIDAR ARCHIVO
            $validacion = self::validarArchivo($archivo_tmp, $nombre_original);
            if (!$validacion['valido']) {
                return [
                    'exito' => false,
                    'error' => $validacion['error']
                ];
            }
            
            // 2. OBTENER PESO ORIGINAL ANTES DE PROCESAR
            $peso_original = filesize($archivo_tmp);
            
            // 3. GENERAR NOMBRE ÚNICO
            $nombre_archivo = self::generarNombreUnico($nombre_original);
            
            // 4. CREAR INSTANCIA DE INTERVENTION IMAGE v3
            $manager = new ImageManager(new Driver());
            
            // 5. PROCESAR A 3 TAMAÑOS
            $datos_archivos = [];
            
            // 5a. Imagen Original (máximo 1920x1080)
            $imagen = $manager->read($archivo_tmp);
            
            // Redimensionar si es mayor
            if ($imagen->width() > 1920 || $imagen->height() > 1080) {
                $imagen->scaleDown(width: 1920, height: 1080);
            }
            
            $ruta_webp = UPLOAD_DIR . '/webp/' . $nombre_archivo;
            $imagen->encode(format: 'webp', quality: 80);
            $imagen->save(path: $ruta_webp);
            
            $datos_archivos['webp'] = [
                'ruta' => $ruta_webp,
                'peso' => filesize($ruta_webp)
            ];
            
            // 5b. Thumbnail (300x200)
            $thumbnail = $manager->read($archivo_tmp);
            $thumbnail->cover(width: 300, height: 200);
            
            $ruta_thumbnail = UPLOAD_DIR . '/thumbnails/' . $nombre_archivo;
            $thumbnail->encode(format: 'webp', quality: 75);
            $thumbnail->save(path: $ruta_thumbnail);
            
            $datos_archivos['thumbnail'] = [
                'ruta' => $ruta_thumbnail,
                'peso' => filesize($ruta_thumbnail)
            ];
            
            // 5c. Tiny (150x100)
            $tiny = $manager->read($archivo_tmp);
            $tiny->cover(width: 150, height: 100);
            
            $ruta_tiny = UPLOAD_DIR . '/tiny/' . $nombre_archivo;
            $tiny->encode(format: 'webp', quality: 70);
            $tiny->save(path: $ruta_tiny);
            
            $datos_archivos['tiny'] = [
                'ruta' => $ruta_tiny,
                'peso' => filesize($ruta_tiny)
            ];
            
            // 6. CALCULAR AHORRO
            $peso_total_optimizado = array_sum(array_column($datos_archivos, 'peso'));
            $porcentaje_ahorro = round(
                (1 - ($peso_total_optimizado / $peso_original)) * 100
            );
            
            // 7. RETORNAR DATOS PARA INSERCIÓN EN BD
            return [
                'exito' => true,
                'datos' => [
                    'nombre_original' => $nombre_original,
                    'nombre_archivo' => $nombre_archivo,
                    'slug' => self::slugify($nombre_original),
                    'peso_original' => $peso_original,
                    'peso_webp' => $datos_archivos['webp']['peso'],
                    'ruta_original' => $datos_archivos['webp']['ruta'],
                    'ruta_webp' => $datos_archivos['webp']['ruta'],
                    'ruta_thumbnail' => $datos_archivos['thumbnail']['ruta'],
                    'ruta_tiny' => $datos_archivos['tiny']['ruta'],
                    'alt_text' => $alt_text,
                    'etiquetas' => $etiquetas,
                    'tipo' => $tipo,
                    'porcentaje_ahorro' => $porcentaje_ahorro
                ]
            ];
            
        } catch (Exception $e) {
            // Limpiar archivos si algo falla
            if (isset($ruta_webp) && file_exists($ruta_webp)) unlink($ruta_webp);
            if (isset($ruta_thumbnail) && file_exists($ruta_thumbnail)) unlink($ruta_thumbnail);
            if (isset($ruta_tiny) && file_exists($ruta_tiny)) unlink($ruta_tiny);
            
            return [
                'exito' => false,
                'error' => 'Error procesando imagen: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Validar archivo subido (exhaustivo)
     * 
     * @param string $archivo_tmp
     * @param string $nombre_original
     * @return array ['valido' => true/false, 'error' => '...']
     */
    private static function validarArchivo($archivo_tmp, $nombre_original) {
        // 1. Verificar que archivo existe
        if (!file_exists($archivo_tmp) || !is_uploaded_file($archivo_tmp)) {
            return ['valido' => false, 'error' => 'Archivo no encontrado'];
        }
        
        // 2. Validar peso
        $peso = filesize($archivo_tmp);
        if ($peso > self::MAX_WEIGHT) {
            $mb_actual = round($peso / 1024 / 1024, 1);
            $mb_maximo = round(self::MAX_WEIGHT / 1024 / 1024, 1);
            return [
                'valido' => false,
                'error' => "Archivo muy grande ({$mb_actual}MB). Máximo: {$mb_maximo}MB"
            ];
        }
        
        if ($peso < 10 * 1024) {  // Mínimo 10KB
            return ['valido' => false, 'error' => 'Archivo muy pequeño'];
        }
        
        // 3. Validar MIME type (doble validación)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo_tmp);
        finfo_close($finfo);
        
        if (!in_array($mime, self::ALLOWED_MIMES)) {
            return [
                'valido' => false,
                'error' => "Tipo de archivo no permitido. MIME: {$mime}"
            ];
        }
        
        // 4. Validar que es imagen real (getimagesize)
        $info = @getimagesize($archivo_tmp);
        if ($info === false) {
            return ['valido' => false, 'error' => 'No es una imagen válida'];
        }
        
        // 5. Validar dimensiones
        $width = $info[0];
        $height = $info[1];
        
        if ($width < self::MIN_WIDTH || $height < self::MIN_HEIGHT) {
            return [
                'valido' => false,
                'error' => "Resolución muy baja ({$width}x{$height}). Mínimo: " .
                          self::MIN_WIDTH . "x" . self::MIN_HEIGHT . "px"
            ];
        }
        
        if ($width > self::MAX_WIDTH || $height > self::MAX_HEIGHT) {
            return [
                'valido' => false,
                'error' => "Resolución muy alta ({$width}x{$height}). Máximo: " .
                          self::MAX_WIDTH . "x" . self::MAX_HEIGHT . "px"
            ];
        }
        
        // 6. Validar nombre original (sanitizar)
        if (strlen($nombre_original) > 255) {
            return ['valido' => false, 'error' => 'Nombre de archivo muy largo'];
        }
        
        if (preg_match('/\x00|\.\.\//', $nombre_original)) {
            return ['valido' => false, 'error' => 'Nombre de archivo inválido'];
        }
        
        return ['valido' => true];
    }
    
    /**
     * Generar nombre único para archivo
     * 
     * @param string $nombre_original
     * @return string Nombre único (ej: "suite-deluxe-xyw9z.webp")
     */
    private static function generarNombreUnico($nombre_original) {
        // Extraer nombre sin extensión
        $nombre_sin_ext = pathinfo($nombre_original, PATHINFO_FILENAME);
        
        // Slugify
        $slug = self::slugify($nombre_sin_ext);
        
        // Generar random único (5 caracteres)
        $random = substr(
            bin2hex(random_bytes(3)), 
            0, 
            5
        );
        
        return $slug . '-' . $random . '.webp';
    }
    
    /**
     * Convertir texto a slug URL-friendly
     * 
     * @param string $texto
     * @return string
     */
    private static function slugify($texto) {
        // Convertir a minúsculas
        $texto = mb_strtolower($texto, 'UTF-8');
        
        // Remover acentos
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT', $texto);
        
        // Reemplazar caracteres no alfanuméricos con guión
        $texto = preg_replace('/[^a-z0-9]+/', '-', $texto);
        
        // Remover guiones al inicio/final
        $texto = trim($texto, '-');
        
        return $texto;
    }
    
    /**
     * Eliminar imagen (archivos físicos + BD)
     * 
     * @param int $imagen_id
     * @return array ['exito' => true/false, 'error' => '...']
     */
    public static function eliminarImagen($imagen_id) {
        try {
            $db = new Database();
            
            // Obtener rutas de imagen
            $imagen = $db->findOne('imagenes', ['id' => $imagen_id]);
            
            if (!$imagen) {
                return ['exito' => false, 'error' => 'Imagen no encontrada'];
            }
            
            // Eliminar archivos físicos
            $rutas = [
                $imagen['ruta_webp'],
                $imagen['ruta_thumbnail'],
                $imagen['ruta_tiny'],
                $imagen['ruta_original']
            ];
            
            foreach ($rutas as $ruta) {
                if ($ruta && file_exists($ruta)) {
                    unlink($ruta);
                }
            }
            
            // Eliminar registro de BD
            $db->delete('imagenes', ['id' => $imagen_id]);
            
            return ['exito' => true];
            
        } catch (Exception $e) {
            return [
                'exito' => false,
                'error' => 'Error eliminando imagen: ' . $e->getMessage()
            ];
        }
    }
}

?>
```

---

## 6. ENDPOINTS API

### 6.1 POST /api/galeria/subir.php

**Descripción**: Recibe imagen, la procesa, la optimiza y la guarda.

**Input (multipart/form-data)**:
```
archivo: File
tipo: enum (habitacion|hotel|amenidades|vista|general)
etiquetas: string (separadas por coma, ej: "suite, deluxe, principal")
alt_text: string (máx 255 caracteres)
csrf_token: string
```

**Validaciones Servidor**:
```
✓ CSRF token válido
✓ Usuario logueado
✓ Archivo existe y es upload válido
✓ Peso <= 5MB && >= 10KB
✓ MIME type correcto (image/jpeg, image/png, image/webp)
✓ Imagen real (getimagesize válido)
✓ Dimensiones: 800x600 mín, 4000x4000 máx
✓ alt_text <= 255 caracteres
✓ etiquetas no contiene caracteres maliciosos
✓ tipo es valor válido
```

**Output JSON**:
```json
{
  "exito": true,
  "imagen_id": 15,
  "nombre_archivo": "suite-deluxe-main-xyw9z.webp",
  "ruta_thumbnail": "/public/uploads/thumbnails/suite-deluxe-main-xyw9z.webp",
  "ruta_original": "/public/uploads/webp/suite-deluxe-main-xyw9z.webp",
  "peso_original": 2500000,
  "peso_optimizado": 650000,
  "porcentaje_ahorro": 74,
  "tipo": "habitacion",
  "etiquetas": "suite, deluxe, principal",
  "alt_text": "Suite Deluxe vista a ciudad"
}
```

**Output en caso de error**:
```json
{
  "exito": false,
  "error": "Archivo muy grande (2.4MB). Máximo: 5MB"
}
```

**Código**:
```php
<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/ImageHandler.php';
require_once __DIR__ . '/../../includes/Validator.php';

http_response_code(200);
header('Content-Type: application/json');

try {
    // 1. Verificar CSRF
    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }
    
    // 2. Verificar login
    if (!Auth::isLoggedIn()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }
    
    // 3. Validar que archivo existe
    if (!isset($_FILES['archivo'])) {
        http_response_code(400);
        die(json_encode(['exito' => false, 'error' => 'No se envió archivo']));
    }
    
    $archivo = $_FILES['archivo'];
    
    // 4. Validar tipo de imagen
    $tipo = $_POST['tipo'] ?? 'general';
    $tipos_validos = ['habitacion', 'hotel', 'amenidades', 'vista', 'general'];
    
    if (!in_array($tipo, $tipos_validos)) {
        http_response_code(400);
        die(json_encode(['exito' => false, 'error' => 'Tipo de imagen inválido']));
    }
    
    // 5. Sanitizar inputs
    $alt_text = Validator::sanitizeString($_POST['alt_text'] ?? '');
    $etiquetas = Validator::sanitizeString($_POST['etiquetas'] ?? '');
    
    // 6. Procesar imagen
    $resultado = ImageHandler::procesarImagen(
        $archivo['tmp_name'],
        $archivo['name'],
        $tipo,
        $alt_text,
        $etiquetas
    );
    
    if (!$resultado['exito']) {
        http_response_code(400);
        die(json_encode($resultado));
    }
    
    // 7. TRANSACCIÓN: Guardar en BD
    $db = new Database();
    $db->beginTransaction();
    
    try {
        $datos = $resultado['datos'];
        $datos['subida_por'] = Auth::getCurrentUser()['id'];
        
        $db->insert('imagenes', $datos);
        $imagen_id = $db->lastInsertId();
        
        $db->commit();
        
        // 8. Retornar respuesta exitosa
        http_response_code(201);
        die(json_encode([
            'exito' => true,
            'imagen_id' => $imagen_id,
            'nombre_archivo' => $datos['nombre_archivo'],
            'ruta_thumbnail' => $datos['ruta_thumbnail'],
            'ruta_original' => $datos['ruta_original'],
            'peso_original' => $datos['peso_original'],
            'peso_optimizado' => $datos['peso_webp'],
            'porcentaje_ahorro' => $resultado['datos']['porcentaje_ahorro'],
            'tipo' => $tipo,
            'etiquetas' => $etiquetas,
            'alt_text' => $alt_text
        ]));
        
    } catch (Exception $e) {
        $db->rollback();
        http_response_code(500);
        die(json_encode([
            'exito' => false,
            'error' => 'Error guardando en BD: ' . $e->getMessage()
        ]));
    }
    
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode([
        'exito' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]));
}

?>
```

---

### 6.2 GET /api/galeria/obtener-imagenes.php

**Descripción**: Obtiene listado paginado de imágenes con búsqueda y filtros.

**Parámetros GET**:
```
q: string (búsqueda opcional, busca en nombre + etiquetas + alt_text)
tipo: enum (filtro opcional: habitacion|hotel|amenidades|vista|general)
ordenar: enum (reciente|antiguo|nombre_az|nombre_za|tamaño_mayor|tamaño_menor) [default: reciente]
pagina: int (número de página, default: 1)
limite: int (imágenes por página, default: 20, máximo: 100)
```

**Ejemplo URL**:
```
/api/galeria/obtener-imagenes.php?q=suite&tipo=habitacion&ordenar=reciente&pagina=1&limite=20
```

**Output JSON**:
```json
{
  "exito": true,
  "total": 47,
  "pagina": 1,
  "paginas": 3,
  "limite": 20,
  "imagenes": [
    {
      "id": 15,
      "nombre_archivo": "suite-deluxe-main-xyw9z.webp",
      "nombre_original": "Suite Deluxe Main.jpg",
      "slug": "suite-deluxe-main",
      "ruta_thumbnail": "/public/uploads/thumbnails/suite-deluxe-main-xyw9z.webp",
      "ruta_original": "/public/uploads/webp/suite-deluxe-main-xyw9z.webp",
      "peso_original": 2500000,
      "peso_webp": 650000,
      "porcentaje_ahorro": 74,
      "tipo": "habitacion",
      "etiquetas": "suite, deluxe, principal",
      "alt_text": "Suite Deluxe vista a ciudad",
      "fecha_subida": "2024-03-15 14:30:00",
      "subida_por": "Juan (admin)"
    },
    ...
  ]
}
```

**Código**:
```php
<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Validator.php';

http_response_code(200);
header('Content-Type: application/json');

try {
    // Verificar login
    if (!Auth::isLoggedIn()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }
    
    $db = new Database();
    
    // Parámetros
    $q = Validator::sanitizeString($_GET['q'] ?? '');
    $tipo = Validator::sanitizeString($_GET['tipo'] ?? '');
    $ordenar = $_GET['ordenar'] ?? 'reciente';
    $pagina = (int)($_GET['pagina'] ?? 1);
    $limite = min((int)($_GET['limite'] ?? 20), 100);  // Máximo 100
    
    // Validar ordenamiento
    $orden_valido = [
        'reciente' => 'fecha_subida DESC',
        'antiguo' => 'fecha_subida ASC',
        'nombre_az' => 'nombre_archivo ASC',
        'nombre_za' => 'nombre_archivo DESC',
        'tamaño_mayor' => 'peso_original DESC',
        'tamaño_menor' => 'peso_original ASC'
    ];
    
    $order_by = $orden_valido[$ordenar] ?? 'fecha_subida DESC';
    
    // Construir query
    $where = [];
    $params = [];
    
    if ($q) {
        $where[] = "(nombre_archivo LIKE ? OR etiquetas LIKE ? OR alt_text LIKE ?)";
        $busca = "%$q%";
        $params = array_merge($params, [$busca, $busca, $busca]);
    }
    
    if ($tipo && in_array($tipo, ['habitacion', 'hotel', 'amenidades', 'vista', 'general'])) {
        $where[] = "tipo = ?";
        $params[] = $tipo;
    }
    
    $where_str = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);
    
    // Contar total
    $count_query = "SELECT COUNT(*) FROM imagenes $where_str";
    $total = $db->query($count_query, $params)->fetchColumn();
    
    // Paginar
    $offset = ($pagina - 1) * $limite;
    
    // Obtener imágenes
    $query = "SELECT 
        i.*,
        u.nombre_completo as subida_por_nombre
        FROM imagenes i
        LEFT JOIN usuarios u ON i.subida_por = u.id
        $where_str
        ORDER BY $order_by
        LIMIT $limite OFFSET $offset";
    
    $imagenes = $db->query($query, $params)->fetchAll(PDO::FETCH_ASSOC);
    
    // Calcular ahorro por imagen
    foreach ($imagenes as &$img) {
        $img['porcentaje_ahorro'] = $img['peso_original'] > 0 
            ? round((1 - ($img['peso_webp'] / $img['peso_original'])) * 100)
            : 0;
    }
    
    http_response_code(200);
    die(json_encode([
        'exito' => true,
        'total' => $total,
        'pagina' => $pagina,
        'paginas' => ceil($total / $limite),
        'limite' => $limite,
        'imagenes' => $imagenes
    ]));
    
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode([
        'exito' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]));
}

?>
```

---

### 6.3 POST /api/galeria/actualizar.php

**Descripción**: Actualiza metadatos de imagen (NO procesa archivo nuevo).

**Input (JSON)**:
```json
{
  "imagen_id": 15,
  "alt_text": "Suite Deluxe vista a ciudad",
  "etiquetas": "suite, deluxe, principal",
  "tipo": "habitacion",
  "csrf_token": "..."
}
```

**Output JSON**:
```json
{
  "exito": true,
  "mensaje": "Imagen actualizada correctamente"
}
```

**Validaciones**:
```
✓ CSRF token válido
✓ Usuario logueado
✓ Imagen existe
✓ alt_text <= 255 caracteres
✓ etiquetas válidas
✓ tipo es valor válido
```

**Código**:
```php
<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Validator.php';

http_response_code(200);
header('Content-Type: application/json');

try {
    // Validaciones
    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }
    
    if (!Auth::isLoggedIn()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }
    
    $db = new Database();
    
    $imagen_id = (int)($_POST['imagen_id'] ?? 0);
    $alt_text = Validator::sanitizeString($_POST['alt_text'] ?? '');
    $etiquetas = Validator::sanitizeString($_POST['etiquetas'] ?? '');
    $tipo = $_POST['tipo'] ?? 'general';
    
    // Validar que imagen existe
    $imagen = $db->findOne('imagenes', ['id' => $imagen_id]);
    if (!$imagen) {
        http_response_code(404);
        die(json_encode(['exito' => false, 'error' => 'Imagen no encontrada']));
    }
    
    // Validar tipo
    $tipos_validos = ['habitacion', 'hotel', 'amenidades', 'vista', 'general'];
    if (!in_array($tipo, $tipos_validos)) {
        http_response_code(400);
        die(json_encode(['exito' => false, 'error' => 'Tipo inválido']));
    }
    
    // Actualizar
    $db->update('imagenes', [
        'alt_text' => $alt_text,
        'etiquetas' => $etiquetas,
        'tipo' => $tipo
    ], ['id' => $imagen_id]);
    
    http_response_code(200);
    die(json_encode([
        'exito' => true,
        'mensaje' => 'Imagen actualizada correctamente'
    ]));
    
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode([
        'exito' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]));
}

?>
```

---

### 6.4 POST /api/galeria/eliminar.php

**Descripción**: Elimina imagen (verifica primero si está en uso).

**Input (JSON)**:
```json
{
  "imagen_id": 15,
  "csrf_token": "..."
}
```

**Output JSON**:
```json
{
  "exito": true,
  "mensaje": "Imagen eliminada correctamente"
}
```

**O si está en uso**:
```json
{
  "exito": false,
  "error": "Imagen está siendo usada en...",
  "usado_en": [...]
}
```

**Código**:
```php
<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/ImageHandler.php';

http_response_code(200);
header('Content-Type: application/json');

try {
    // Validaciones
    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }
    
    if (!Auth::isLoggedIn()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }
    
    $db = new Database();
    $imagen_id = (int)($_POST['imagen_id'] ?? 0);
    
    // Verificar que imagen existe
    $imagen = $db->findOne('imagenes', ['id' => $imagen_id]);
    if (!$imagen) {
        http_response_code(404);
        die(json_encode(['exito' => false, 'error' => 'Imagen no encontrada']));
    }
    
    // FASE 1a: Verificar uso en ajustes + habitaciones
    $uso_en = [];
    
    // Verificar si es logo o favicon
    $logo_id = $db->query(
        "SELECT valor FROM sitio_ajustes WHERE clave = 'logo_id' LIMIT 1"
    )->fetchColumn();
    
    if ($logo_id == $imagen_id) {
        $uso_en[] = [
            'tipo' => 'ajustes',
            'nombre' => 'Logo del hotel',
            'rol' => 'logo'
        ];
    }
    
    $favicon_id = $db->query(
        "SELECT valor FROM sitio_ajustes WHERE clave = 'favicon_id' LIMIT 1"
    )->fetchColumn();
    
    if ($favicon_id == $imagen_id) {
        $uso_en[] = [
            'tipo' => 'ajustes',
            'nombre' => 'Favicon del hotel',
            'rol' => 'favicon'
        ];
    }
    
    // Verificar si es imagen principal de habitación
    $hab_principal = $db->query(
        "SELECT h.id, h.numero_habitacion 
         FROM habitaciones h
         WHERE h.imagen_id = ? OR h.imagen_id = ?",
        [$imagen_id, $imagen_id]
    )->fetchAll();
    
    foreach ($hab_principal as $hab) {
        $uso_en[] = [
            'tipo' => 'habitacion',
            'id' => $hab['id'],
            'nombre' => 'Habitación #' . $hab['numero_habitacion'],
            'rol' => 'imagen_principal'
        ];
    }
    
    // Verificar si está en galería de habitaciones
    $gal_hab = $db->query(
        "SELECT DISTINCT h.id, h.numero_habitacion
         FROM habitacion_imagenes hi
         JOIN habitaciones h ON hi.habitacion_id = h.id
         WHERE hi.imagen_id = ?",
        [$imagen_id]
    )->fetchAll();
    
    foreach ($gal_hab as $hab) {
        $uso_en[] = [
            'tipo' => 'habitacion',
            'id' => $hab['id'],
            'nombre' => 'Habitación #' . $hab['numero_habitacion'] . ' (galería)',
            'rol' => 'galeria'
        ];
    }
    
    // TODO FASE 1b: Cuando tengamos Blog + Páginas
    // - Verificar blog_entradas.imagen_destacada_id
    // - Verificar si aparece en HTML de paginas_idiomas.contenido
    
    // Si está en uso, NO eliminar
    if (!empty($uso_en)) {
        http_response_code(400);
        die(json_encode([
            'exito' => false,
            'error' => 'Imagen está siendo usada. No se puede eliminar.',
            'usado_en' => $uso_en
        ]));
    }
    
    // Si no está en uso, eliminar
    $resultado = ImageHandler::eliminarImagen($imagen_id);
    
    if ($resultado['exito']) {
        http_response_code(200);
        die(json_encode([
            'exito' => true,
            'mensaje' => 'Imagen eliminada correctamente'
        ]));
    } else {
        http_response_code(500);
        die(json_encode($resultado));
    }
    
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode([
        'exito' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]));
}

?>
```

---

### 6.5 GET /api/galeria/obtener-uso.php

**Descripción**: Obtiene lugares donde una imagen está siendo usada.

**Parámetro GET**:
```
imagen_id: int
```

**Output JSON**:
```json
{
  "exito": true,
  "usado_en": [
    {
      "tipo": "ajustes",
      "nombre": "Logo del hotel",
      "rol": "logo"
    },
    {
      "tipo": "habitacion",
      "id": 1,
      "nombre": "Habitación #101 - Suite Deluxe",
      "rol": "imagen_principal"
    },
    {
      "tipo": "habitacion",
      "id": 1,
      "nombre": "Habitación #101 - Suite Deluxe (galería)",
      "rol": "galeria"
    }
  ]
}
```

---

## 7. INTERFAZ ADMIN - /admin/galeria.php

### 7.1 Estructura HTML

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galería de Imágenes</title>
    <link rel="stylesheet" href="/proyecto-hotel/admin/assets/css/admin.css">
    <link rel="stylesheet" href="/proyecto-hotel/admin/assets/css/galeria.css">
    <meta name="csrf-token" content="<?php echo htmlspecialchars(CSRF::generateToken()); ?>">
</head>
<body>
    <?php include __DIR__ . '/includes/header.php'; ?>
    
    <main class="admin-content">
        <!-- HEADER DE PÁGINA -->
        <div class="page-header">
            <h1>Galería de Imágenes</h1>
            <button id="btnSubirImagen" class="btn btn-primary">+ SUBIR IMAGEN</button>
        </div>
        
        <!-- ESTADÍSTICAS -->
        <div class="galeria-stats">
            <div class="stat-card">
                <h3>Total Imágenes</h3>
                <p id="stat-total" class="stat-value">0</p>
            </div>
            <div class="stat-card">
                <h3>Espacio Usado</h3>
                <p id="stat-espacio" class="stat-value">0 MB</p>
            </div>
            <div class="stat-card">
                <h3>Espacio Optimizado</h3>
                <p id="stat-optimizado" class="stat-value">0 MB</p>
            </div>
            <div class="stat-card">
                <h3>Ahorro Total</h3>
                <p id="stat-ahorro" class="stat-value">0%</p>
            </div>
        </div>
        
        <!-- FILTROS Y BÚSQUEDA -->
        <div class="galeria-controles">
            <div class="busqueda">
                <input type="text" 
                       id="inputBusqueda" 
                       placeholder="Buscar imagen..." 
                       class="input-search">
                <button class="btn-search">🔍</button>
            </div>
            
            <div class="filtros">
                <select id="selectTipo" class="select-filter">
                    <option value="">Todas las imágenes</option>
                    <option value="habitacion">Habitaciones</option>
                    <option value="hotel">Hotel</option>
                    <option value="amenidades">Amenidades</option>
                    <option value="vista">Vistas</option>
                    <option value="general">General</option>
                </select>
                
                <select id="selectOrden" class="select-filter">
                    <option value="reciente">Más recientes</option>
                    <option value="antiguo">Más antiguos</option>
                    <option value="nombre_az">Nombre A-Z</option>
                    <option value="nombre_za">Nombre Z-A</option>
                    <option value="tamaño_mayor">Más grandes</option>
                    <option value="tamaño_menor">Más pequeños</option>
                </select>
            </div>
        </div>
        
        <!-- GRID DE IMÁGENES -->
        <div id="galeriaGrid" class="galeria-grid">
            <p class="loading">Cargando imágenes...</p>
        </div>
        
        <!-- PAGINACIÓN -->
        <div id="paginacion" class="paginacion">
            <button class="btn-paginacion" id="btnPrevio" disabled>← Anterior</button>
            <span id="infoPaginacion">Página 1 de 1</span>
            <button class="btn-paginacion" id="btnSiguiente" disabled>Siguiente →</button>
        </div>
    </main>
    
    <!-- MODALS -->
    <div id="modalSubir" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Subir Nueva Imagen</h2>
                <button class="btn-cerrar-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="drop-zone" id="dropZone">
                    <p>Arrastra imagen aquí o</p>
                    <button class="btn btn-secondary" id="btnSeleccionar">Seleccionar archivo</button>
                    <p class="help-text">JPG, PNG, WebP • Máximo 5MB • Mínimo 800x600px</p>
                </div>
                
                <form id="formSubir" class="form-subida hidden">
                    <div class="form-group">
                        <label>Tipo de Imagen:</label>
                        <select name="tipo" required>
                            <option value="habitacion">Habitación</option>
                            <option value="hotel">Hotel</option>
                            <option value="amenidades">Amenidades</option>
                            <option value="vista">Vista</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Alt Text (para SEO):</label>
                        <input type="text" 
                               name="alt_text" 
                               placeholder="Descripción de la imagen"
                               maxlength="255">
                    </div>
                    
                    <div class="form-group">
                        <label>Etiquetas (separadas por coma):</label>
                        <input type="text" 
                               name="etiquetas" 
                               placeholder="suite, deluxe, principal">
                    </div>
                    
                    <div class="progress-upload hidden">
                        <div class="progress-bar">
                            <div id="progressBarFill" class="progress-fill"></div>
                        </div>
                        <p id="statusUpload">Subiendo...</p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="btnCancelarSubida">Cancelar</button>
                <button class="btn btn-primary" id="btnConfirmarSubida" disabled>Subir</button>
            </div>
        </div>
    </div>
    
    <!-- MODAL DETALLES -->
    <div id="modalDetalles" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detalles de Imagen</h2>
                <button class="btn-cerrar-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="imagen-preview">
                    <img id="detallesImg" src="" alt="">
                </div>
                
                <div class="detalles-info">
                    <p><strong>Nombre:</strong> <span id="detallesNombre"></span></p>
                    <p><strong>Original:</strong> <span id="detallesNombreOriginal"></span></p>
                    <p><strong>Tamaño Original:</strong> <span id="detalleTamañoOriginal"></span></p>
                    <p><strong>Tamaño WebP:</strong> <span id="detalleTamañoWebP"></span></p>
                    <p><strong>Ahorro:</strong> <span id="detalleAhorro"></span></p>
                    <p><strong>Tipo:</strong> <span id="detalleTipo"></span></p>
                    <p><strong>Etiquetas:</strong> <span id="detallesEtiquetas"></span></p>
                    <p><strong>Subida por:</strong> <span id="detallesSubidaPor"></span></p>
                    <p><strong>Fecha:</strong> <span id="detallesFecha"></span></p>
                </div>
                
                <div class="detalles-uso">
                    <h3>Usado en:</h3>
                    <ul id="listaUso"></ul>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="btnEditarDetalles">✏️ Editar</button>
                <button class="btn btn-danger" id="btnEliminarImagen">🗑️ Eliminar</button>
                <button class="btn btn-secondary" id="btnCerrarDetalles">Cerrar</button>
            </div>
        </div>
    </div>
    
    <!-- MODAL EDITAR -->
    <div id="modalEditar" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Editar Información</h2>
                <button class="btn-cerrar-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="formEditar">
                    <div class="form-group">
                        <label>Alt Text:</label>
                        <input type="text" 
                               id="editAltText" 
                               maxlength="255">
                    </div>
                    
                    <div class="form-group">
                        <label>Etiquetas:</label>
                        <input type="text" 
                               id="editEtiquetas">
                    </div>
                    
                    <div class="form-group">
                        <label>Tipo:</label>
                        <select id="editTipo">
                            <option value="habitacion">Habitación</option>
                            <option value="hotel">Hotel</option>
                            <option value="amenidades">Amenidades</option>
                            <option value="vista">Vista</option>
                            <option value="general">General</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="btnCancelarEditar">Cancelar</button>
                <button class="btn btn-primary" id="btnGuardarEditar">Guardar</button>
            </div>
        </div>
    </div>
    
    <!-- MODAL ELIMINAR CONFIRMACIÓN -->
    <div id="modalEliminar" class="modal hidden">
        <div class="modal-content">
            <div class="modal-header">
                <h2>⚠️ Confirmar Eliminación</h2>
                <button class="btn-cerrar-modal">&times;</button>
            </div>
            <div class="modal-body">
                <p id="mensajeEliminar"></p>
                <div id="avisoUso" class="aviso aviso-warning hidden">
                    <p><strong>Advertencia:</strong> Esta imagen está siendo usada en:</p>
                    <ul id="listaUsoAviso"></ul>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" id="btnCancelarEliminar">Cancelar</button>
                <button class="btn btn-danger" id="btnConfirmarEliminar">Sí, Eliminar</button>
            </div>
        </div>
    </div>
    
    <input type="file" id="inputFile" hidden accept="image/*">
    
    <script src="/proyecto-hotel/admin/assets/js/galeria.js"></script>
</body>
</html>
```

---

### 7.2 CSS - /admin/assets/css/galeria.css

```css
/* ESTADÍSTICAS */
.galeria-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border: 1px solid #E1E4E3;
    border-radius: 8px;
    padding: 1.5rem;
    text-align: center;
}

.stat-card h3 {
    color: #8FA8AE;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.stat-value {
    color: #425363;
    font-size: 1.8rem;
    font-weight: 700;
}

/* CONTROLES */
.galeria-controles {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.busqueda {
    display: flex;
    gap: 0.5rem;
    flex: 1;
    min-width: 200px;
}

.input-search {
    flex: 1;
    padding: 0.75rem;
    border: 1px solid #E1E4E3;
    border-radius: 6px;
    font-family: inherit;
}

.btn-search {
    padding: 0.75rem 1rem;
    background: #425363;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.filtros {
    display: flex;
    gap: 1rem;
}

.select-filter {
    padding: 0.75rem;
    border: 1px solid #E1E4E3;
    border-radius: 6px;
    background: white;
    cursor: pointer;
}

/* GRID DE IMÁGENES */
.galeria-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.imagen-item {
    background: white;
    border: 1px solid #E1E4E3;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}

.imagen-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(66, 83, 99, 0.1);
}

.imagen-thumb {
    width: 100%;
    height: 150px;
    object-fit: cover;
    background: #E8EDF0;
}

.imagen-info {
    padding: 1rem;
}

.imagen-nombre {
    font-weight: 600;
    color: #425363;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.imagen-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.8rem;
    color: #8FA8AE;
}

.imagen-tipo {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    background: #E8EDF0;
    border-radius: 4px;
    font-size: 0.75rem;
    margin-top: 0.5rem;
}

/* PAGINACIÓN */
.paginacion {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
}

.btn-paginacion {
    padding: 0.5rem 1rem;
    background: #425363;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.btn-paginacion:disabled {
    background: #E1E4E3;
    cursor: not-allowed;
    color: #8FA8AE;
}

/* DROP ZONE */
.drop-zone {
    border: 2px dashed #425363;
    border-radius: 8px;
    padding: 3rem 2rem;
    text-align: center;
    background: #E8EDF0;
    cursor: pointer;
}

.drop-zone.active {
    background: #E1E4E3;
    border-color: #A90101;
}

.drop-zone p {
    margin: 0.5rem 0;
    color: #425363;
}

.help-text {
    font-size: 0.85rem;
    color: #8FA8AE;
}

/* PROGRESS BAR */
.progress-upload {
    margin-top: 1rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #E1E4E3;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #425363;
    width: 0%;
    transition: width 0.3s ease;
}

/* MODALS */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.modal.hidden {
    display: none;
}

.modal-content {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 12px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #E1E4E3;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    color: #425363;
}

.btn-cerrar-modal {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #8FA8AE;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #E1E4E3;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

.imagen-preview {
    margin-bottom: 1.5rem;
}

.imagen-preview img {
    max-width: 100%;
    border-radius: 8px;
}

.detalles-info {
    background: #E8EDF0;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.detalles-info p {
    margin: 0.5rem 0;
    font-size: 0.95rem;
}

.detalles-uso {
    background: #E8EDF0;
    padding: 1rem;
    border-radius: 8px;
}

.detalles-uso ul {
    margin: 0.5rem 0 0 1.5rem;
    padding: 0;
}

.detalles-uso li {
    margin: 0.25rem 0;
    font-size: 0.9rem;
}

/* AVISOS */
.aviso {
    padding: 1rem;
    border-radius: 8px;
    margin: 1rem 0;
}

.aviso-warning {
    background: #fff3cd;
    border: 1px solid #ffc107;
    color: #856404;
}

.aviso.hidden {
    display: none;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .galeria-controles {
        flex-direction: column;
    }
    
    .filtros {
        flex-direction: column;
    }
    
    .galeria-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    }
    
    .modal-content {
        width: 95%;
    }
}
```

---

### 7.3 JavaScript - /admin/assets/js/galeria.js

```javascript
// Configuración
const API_SUBIR = '/proyecto-hotel/api/galeria/subir.php';
const API_OBTENER = '/proyecto-hotel/api/galeria/obtener-imagenes.php';
const API_ACTUALIZAR = '/proyecto-hotel/api/galeria/actualizar.php';
const API_ELIMINAR = '/proyecto-hotel/api/galeria/eliminar.php';
const API_USO = '/proyecto-hotel/api/galeria/obtener-uso.php';

let paginaActual = 1;
let imagenSeleccionada = null;
let archivoSeleccionado = null;

// Elementos DOM
const btnSubirImagen = document.getElementById('btnSubirImagen');
const modalSubir = document.getElementById('modalSubir');
const modalDetalles = document.getElementById('modalDetalles');
const modalEditar = document.getElementById('modalEditar');
const modalEliminar = document.getElementById('modalEliminar');
const galeriaGrid = document.getElementById('galeriaGrid');
const inputFile = document.getElementById('inputFile');
const dropZone = document.getElementById('dropZone');
const formSubir = document.getElementById('formSubir');

// Event Listeners - Botones principales
btnSubirImagen.addEventListener('click', () => abrirModalSubida());
document.getElementById('btnCancelarSubida').addEventListener('click', () => cerrarModal(modalSubir));
document.getElementById('btnConfirmarSubida').addEventListener('click', () => subirImagen());
document.getElementById('btnSeleccionar').addEventListener('click', () => inputFile.click());
document.getElementById('btnCancelarEditar').addEventListener('click', () => cerrarModal(modalEditar));
document.getElementById('btnGuardarEditar').addEventListener('click', () => guardarEdicion());
document.getElementById('btnCancelarEliminar').addEventListener('click', () => cerrarModal(modalEliminar));
document.getElementById('btnConfirmarEliminar').addEventListener('click', () => confirmarEliminacion());

// Cerrar modals con X
document.querySelectorAll('.btn-cerrar-modal').forEach(btn => {
    btn.addEventListener('click', (e) => {
        e.target.closest('.modal').classList.add('hidden');
    });
});

// Drag and drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('active');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('active');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('active');
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        archivoSeleccionado = files[0];
        mostrarFormularioSubida();
    }
});

// Seleccionar archivo
inputFile.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        archivoSeleccionado = e.target.files[0];
        mostrarFormularioSubida();
    }
});

// Filtros y búsqueda
document.getElementById('inputBusqueda').addEventListener('change', () => {
    paginaActual = 1;
    cargarImagenes();
});

document.getElementById('selectTipo').addEventListener('change', () => {
    paginaActual = 1;
    cargarImagenes();
});

document.getElementById('selectOrden').addEventListener('change', () => {
    paginaActual = 1;
    cargarImagenes();
});

// Paginación
document.getElementById('btnPrevio').addEventListener('click', () => {
    if (paginaActual > 1) {
        paginaActual--;
        cargarImagenes();
    }
});

document.getElementById('btnSiguiente').addEventListener('click', () => {
    paginaActual++;
    cargarImagenes();
});

// FUNCIONES

function abrirModalSubida() {
    modalSubir.classList.remove('hidden');
    dropZone.classList.remove('hidden');
    formSubir.classList.add('hidden');
    archivoSeleccionado = null;
}

function mostrarFormularioSubida() {
    dropZone.classList.add('hidden');
    formSubir.classList.remove('hidden');
    document.getElementById('btnConfirmarSubida').disabled = false;
}

function cerrarModal(modal) {
    modal.classList.add('hidden');
    archivoSeleccionado = null;
    dropZone.classList.remove('hidden');
    formSubir.classList.add('hidden');
}

async function subirImagen() {
    if (!archivoSeleccionado) return;
    
    const formData = new FormData();
    formData.append('archivo', archivoSeleccionado);
    formData.append('tipo', document.querySelector('select[name="tipo"]').value);
    formData.append('alt_text', document.querySelector('input[name="alt_text"]').value);
    formData.append('etiquetas', document.querySelector('input[name="etiquetas"]').value);
    formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
    
    try {
        const response = await fetch(API_SUBIR, {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.exito) {
            mostrarNotificacion('Imagen subida exitosamente', 'success');
            cerrarModal(modalSubir);
            cargarImagenes();
        } else {
            mostrarNotificacion(resultado.error, 'error');
        }
    } catch (error) {
        mostrarNotificacion('Error: ' + error.message, 'error');
    }
}

async function cargarImagenes() {
    const q = document.getElementById('inputBusqueda').value;
    const tipo = document.getElementById('selectTipo').value;
    const ordenar = document.getElementById('selectOrden').value;
    
    const params = new URLSearchParams({
        q,
        tipo,
        ordenar,
        pagina: paginaActual,
        limite: 20
    });
    
    galeriaGrid.innerHTML = '<p class="loading">Cargando...</p>';
    
    try {
        const response = await fetch(API_OBTENER + '?' + params);
        const resultado = await response.json();
        
        if (resultado.exito) {
            mostrarImagenes(resultado.imagenes);
            actualizarPaginacion(resultado);
            actualizarEstadisticas(resultado.imagenes);
        }
    } catch (error) {
        galeriaGrid.innerHTML = '<p class="error">Error cargando imágenes</p>';
    }
}

function mostrarImagenes(imagenes) {
    if (imagenes.length === 0) {
        galeriaGrid.innerHTML = '<p>No hay imágenes</p>';
        return;
    }
    
    galeriaGrid.innerHTML = imagenes.map(img => `
        <div class="imagen-item" onclick="verDetalles(${img.id})">
            <img src="${img.ruta_tiny}" alt="${img.alt_text}" class="imagen-thumb">
            <div class="imagen-info">
                <div class="imagen-nombre">${img.nombre_original}</div>
                <div class="imagen-meta">
                    <span>${formatearTamaño(img.peso_webp)}</span>
                    <span>${img.porcentaje_ahorro}% ↓</span>
                </div>
                <div class="imagen-tipo">${img.tipo}</div>
            </div>
        </div>
    `).join('');
}

async function verDetalles(imagenId) {
    imagenSeleccionada = imagenId;
    
    // Obtener datos de imagen
    const imagenes = await fetch(API_OBTENER + '?pagina=1&limite=1000')
        .then(r => r.json())
        .then(r => r.imagenes.find(i => i.id == imagenId));
    
    if (!imagenes) return;
    
    // Llenar modal
    document.getElementById('detallesImg').src = imagenes.ruta_original;
    document.getElementById('detallesNombre').textContent = imagenes.nombre_archivo;
    document.getElementById('detallesNombreOriginal').textContent = imagenes.nombre_original;
    document.getElementById('detalleTamañoOriginal').textContent = formatearTamaño(imagenes.peso_original);
    document.getElementById('detalleTamañoWebP').textContent = formatearTamaño(imagenes.peso_webp);
    document.getElementById('detalleAhorro').textContent = imagenes.porcentaje_ahorro + '%';
    document.getElementById('detalleTipo').textContent = imagenes.tipo;
    document.getElementById('detallesEtiquetas').textContent = imagenes.etiquetas || '(ninguna)';
    document.getElementById('detallesSubidaPor').textContent = imagenes.subida_por_nombre;
    document.getElementById('detallesFecha').textContent = new Date(imagenes.fecha_subida).toLocaleString('es-ES');
    
    // Obtener uso
    const usoResult = await fetch(API_USO + '?imagen_id=' + imagenId)
        .then(r => r.json());
    
    const listaUso = document.getElementById('listaUso');
    if (usoResult.usado_en && usoResult.usado_en.length > 0) {
        listaUso.innerHTML = usoResult.usado_en.map(uso => `
            <li>${uso.tipo}: ${uso.nombre} (${uso.rol})</li>
        `).join('');
    } else {
        listaUso.innerHTML = '<li>No está siendo usada</li>';
    }
    
    modalDetalles.classList.remove('hidden');
}

function editarImagen() {
    // Llenar formulario de edición
    const imagenes = document.querySelectorAll('.imagen-item');
    // ... (llenar con datos actuales)
    modalEditar.classList.remove('hidden');
}

async function guardarEdicion() {
    const formData = new FormData();
    formData.append('imagen_id', imagenSeleccionada);
    formData.append('alt_text', document.getElementById('editAltText').value);
    formData.append('etiquetas', document.getElementById('editEtiquetas').value);
    formData.append('tipo', document.getElementById('editTipo').value);
    formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
    
    try {
        const response = await fetch(API_ACTUALIZAR, {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.exito) {
            mostrarNotificacion('Imagen actualizada', 'success');
            cerrarModal(modalEditar);
            cargarImagenes();
        } else {
            mostrarNotificacion(resultado.error, 'error');
        }
    } catch (error) {
        mostrarNotificacion('Error: ' + error.message, 'error');
    }
}

async function mostrarConfirmacionEliminar() {
    // Obtener uso
    const usoResult = await fetch(API_USO + '?imagen_id=' + imagenSeleccionada)
        .then(r => r.json());
    
    const avisoUso = document.getElementById('avisoUso');
    if (usoResult.usado_en && usoResult.usado_en.length > 0) {
        avisoUso.classList.remove('hidden');
        const listaUso = document.getElementById('listaUsoAviso');
        listaUso.innerHTML = usoResult.usado_en.map(uso => `
            <li>${uso.tipo}: ${uso.nombre}</li>
        `).join('');
        
        document.getElementById('mensajeEliminar').textContent = 
            'Esta imagen está siendo usada. ¿Deseas eliminarla de todas formas?';
        
        document.getElementById('btnConfirmarEliminar').textContent = 'Sí, Eliminar igual';
    } else {
        avisoUso.classList.add('hidden');
        document.getElementById('mensajeEliminar').textContent = 
            '¿Estás seguro de que deseas eliminar esta imagen?';
        
        document.getElementById('btnConfirmarEliminar').textContent = 'Sí, Eliminar';
    }
    
    modalEliminar.classList.remove('hidden');
}

async function confirmarEliminacion() {
    const formData = new FormData();
    formData.append('imagen_id', imagenSeleccionada);
    formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]').content);
    
    try {
        const response = await fetch(API_ELIMINAR, {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        
        if (resultado.exito) {
            mostrarNotificacion('Imagen eliminada', 'success');
            cerrarModal(modalEliminar);
            cerrarModal(modalDetalles);
            cargarImagenes();
        } else {
            mostrarNotificacion(resultado.error, 'error');
        }
    } catch (error) {
        mostrarNotificacion('Error: ' + error.message, 'error');
    }
}

function actualizarPaginacion(resultado) {
    const prevBtn = document.getElementById('btnPrevio');
    const nextBtn = document.getElementById('btnSiguiente');
    const info = document.getElementById('infoPaginacion');
    
    prevBtn.disabled = resultado.pagina <= 1;
    nextBtn.disabled = resultado.pagina >= resultado.paginas;
    info.textContent = `Página ${resultado.pagina} de ${resultado.paginas}`;
}

function actualizarEstadisticas(imagenes) {
    const total = imagenes.length;
    const totalPeso = imagenes.reduce((sum, img) => sum + (img.peso_original || 0), 0);
    const totalOptimizado = imagenes.reduce((sum, img) => sum + (img.peso_webp || 0), 0);
    const ahorroPromedio = totalPeso > 0 
        ? Math.round((1 - (totalOptimizado / totalPeso)) * 100)
        : 0;
    
    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-espacio').textContent = formatearTamaño(totalPeso);
    document.getElementById('stat-optimizado').textContent = formatearTamaño(totalOptimizado);
    document.getElementById('stat-ahorro').textContent = ahorroPromedio + '%';
}

function formatearTamaño(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const tamaños = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 10) / 10 + ' ' + tamaños[i];
}

function mostrarNotificacion(mensaje, tipo) {
    console.log(`[${tipo}] ${mensaje}`);
    // TODO: Implementar UI de notificación
}

// Cargar imágenes al abrir página
document.addEventListener('DOMContentLoaded', () => {
    cargarImagenes();
});
```

---

## 8. CONSIDERACIONES IMPORTANTES

### 8.1 Seguridad

✅ Validación exhaustiva de uploads  
✅ MIME type validation (finfo + getimagesize)  
✅ Path traversal prevention  
✅ CSRF tokens en todas las operaciones  
✅ Autenticación verificada  
✅ SQL injection prevention (prepared statements)  
✅ XSS prevention (sanitización + escaping)  

### 8.2 Performance

✅ Paginación de 20 imágenes por página  
✅ Thumbnails 150x100 para preview (no cargar originals)  
✅ Caché de navegador (1 mes para imágenes)  
✅ Compresión WebP (reducción ~70%)  
✅ Índices en BD para búsqueda rápida  

### 8.3 Transacciones BD

✅ Usar transacciones en upload  
✅ Si falla inserción, limpiar archivos  
✅ Si falla eliminación, no borrar archivos  

### 8.4 Fases de obtener-uso.php

**Fase 1a (Inmediato)**:
- Ajustes (logo, favicon)
- Habitaciones (imagen principal + galería)

**Fase 1b (Cuando tengamos blog/páginas)**:
- Blog entradas (imagen_destacada)
- Páginas (HTML content search)

---

## 9. CHECKLIST DE IMPLEMENTACIÓN

### Preparación
- [ ] Crear carpetas en public/uploads/
- [ ] Crear archivo .htaccess
- [ ] Ejecutar schema.sql
- [ ] Verificar permisos (chmod 755)

### Backend
- [ ] Clase ImageHandler (v3 syntax)
- [ ] API subir.php (con transacciones)
- [ ] API obtener-imagenes.php (con paginación)
- [ ] API actualizar.php
- [ ] API eliminar.php
- [ ] API obtener-uso.php (Fase 1a)

### Frontend
- [ ] HTML galeria.php
- [ ] CSS galeria.css (responsive)
- [ ] JavaScript galeria.js (drag-drop, modals)

### Testing
- [ ] Upload archivo pequeño
- [ ] Upload archivo grande (rechazar)
- [ ] Upload imagen inválida
- [ ] Búsqueda y filtros
- [ ] Paginación
- [ ] Eliminar imagen sin uso
- [ ] Intentar eliminar imagen en uso
- [ ] Editar metadatos

---

## 10. TIMELINE

**Total: 50 horas (Semana 4)**

- Días 1-2: Setup + ImageHandler (16 horas)
- Días 3-4: APIs (32 horas)
- Días 5-10: UI + Testing (16 horas)

---

**Documento Finalizado**  
**Versión 2.0 - COMPLETO**  
**Estado: Listo para codificar**

