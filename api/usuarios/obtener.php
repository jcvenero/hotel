<?php
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Helpers.php';

Auth::requireRole(['super_admin', 'admin']);

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT id, email, nombre_completo, rol, activo, ultima_sesion FROM usuarios ORDER BY nombre_completo ASC");
    $usuarios = $stmt->fetchAll();
    
    Helpers::jsonResponse(true, $usuarios);

} catch (Exception $e) {
    Helpers::jsonResponse(false, 'Error al obtener usuarios: ' . $e->getMessage(), 500);
}
?>
