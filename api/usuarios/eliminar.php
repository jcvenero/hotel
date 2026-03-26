<?php
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Helpers.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Logger.php';

Auth::requireRole(['super_admin', 'admin']);

$data = json_decode(file_get_contents('php://input'), true);

if (!CSRF::validate(apache_request_headers()['X-CSRF-Token'] ?? '')) {
    Helpers::jsonResponse(false, 'Token CSRF inválido', 403);
}

try {
    $id = (int)($data['id'] ?? 0);
    
    if (!$id) {
        Helpers::jsonResponse(false, 'ID inválido', 400);
    }
    
    if ($id == $_SESSION['user_id']) {
        Helpers::jsonResponse(false, 'No puedes eliminarte a ti mismo', 403);
    }

    $db = Database::getInstance();
    $stmt = $db->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);

    Logger::audit($_SESSION['user_id'], 'DELETE', 'usuarios', $id, "Usuario eliminado");
    Helpers::jsonResponse(true);

} catch (Exception $e) {
    Helpers::jsonResponse(false, 'Error interno o violación de llave foránea: ' . $e->getMessage(), 500);
}
?>
