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
    if (!Auth::check() || !in_array($_SESSION['user_rol'], ['super_admin', 'admin'])) {
        http_response_code(401);
        die(json_encode(['exito' => false, 'error' => 'No autorizado']));
    }

    $db = Database::getInstance();
    $method = $_SERVER['REQUEST_METHOD'];

    // ---- GET: Listar o Obtener ----
    if ($method === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
        
        if ($id) {
            $stmt = $db->prepare("SELECT * FROM formularios WHERE id = ?");
            $stmt->execute([$id]);
            $form = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$form) throw new Exception("Formulario no encontrado");
            
            $stmt = $db->prepare("SELECT * FROM formulario_campos WHERE formulario_id = ? ORDER BY orden ASC");
            $stmt->execute([$id]);
            $form['campos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['exito' => true, 'formulario' => $form]);
        } else {
            $stmt = $db->query("SELECT * FROM formularios ORDER BY id DESC");
            $formularios = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['exito' => true, 'formularios' => $formularios]);
        }
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!CSRF::validate($input['csrf_token'] ?? '')) {
        http_response_code(403);
        die(json_encode(['exito' => false, 'error' => 'Token CSRF inválido']));
    }

    // ---- POST / PUT: Crear o Actualizar ----
    if ($method === 'POST') {
        $id = isset($input['id']) ? (int)$input['id'] : null;
        $nombre = Validator::sanitizeString($input['nombre'] ?? '');
        $slug = Helpers::slugify($nombre);
        $tipo = $input['tipo'] ?? 'custom';
        $email = $input['email_notificacion'] ?? '';
        $redir = $input['redirigir_a'] ?? '';
        $msg = $input['mensaje_exito'] ?? '';
        $activo = isset($input['activo']) ? (int)$input['activo'] : 1;

        if (!$nombre) throw new Exception("El nombre es obligatorio");

        $db->beginTransaction();

        if ($id) {
            $stmt = $db->prepare("UPDATE formularios SET nombre=?, tipo=?, activo=?, email_notificacion=?, redirigir_a=?, mensaje_exito=? WHERE id=?");
            $stmt->execute([$nombre, $tipo, $activo, $email, $redir, $msg, $id]);
        } else {
            $stmt = $db->prepare("INSERT INTO formularios (nombre, slug, tipo, activo, email_notificacion, redirigir_a, mensaje_exito) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $slug, $tipo, $activo, $email, $redir, $msg]);
            $id = $db->lastInsertId();
        }

        // Manejar Campos
        if (isset($input['campos'])) {
            // Eliminar campos antiguos
            $db->prepare("DELETE FROM formulario_campos WHERE formulario_id = ?")->execute([$id]);
            
            $stmt = $db->prepare("INSERT INTO formulario_campos (formulario_id, nombre_campo, tipo_campo, label, label_en, placeholder, requerido, opciones, orden) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($input['campos'] as $index => $campo) {
                $opciones = !empty($campo['opciones']) ? json_encode($campo['opciones']) : null;
                $stmt->execute([
                    $id,
                    Helpers::slugify($campo['label'] ?? 'campo'),
                    $campo['tipo_campo'] ?? 'texto',
                    $campo['label'] ?? '',
                    $campo['label_en'] ?? '',
                    $campo['placeholder'] ?? '',
                    !empty($campo['requerido']) ? 1 : 0,
                    $opciones,
                    $index
                ]);
            }
        }

        $db->commit();
        echo json_encode(['exito' => true, 'id' => $id]);
        exit;
    }

    // ---- DELETE ----
    if ($method === 'DELETE' || (isset($input['_method']) && $input['_method'] === 'DELETE')) {
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if (!$id) throw new Exception("ID inválido");
        
        $db->prepare("DELETE FROM formularios WHERE id = ?")->execute([$id]);
        echo json_encode(['exito' => true]);
        exit;
    }

} catch (Exception $e) {
    if (isset($db) && $db->inTransaction()) $db->rollBack();
    http_response_code(500);
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}
