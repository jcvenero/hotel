<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';

header('Content-Type: application/json');

try {
    if (!Auth::check()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }

    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    // ---- GET: Obtener tarifas de un tipo ----
    if ($method === 'GET') {
        $tipo_id = (int)($_GET['tipo_habitacion_id'] ?? 0);
        if ($tipo_id) {
            $stmt = $db->prepare("SELECT * FROM tipo_habitacion_tarifas WHERE tipo_habitacion_id = ?");
            $stmt->execute([$tipo_id]);
            $tarifa = $stmt->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['exito' => true, 'tarifa' => $tarifa ?: null]);
        } else {
            // Todas las tarifas con nombre del tipo
            $stmt = $db->query("SELECT t.*, th.nombre AS tipo_nombre FROM tipo_habitacion_tarifas t JOIN tipos_habitacion th ON t.tipo_habitacion_id = th.id ORDER BY th.nombre");
            echo json_encode(['exito' => true, 'tarifas' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        }
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    if (!CSRF::validate($input['csrf_token'] ?? '')) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }

    // ---- POST: Guardar/Actualizar tarifas de un tipo ----
    if ($method === 'POST') {
        $tipo_id = (int)($input['tipo_habitacion_id'] ?? 0);
        if (!$tipo_id) die(json_encode(['exito' => false, 'error' => 'Tipo de habitación requerido']));

        $precio_baja = (float)($input['precio_baja'] ?? 0);
        $precio_regular = (float)($input['precio_regular'] ?? 0);
        $precio_alta = (float)($input['precio_alta'] ?? 0);
        $precio_manual = (float)($input['precio_manual'] ?? 0);
        $manual_activa = !empty($input['tarifa_manual_activa']) ? 1 : 0;

        // UPSERT (INSERT … ON DUPLICATE KEY UPDATE)
        $stmt = $db->prepare("INSERT INTO tipo_habitacion_tarifas 
            (tipo_habitacion_id, precio_baja, precio_regular, precio_alta, precio_manual, tarifa_manual_activa) 
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                precio_baja = VALUES(precio_baja),
                precio_regular = VALUES(precio_regular),
                precio_alta = VALUES(precio_alta),
                precio_manual = VALUES(precio_manual),
                tarifa_manual_activa = VALUES(tarifa_manual_activa)");
        $stmt->execute([$tipo_id, $precio_baja, $precio_regular, $precio_alta, $precio_manual, $manual_activa]);

        echo json_encode(['exito' => true, 'mensaje' => 'Tarifas actualizadas']);
        exit;
    }

    die(json_encode(['exito' => false, 'error' => 'Método no soportado']));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}
