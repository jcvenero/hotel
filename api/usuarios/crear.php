<?php
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Helpers.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Validator.php';
require_once __DIR__ . '/../../includes/Logger.php';

Auth::requireRole(['super_admin', 'admin']);

// Leer raw JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Prevenir CSRF
if (!CSRF::validate(apache_request_headers()['X-CSRF-Token'] ?? '')) {
    Helpers::jsonResponse(false, 'Token CSRF inválido', 403);
}

try {
    $email = Validator::sanitizeEmail($data['email'] ?? '');
    $nombre = Validator::sanitizeString($data['nombre_completo'] ?? '');
    $rol = Validator::sanitizeString($data['rol'] ?? 'editor');
    $password = $data['password'] ?? '';
    
    if (empty($email) || empty($nombre) || empty($password)) {
        Helpers::jsonResponse(false, 'Todos los campos básicos son requeridos.', 400);
    }
    if (!Validator::email($email)) {
        Helpers::jsonResponse(false, 'Email inválido', 400);
    }
    
    // Validar super admin
    if ($rol === 'super_admin' && $_SESSION['user_rol'] !== 'super_admin') {
        Helpers::jsonResponse(false, 'No tienes permisos para crear Super Admins.', 403);
    }

    $db = Database::getInstance();
    
    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn()) {
        Helpers::jsonResponse(false, 'El email ya está en uso', 400);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $activo = isset($data['activo']) ? (int)$data['activo'] : 1;

    $stmt = $db->prepare("INSERT INTO usuarios (email, password_hash, nombre_completo, rol, activo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$email, $hash, $nombre, $rol, $activo]);
    $newId = $db->lastInsertId();

    Logger::audit($_SESSION['user_id'], 'CREATE', 'usuarios', $newId, "Usuario creado: $email");
    Helpers::jsonResponse(true, ['id' => $newId]);

} catch (Exception $e) {
    Helpers::jsonResponse(false, 'Error interno: ' . $e->getMessage(), 500);
}
?>
