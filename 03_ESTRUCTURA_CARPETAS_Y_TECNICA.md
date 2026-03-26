# ESTRUCTURA DE CARPETAS Y GUÍA TÉCNICA FINAL

## ESTRUCTURA COMPLETA DEL PROYECTO

```
proyecto-hotel/
│
├── config/                          # Configuración
│   ├── database.php                 # Credenciales y constantes BD
│   ├── settings.php                 # Variables globales
│   └── constants.php                # Constantes de la app
│
├── includes/                        # Clases principales (núcleo)
│   ├── Database.php                 # PDO wrapper - conexión a BD
│   ├── Auth.php                     # Autenticación y roles
│   ├── CSRF.php                     # Protección CSRF
│   ├── Validator.php                # Validación de inputs
│   ├── Escaper.php                  # Escape de output (XSS)
│   ├── Security.php                 # Rate limiting, headers, etc.
│   ├── Language.php                 # Sistema multiidioma
│   ├── ImageHandler.php             # Upload y procesamiento de imágenes
│   ├── SEO.php                      # Generación de meta tags
│   ├── SchemaGenerator.php          # Generación de Schema.org
│   ├── SEOAnalyzer.php              # Análisis de contenido SEO
│   ├── Logger.php                   # Logging de errores y auditoría
│   ├── Mailer.php                   # Envío de emails (PHPMailer)
│   ├── FormularioRenderer.php       # Renderización dinámica de formularios
│   └── Helpers.php                  # Funciones auxiliares (slugify, etc.)
│
├── api/                             # Endpoints API (REST)
│   ├── auth/
│   │   ├── login.php                # POST: login
│   │   └── logout.php               # POST: logout
│   │
│   ├── usuarios/
│   │   ├── crear.php                # POST: crear usuario
│   │   ├── actualizar.php           # POST: actualizar usuario
│   │   ├── eliminar.php             # POST: eliminar usuario
│   │   └── obtener.php              # GET: obtener datos usuario
│   │
│   ├── habitaciones/
│   │   ├── crear.php                # POST: crear habitación
│   │   ├── actualizar.php           # POST: actualizar habitación
│   │   ├── eliminar.php             # POST: eliminar habitación
│   │   ├── obtener.php              # GET: obtener habitación
│   │   ├── tarifas/
│   │   │   ├── crear.php
│   │   │   ├── actualizar.php
│   │   │   └── eliminar.php
│   │   └── comodidades/
│   │       └── actualizar.php
│   │
│   ├── galeria/
│   │   ├── subir.php                # POST: subir imagen
│   │   ├── obtener-imagenes.php     # GET: listar imágenes
│   │   └── eliminar.php             # POST: eliminar imagen
│   │
│   ├── paginas/
│   │   ├── crear.php
│   │   ├── actualizar.php
│   │   ├── actualizar-vivo.php      # POST: edición en vivo
│   │   ├── eliminar.php
│   │   └── obtener.php
│   │
│   ├── formularios/
│   │   ├── crear.php
│   │   ├── actualizar.php
│   │   ├── eliminar.php
│   │   ├── procesar.php             # POST: procesar envío de formulario
│   │   └── respuestas/
│   │       ├── obtener.php
│   │       ├── marcar-leida.php
│   │       └── responder.php
│   │
│   ├── blog/
│   │   ├── crear.php
│   │   ├── actualizar.php
│   │   ├── eliminar.php
│   │   └── obtener.php
│   │
│   ├── resenas/
│   │   ├── crear.php
│   │   ├── actualizar.php
│   │   ├── eliminar.php
│   │   ├── obtener.php
│   │   └── obtener-promedio.php
│   │
│   ├── promociones/
│   │   ├── crear.php
│   │   ├── actualizar.php
│   │   ├── eliminar.php
│   │   ├── obtener.php
│   │   └── obtener-activas.php
│   │
│   ├── ajustes/
│   │   └── actualizar.php
│   │
│   └── faqs/
│       ├── crear.php
│       ├── actualizar.php
│       └── eliminar.php
│
├── admin/                           # Panel administrativo
│   ├── dashboard.php                # Dashboard principal
│   ├── login.php                    # Login
│   ├── logout.php                   # Logout
│   ├── usuarios.php                 # Gestión de usuarios
│   ├── habitaciones.php             # Listado de habitaciones
│   ├── habitaciones/
│   │   └── editar.php               # Formulario de edición
│   ├── galeria.php                  # Gestor de galería
│   ├── paginas.php                  # Listado de páginas
│   ├── paginas/
│   │   └── editar.php               # Formulario de edición
│   ├── blog.php                     # Listado de artículos
│   ├── blog/
│   │   └── editar.php               # Formulario de edición
│   ├── formularios.php              # Listado de formularios
│   ├── formularios/
│   │   ├── editar.php               # Formulario de edición
│   │   └── respuestas.php           # Gestión de respuestas
│   ├── resenas.php                  # Gestión de reseñas
│   ├── resenas/
│   │   └── editar.php
│   ├── promociones.php              # Gestión de promociones
│   ├── promociones/
│   │   └── editar.php
│   ├── ajustes.php                  # Configuración del sitio
│   ├── faqs.php                     # FAQs del hotel
│   ├── habitacion-faqs/
│   │   └── editar.php               # FAQs por habitación
│   ├── assets/
│   │   ├── css/
│   │   │   ├── admin.css            # Estilos del admin
│   │   │   ├── components.css       # Componentes
│   │   │   └── responsive.css
│   │   └── js/
│   │       ├── admin.js             # JavaScript del admin
│   │       ├── repeater.js          # Lógica de repeaters
│   │       └── uploader.js          # Lógica de upload
│   │
│   └── includes/
│       └── header.php               # Header del admin (nav, css)
│
├── themes/                          # Temas del frontend
│   └── default/                     # Tema por defecto
│       ├── assets/
│       │   ├── css/
│       │   │   ├── style.css        # Estilos principales
│       │   │   ├── responsive.css
│       │   │   └── editor-vivo.css  # Estilos de edición en vivo
│       │   └── js/
│       │       ├── main.js          # JavaScript principal
│       │       └── editor-vivo.js   # Edición en vivo
│       │
│       ├── partials/
│       │   ├── header.php           # Header (navbar)
│       │   ├── footer.php           # Footer
│       │   ├── formulario-reserva.php
│       │   ├── galeria-imagenes.php
│       │   └── resenas.php
│       │
│       ├── inicio.php               # Página de inicio
│       ├── nosotros.php             # Página nosotros (custom cliente)
│       ├── habitaciones.php         # Listado de habitaciones
│       ├── habitacion-single.php    # Detalle de habitación
│       ├── blog.php                 # Listado de blog
│       ├── blog-single.php          # Detalle de artículo
│       ├── contacto.php             # Página de contacto
│       ├── legal.php                # Páginas legales (generador)
│       └── 404.php                  # Página 404
│
├── public/                          # Carpeta pública (DocumentRoot)
│   ├── index.php                    # Enrutador principal
│   ├── .htaccess                    # Rewrite rules
│   ├── sitemap.xml                  # Sitemap dinámico
│   ├── robots.txt                   # Robots.txt
│   │
│   └── uploads/                     # Imágenes subidas
│       ├── originals/               # Imágenes originales en WebP
│       ├── webp/                    # Versiones WebP optimizadas
│       ├── thumbnails/              # Thumbnails (300x200)
│       ├── tiny/                    # Tiny (150x100)
│       └── .htaccess                # Denegar ejecución de archivos
│
├── languages/                       # Archivos de idioma
│   ├── es.php                       # Strings en español
│   └── en.php                       # Strings en inglés
│
├── logs/                            # Registros de la aplicación
│   ├── errors.log                   # Errores
│   ├── security.log                 # Eventos de seguridad
│   ├── audit.log                    # Auditoría de cambios
│   └── .htaccess                    # Denegar acceso web
│
├── sql/                             # Scripts de base de datos
│   ├── schema.sql                   # Creación de tablas (FASE 1)
│   ├── data-inicial.sql             # Datos iniciales (usuario admin)
│   └── migraciones/                 # Migraciones futuras
│       ├── 001-inicial.sql
│       ├── 002-agregar-blog.sql
│       └── ...
│
├── vendor/                          # Composeer packages
│   ├── intervention/image           # Procesamiento de imágenes
│   ├── phpmailer/phpmailer          # Envío de emails
│   ├── monolog/monolog              # Logging
│   └── ...
│
├── .env.example                     # Plantilla de variables de entorno
├── .gitignore                       # Ignorar archivos en Git
├── .htaccess                        # Configuración Apache general
├── composer.json                    # Dependencias de Composer
├── composer.lock                    # Lock de dependencias
├── README.md                        # Documentación del proyecto
└── INSTALL.md                       # Guía de instalación
```

---

## ESTRUCTURA DE ARCHIVOS CRÍTICOS

### 1. config/database.php
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'hotel_db');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_URL', 'http://localhost/proyecto-hotel');
define('ADMIN_URL', APP_URL . '/admin');
define('API_URL', APP_URL . '/api');

define('UPLOAD_DIR', __DIR__ . '/../public/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

define('IDIOMAS_DISPONIBLES', ['es', 'en']);
define('IDIOMA_DEFECTO', 'es');
?>
```

### 2. public/index.php (Enrutador)
```php
<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Language.php';
require_once __DIR__ . '/../includes/Database.php';

// Detectar idioma y ruta
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

$idioma = in_array($segments[0], IDIOMAS_DISPONIBLES) ? $segments[0] : IDIOMA_DEFECTO;
$ruta = implode('/', array_slice($segments, 1));

// Instanciar language
$language = new Language($idioma);

// Enrutador según ruta
switch ($ruta) {
    case '':
    case '/':
        include __DIR__ . '/../themes/default/inicio.php';
        break;
    case 'habitaciones':
        include __DIR__ . '/../themes/default/habitaciones.php';
        break;
    case (strpos($ruta, 'habitacion/') === 0):
        $_GET['slug'] = substr($ruta, strlen('habitacion/'));
        include __DIR__ . '/../themes/default/habitacion-single.php';
        break;
    // ... más rutas
    default:
        header("HTTP/1.0 404 Not Found");
        include __DIR__ . '/../themes/default/404.php';
        break;
}
?>
```

### 3. public/.htaccess (Rewrite Rules)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /proyecto-hotel/
    
    # No reescribir archivos y carpetas reales
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Reescribir todas las solicitudes a index.php
    RewriteRule ^(.*)$ index.php?path=$1 [QSA,L]
</IfModule>

# Denegar acceso a archivos sensibles
<FilesMatch "\.env|\.git|\.htaccess">
    Order allow,deny
    Deny from all
</FilesMatch>

# Habilitar gzip
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript
</IfModule>

# Cache de navegador
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType text/css "access plus 1 week"
    ExpiresByType application/javascript "access plus 1 week"
</IfModule>
```

### 4. public/uploads/.htaccess (Seguridad)
```apache
# Denegar ejecución de PHP en uploads
<FilesMatch "\.php$">
    Order allow,deny
    Deny from all
</FilesMatch>

# Permitir solo imágenes
<FilesMatch "\.(jpg|jpeg|png|webp|gif)$">
    Order allow,deny
    Allow from all
</FilesMatch>
```

### 5. admin/login.php
```php
<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/CSRF.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar CSRF
    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        $error = 'Token CSRF inválido';
    } else {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $resultado = Auth::login($email, $password);
        
        if ($resultado['exito']) {
            header('Location: /proyecto-hotel/admin/dashboard.php');
            exit;
        } else {
            $error = $resultado['error'];
        }
    }
}

$csrf_token = CSRF::generateToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Admin Hotel</title>
    <link rel="stylesheet" href="/proyecto-hotel/admin/assets/css/admin.css">
</head>
<body>
    <div class="login-container">
        <form method="POST" class="login-form">
            <h1>Hotel Admin</h1>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="password" required>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </form>
    </div>
</body>
</html>
```

---

## SEGURIDAD - CHECKLIST DE IMPLEMENTACIÓN

### 1. SQL Injection Prevention ✅
```php
// NUNCA hacer esto:
$query = "SELECT * FROM usuarios WHERE email = '$email'";

// SIEMPRE hacer esto (prepared statements):
$stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
$stmt->execute([$email]);
```

### 2. XSS Prevention ✅
```php
// NUNCA hacer esto:
echo "<h1>" . $_POST['titulo'] . "</h1>";

// SIEMPRE hacer esto:
echo "<h1>" . htmlspecialchars($_POST['titulo']) . "</h1>";
```

### 3. CSRF Protection ✅
```html
<!-- En todo formulario: -->
<form method="POST">
    <?php echo CSRF::getField(); ?>
    <!-- resto del formulario -->
</form>

<!-- En AJAX: -->
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
fetch('/api/...', {
    method: 'POST',
    headers: {'X-CSRF-Token': csrfToken},
    body: JSON.stringify(data)
});
</script>
```

### 4. Password Hashing ✅
```php
// Al crear usuario:
$password_hash = password_hash($password, PASSWORD_BCRYPT);

// Al verificar:
if (password_verify($password_ingresada, $hash_almacenado)) {
    // Contraseña correcta
}
```

### 5. Input Validation ✅
```php
$email = $_POST['email'] ?? '';
if (!Validator::email($email)) {
    die('Email inválido');
}

$email = Validator::sanitizeEmail($email);
```

### 6. Rate Limiting ✅
```php
if (!Security::checkRateLimit($_SERVER['REMOTE_ADDR'], 5, 15)) {
    die('Demasiados intentos. Intenta más tarde.');
}
```

### 7. HTTPS en Producción ✅
```php
Security::forceHTTPS();
```

### 8. Security Headers ✅
```php
Security::setSecurityHeaders();
```

---

## DEPENDENCIAS COMPOSER

### composer.json
```json
{
    "require": {
        "intervention/image": "^3.0",
        "phpmailer/phpmailer": "^6.8",
        "monolog/monolog": "^3.0"
    },
    "autoload": {
        "psr-4": {
            "App\\": "includes/"
        }
    }
}
```

---

## VARIABLES DE ENTORNO (.env)

```env
# Base de datos
DB_HOST=localhost
DB_NAME=hotel_db
DB_USER=root
DB_PASS=

# Aplicación
APP_URL=http://localhost/proyecto-hotel
APP_ENV=development
APP_DEBUG=true

# Email (Sendgrid)
SENDGRID_API_KEY=SG.xxxxx

# Google
GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
RECAPTCHA_SITE_KEY=6Lc...
RECAPTCHA_SECRET_KEY=6Lc...

# Seguridad
SESSION_TIMEOUT=1800
MAX_UPLOAD_SIZE=5242880
```

---

## INSTALACIÓN Y SETUP

### 1. Clonar/Descargar Proyecto
```bash
git clone https://github.com/... proyecto-hotel
cd proyecto-hotel
```

### 2. Instalar Dependencias
```bash
composer install
```

### 3. Configurar .env
```bash
cp .env.example .env
# Editar .env con credenciales locales
```

### 4. Crear Base de Datos
```bash
# En MySQL
CREATE DATABASE hotel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
mysql -u root hotel_db < sql/schema.sql
mysql -u root hotel_db < sql/data-inicial.sql
```

### 5. Crear Carpetas de Uploads
```bash
mkdir -p public/uploads/{originals,webp,thumbnails,tiny}
chmod 755 public/uploads
```

### 6. Crear Archivo .htaccess
```bash
# Ya incluido en public/.htaccess
# Verificar que Apache tiene mod_rewrite habilitado
```

### 7. Acceder al Admin
```
http://localhost/proyecto-hotel/admin/login.php
Usuario: admin@hotel.com
Contraseña: admin123 (cambiar después)
```

---

## COMANDOS ÚTILES

### Backup de BD
```bash
mysqldump -u root -p hotel_db > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Restaurar BD
```bash
mysql -u root -p hotel_db < backup_20240315_123456.sql
```

### Limpiar logs
```bash
rm -f logs/*.log
```

### Limpiar caché
```bash
rm -rf public/uploads/cache/*
```

---

## ESTÁNDARES DE CÓDIGO

### Nombres de archivos
- PHP: `PascalCase.php` para clases, `lowercase.php` para includes
- CSS: `kebab-case.css`
- JS: `camelCase.js`

### Nombres de tablas
- `tabla_nombre` (minúsculas, guiones bajos)
- Plural: `usuarios`, `habitaciones`

### Nombres de columnas
- `columna_nombre` (minúsculas, guiones bajos)
- Foreign keys: `tabla_id` (singular)

### Convención de métodos
- Públicos: `metodoPublico()`
- Privados: `_metodoPrivado()` o `private function`

### Comentarios PHPDoc
```php
/**
 * Obtiene todas las habitaciones activas
 *
 * @param int $limite Máximo de resultados
 * @param string $orden Campo para ordenar
 * @return array Array de habitaciones
 */
public function getHabitacionesActivas($limite = 10, $orden = 'nombre') {
    // implementación
}
```

