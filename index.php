<?php
/**
 * Enrutador Frontal Principal (Frontend Público)
 */
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Language.php';
require_once __DIR__ . '/includes/Helpers.php';
require_once __DIR__ . '/includes/FormBuilder.php';

// Inicializar idioma
Language::init();
$lang = Language::current();

// Definir Tema Activo
define('THEME_NAME', 'villas-del-sol');
define('THEME_PATH', __DIR__ . '/themes/' . THEME_NAME);
define('THEME_URL', '/hotel/themes/' . THEME_NAME);

// Obtener Ajustes Globales para todo el sitio
$db = Database::getInstance();
$stmt = $db->query("SELECT clave, valor FROM sitio_ajustes");
$ajustes = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $ajustes[$row['clave']] = $row['valor'];
}

// Enrutamiento Simple
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/hotel', '', $uri); // Adaptación para XAMPP
$path = trim($path, '/');

// Decidir qué archivo del tema cargar
if ($path === '' || $path === 'inicio') {
    $file = 'inicio.php';
} elseif ($path === 'nosotros') {
    $file = 'nosotros.php';
} elseif ($path === 'habitaciones') {
    $file = 'habitaciones.php';
} elseif ($path === 'contacto') {
    $file = 'contacto.php';
} elseif (preg_match('/^habitacion\/([a-zA-Z0-9-]+)$/', $path, $matches)) {
    $_GET['slug'] = $matches[1];
    $file = 'habitacion-detalle.php';
} else {
    $file = '404.php';
}

// Cargar el archivo del tema si existe, sino 404
$target = THEME_PATH . '/' . $file;
if (file_exists($target)) {
    require_once $target;
} else {
    http_response_code(404);
    if (file_exists(THEME_PATH . '/404.php')) {
        require_once THEME_PATH . '/404.php';
    } else {
        echo "<h1>404 Not Found</h1><p>Theme file missing: $file</p>";
    }
}
