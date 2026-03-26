<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Validator.php';

header('Content-Type: application/json');

try {
    if (!Auth::check()) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }

    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    // ---- GET ----
    if ($method === 'GET') {
        $stmt = $db->query("SELECT * FROM temporadas_globales ORDER BY tipo_temporada ASC, fecha_inicio ASC");
        $temporadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['exito' => true, 'temporadas' => $temporadas]);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    if (!CSRF::validate($input['csrf_token'] ?? '')) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }

    // ---- POST: Crear ----
    if ($method === 'POST' && empty($input['_method'])) {
        $tipo = $input['tipo_temporada'] ?? '';
        $inicio = $input['fecha_inicio'] ?? '';
        $fin = $input['fecha_fin'] ?? '';
        $desc = Validator::sanitizeString($input['descripcion'] ?? '');

        if (!in_array($tipo, ['baja', 'regular', 'alta'])) {
            die(json_encode(['exito' => false, 'error' => 'Tipo de temporada inválido']));
        }
        if (!$inicio || !$fin) {
            die(json_encode(['exito' => false, 'error' => 'Fechas requeridas']));
        }

        $stmt = $db->prepare("INSERT INTO temporadas_globales (tipo_temporada, fecha_inicio, fecha_fin, descripcion) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tipo, $inicio, $fin, $desc]);
        echo json_encode(['exito' => true, 'id' => $db->lastInsertId()]);
        exit;
    }

    // ---- PUT ----
    if ($method === 'POST' && ($input['_method'] ?? '') === 'PUT') {
        $id = (int)($input['id'] ?? 0);
        $tipo = $input['tipo_temporada'] ?? '';
        $inicio = $input['fecha_inicio'] ?? '';
        $fin = $input['fecha_fin'] ?? '';
        $desc = Validator::sanitizeString($input['descripcion'] ?? '');

        if (!$id) die(json_encode(['exito' => false, 'error' => 'ID requerido']));

        $stmt = $db->prepare("UPDATE temporadas_globales SET tipo_temporada=?, fecha_inicio=?, fecha_fin=?, descripcion=? WHERE id=?");
        $stmt->execute([$tipo, $inicio, $fin, $desc, $id]);
        echo json_encode(['exito' => true]);
        exit;
    }

    // ---- DELETE ----
    if ($method === 'POST' && ($input['_method'] ?? '') === 'DELETE') {
        $id = (int)($input['id'] ?? 0);
        if (!$id) die(json_encode(['exito' => false, 'error' => 'ID requerido']));
        $db->prepare("DELETE FROM temporadas_globales WHERE id = ?")->execute([$id]);
        echo json_encode(['exito' => true]);
        exit;
    }

    die(json_encode(['exito' => false, 'error' => 'Método no soportado']));

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}
