<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Validator.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    if (!Auth::check()) {
        http_response_code(401);
        throw new Exception("No autorizado");
    }

    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        throw new Exception("Token CSRF inválido");
    }

    $id = (int)($_POST['id'] ?? 0);
    if (!$id) {
        throw new Exception("Falta el ID de la imagen");
    }

    $alt_text = Validator::sanitizeString($_POST['alt_text'] ?? '');
    $etiquetas = Validator::sanitizeString($_POST['etiquetas'] ?? '');
    
    // SEO
    $seo_titulo = Validator::sanitizeString($_POST['seo_titulo'] ?? '');
    $seo_descripcion = Validator::sanitizeString($_POST['seo_descripcion'] ?? '');
    $seo_palabras_clave = Validator::sanitizeString($_POST['seo_palabras_clave'] ?? '');
    $schema_json = $_POST['schema_json'] ?? ''; // Sin sanear para permitir comillas, validar JSON
    
    if($schema_json && !json_decode($schema_json)) {
        throw new Exception("El Schema JSON es inválido.");
    }
    
    // GEO
    $geo_latitud = $_POST['geo_latitud'] !== '' ? (float)$_POST['geo_latitud'] : null;
    $geo_longitud = $_POST['geo_longitud'] !== '' ? (float)$_POST['geo_longitud'] : null;
    $geo_region = Validator::sanitizeString($_POST['geo_region'] ?? '');

    $db = Database::getInstance();
    
    $query = "UPDATE imagenes SET 
        alt_text = ?, 
        etiquetas = ?, 
        seo_titulo = ?, 
        seo_descripcion = ?, 
        seo_palabras_clave = ?, 
        schema_json = ?, 
        geo_latitud = ?, 
        geo_longitud = ?, 
        geo_region = ?
        WHERE id = ?";
        
    $stmt = $db->prepare($query);
    $stmt->execute([
        $alt_text,
        $etiquetas,
        $seo_titulo,
        $seo_descripcion,
        $seo_palabras_clave,
        $schema_json,
        $geo_latitud,
        $geo_longitud,
        $geo_region,
        $id
    ]);

    echo json_encode(['exito' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
