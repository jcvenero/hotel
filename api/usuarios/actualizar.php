<?php
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Helpers.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Validator.php';
require_once __DIR__ . '/../../includes/Logger.php';

Auth::requireRole(['super_admin', 'admin']);

$data = json_decode(file_get_contents('php://input'), true);

if (!CSRF::validate(apache_request_headers()['X-CSRF-Token'] ?? '')) {
    Helpers::jsonResponse(false, 'Token CSRF inválido', 403);
}

try {
    $id = (int)($data['id'] ?? 0);
    $email = Validator::sanitizeEmail($data['email'] ?? '');
    $nombre = Validator::sanitizeString($data['nombre_completo'] ?? '');
    $rol = Validator::sanitizeString($data['rol'] ?? 'editor');
    $activo = isset($data['activo']) ? (int)$data['activo'] : 1;
    $password = trim($data['password'] ?? '');

    if (!$id || empty($email) || empty($nombre)) {
        Helpers::jsonResponse(false, 'Datos incompletos', 400);
    }

    if ($rol === 'super_admin' && $_SESSION['user_rol'] !== 'super_admin') {
        Helpers::jsonResponse(false, 'No puedes asignar rol Super Admin', 403);
    }

    $db = Database::getInstance();
    
    // Check if email belongs to someone else
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $id]);
    if ($stmt->fetchColumn()) {
        Helpers::jsonResponse(false, 'El correo electrónico ya está en uso', 400);
    }

    // Protection to not downgrade self from super admin
    if ($id == $_SESSION['user_id'] && $_SESSION['user_rol'] === 'super_admin' && $rol !== 'super_admin') {
        Helpers::jsonResponse(false, 'No te puedes quitar a ti mismo el rol de Super Admin', 403);
    }

    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $db->prepare("UPDATE usuarios SET email=?, nombre_completo=?, rol=?, activo=?, password_hash=? WHERE id=?");
        $stmt->execute([$email, $nombre, $rol, $activo, $hash, $id]);
    } else {
        $stmt = $db->prepare("UPDATE usuarios SET email=?, nombre_completo=?, rol=?, activo=? WHERE id=?");
        $stmt->execute([$email, $nombre, $rol, $activo, $id]);
    }

    Logger::audit($_SESSION['user_id'], 'UPDATE', 'usuarios', $id, "Usuario actualizado: $email");
    Helpers::jsonResponse(true);

} catch (Exception $e) {
    Helpers::jsonResponse(false, 'Error interno: ' . $e->getMessage(), 500);
}
?>
