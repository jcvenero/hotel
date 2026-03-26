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
    if (!Auth::check()) {
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
    $tipos_validos = ['habitacion', 'hotel', 'amenidades', 'vista', 'general', 'logo'];
    
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
    $db = Database::getInstance();
    $db->beginTransaction();
    
    try {
        $datos = $resultado['datos'];
        $datos['subida_por'] = $_SESSION['user_id'] ?? null;
        
        $campos = implode(', ', array_keys($datos));
        $placeholders = implode(', ', array_fill(0, count($datos), '?'));
        $stmt = $db->prepare("INSERT INTO imagenes ($campos) VALUES ($placeholders)");
        $stmt->execute(array_values($datos));
        
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
        'error' => 'Error crítico de servidor: ' . $e->getMessage()
    ]));
}

