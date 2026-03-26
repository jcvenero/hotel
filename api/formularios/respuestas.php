<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';

header('Content-Type: application/json');

try {
    if (!Auth::check() || !in_array($_SESSION['user_rol'], ['super_admin', 'admin', 'recepcionista'])) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }

    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    // ---- GET: Listar respuestas ----
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if ($id) {
            $stmt = $db->prepare("SELECT r.*, f.nombre AS formulario_nombre 
                                FROM formulario_respuestas r 
                                JOIN formularios f ON r.formulario_id = f.id 
                                WHERE r.id = ?");
            $stmt->execute([$id]);
            $respuesta = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($respuesta) {
                $respuesta['datos'] = json_decode($respuesta['datos'], true);
                // Marcar como leída automáticamente al ver detalle
                if (!$respuesta['leida']) {
                    $db->prepare("UPDATE formulario_respuestas SET leida = 1 WHERE id = ?")->execute([$id]);
                }
            }
            
            echo json_encode(['exito' => true, 'respuesta' => $respuesta]);
        } else {
            $form_id = isset($_GET['formulario_id']) ? (int)$_GET['formulario_id'] : null;
            $where = $form_id ? "WHERE r.formulario_id = $form_id" : "";
            
            $stmt = $db->query("SELECT r.*, f.nombre AS formulario_nombre 
                                FROM formulario_respuestas r 
                                JOIN formularios f ON r.formulario_id = f.id 
                                $where 
                                ORDER BY r.fecha_creacion DESC");
            $respuestas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($respuestas as &$r) {
                $r['datos'] = json_decode($r['datos'], true);
            }
            
            echo json_encode(['exito' => true, 'respuestas' => $respuestas]);
        }
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!CSRF::validate($input['csrf_token'] ?? '')) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }

    // ---- POST: Responder o marcar ----
    if ($method === 'POST') {
        $id = (int)($input['id'] ?? 0);
        $accion = $input['accion'] ?? '';

        if (!$id) throw new Exception("ID inválido");

        if ($accion === 'responder') {
            $mensaje = $input['mensaje'] ?? '';
            $stmt = $db->prepare("UPDATE formulario_respuestas SET respondida = 1, respuesta_admin = ?, respondida_por = ?, fecha_respuesta = NOW() WHERE id = ?");
            $stmt->execute([$mensaje, $_SESSION['user_id'], $id]);
        } elseif ($accion === 'toggle_leida') {
            $stmt = $db->prepare("UPDATE formulario_respuestas SET leida = NOT leida WHERE id = ?");
            $stmt->execute([$id]);
        }

        echo json_encode(['exito' => true]);
        exit;
    }

    // ---- DELETE ----
    if ($method === 'DELETE' || (isset($input['_method']) && $input['_method'] === 'DELETE')) {
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        $db->prepare("DELETE FROM formulario_respuestas WHERE id = ?")->execute([$id]);
        echo json_encode(['exito' => true]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}
