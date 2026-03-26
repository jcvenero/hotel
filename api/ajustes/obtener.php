<?php
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Helpers.php';

Auth::requireRole(['super_admin', 'admin']);

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT clave, valor, tipo FROM sitio_ajustes");
    
    $ajustes = [];
    while ($row = $stmt->fetch()) {
        $ajustes[$row['clave']] = ($row['tipo'] === 'json') ? json_decode($row['valor'], true) : $row['valor'];
    }
    
    // Por si es la primera vez y la DB está vacía, enviamos un array asegurando formato
    Helpers::jsonResponse(true, $ajustes);

} catch (Exception $e) {
    Helpers::jsonResponse(false, 'Error de lectura de ajustes: ' . $e->getMessage(), 500);
}
?>
