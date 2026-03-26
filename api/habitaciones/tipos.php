<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Validator.php';
require_once __DIR__ . '/../../includes/Helpers.php';

header('Content-Type: application/json');

try {
    if (!Auth::check()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }

    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    // ---- GET: Listar todos ----
    if ($method === 'GET') {
        $stmt = $db->query("SELECT * FROM tipos_habitacion ORDER BY nombre ASC");
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['exito' => true, 'tipos' => $tipos]);
        exit;
    }

    // Validar CSRF para escritura
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    if (!CSRF::validate($input['csrf_token'] ?? '')) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }

    // ---- POST: Crear nuevo ----
    if ($method === 'POST' && empty($input['_method'])) {
        $nombre = Validator::sanitizeString($input['nombre'] ?? '');
        $descripcion = Validator::sanitizeString($input['descripcion'] ?? '');
        if (!$nombre) {
            die(json_encode(['exito' => false, 'error' => 'El nombre es obligatorio']));
        }
        $slug = Helpers::slugify($nombre);

        $stmt = $db->prepare("INSERT INTO tipos_habitacion (nombre, slug, descripcion, activo) VALUES (?, ?, ?, 1)");
        $stmt->execute([$nombre, $slug, $descripcion]);
        $id = $db->lastInsertId();

        // Crear registro de tarifas vacío para este tipo
        $stmt2 = $db->prepare("INSERT INTO tipo_habitacion_tarifas (tipo_habitacion_id) VALUES (?)");
        $stmt2->execute([$id]);

        echo json_encode(['exito' => true, 'id' => $id, 'mensaje' => 'Tipo creado']);
        exit;
    }

    // ---- PUT/PATCH: Actualizar ----
    if ($method === 'POST' && ($input['_method'] ?? '') === 'PUT') {
        $id = (int)($input['id'] ?? 0);
        $nombre = Validator::sanitizeString($input['nombre'] ?? '');
        $descripcion = Validator::sanitizeString($input['descripcion'] ?? '');
        $activo = isset($input['activo']) ? (int)$input['activo'] : 1;
        if (!$id || !$nombre) {
            die(json_encode(['exito' => false, 'error' => 'ID y nombre requeridos']));
        }
        $slug = Helpers::slugify($nombre);
        $stmt = $db->prepare("UPDATE tipos_habitacion SET nombre=?, slug=?, descripcion=?, activo=? WHERE id=?");
        $stmt->execute([$nombre, $slug, $descripcion, $activo, $id]);
        echo json_encode(['exito' => true, 'mensaje' => 'Tipo actualizado']);
        exit;
    }

    // ---- DELETE ----
    if ($method === 'POST' && ($input['_method'] ?? '') === 'DELETE') {
        $id = (int)($input['id'] ?? 0);
        if (!$id) die(json_encode(['exito' => false, 'error' => 'ID requerido']));

        // Verificar que no haya habitaciones usando este tipo
        $stmt = $db->prepare("SELECT COUNT(*) FROM habitaciones WHERE tipo_habitacion_id = ?");
        $stmt->execute([$id]);
        if ($stmt->fetchColumn() > 0) {
            die(json_encode(['exito' => false, 'error' => 'No se puede eliminar: hay habitaciones con este tipo asignado']));
        }

        $db->prepare("DELETE FROM tipo_habitacion_tarifas WHERE tipo_habitacion_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM tipos_habitacion WHERE id = ?")->execute([$id]);
        echo json_encode(['exito' => true, 'mensaje' => 'Tipo eliminado']);
        exit;
    }

    die(json_encode(['exito' => false, 'error' => 'Método no soportado']));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}
