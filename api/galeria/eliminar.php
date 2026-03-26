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
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed");
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $csrf = $input['csrf_token'] ?? '';
    $id = $input['id'] ?? 0;

    if (!CSRF::validate($csrf)) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }
    
    if (!Auth::check()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }

    // El admin podría eliminar, o solo el super_admin?
    // De momento autorizado.
    
    if (!$id) {
        die(json_encode(['exito' => false, 'error' => 'ID de imagen no proporcionado']));
    }

    $resultado = ImageHandler::eliminarImagen($id);
    
    if (!$resultado['exito']) {
        http_response_code(400);
        die(json_encode($resultado));
    }
    
    echo json_encode(['exito' => true, 'mensaje' => 'Imagen eliminada correctamente del servidor']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['exito' => false, 'error' => 'Error crítico: ' . $e->getMessage()]);
}
