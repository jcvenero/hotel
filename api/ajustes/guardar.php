<?php
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Helpers.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Logger.php';

Auth::requireRole(['super_admin', 'admin']);

$input = json_decode(file_get_contents('php://input'), true);

if (!CSRF::validate(apache_request_headers()['X-CSRF-Token'] ?? '')) {
    Helpers::jsonResponse(false, 'Token CSRF inválido', 403);
}

try {
    $db = Database::getInstance();
    $db->beginTransaction();

    $stmt = $db->prepare("INSERT INTO sitio_ajustes (clave, valor, tipo) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE valor = VALUES(valor), tipo = VALUES(tipo)");

    foreach ($input as $clave => $valor) {
        $tipo = 'texto';
        $valorAGuardar = $valor;

        if (is_array($valor)) {
            $tipo = 'json';
            $valorAGuardar = json_encode($valor, JSON_UNESCAPED_UNICODE);
        } else if (is_numeric($valor) && $clave != 'geo_latitud' && $clave != 'geo_longitud') {
            // Lat/Lng lo tratamos siempre texto para precisión, todo lo demás es duck-type numero
            $tipo = 'numero';
        } else if (is_bool($valor)) {
            $tipo = 'booleano';
            $valorAGuardar = $valor ? '1' : '0';
        }

        $stmt->execute([$clave, $valorAGuardar, $tipo]);
    }

    $db->commit();
    Logger::audit($_SESSION['user_id'], 'UPDATE', 'sitio_ajustes', 0, "Ajustes globales actualizados");
    
    Helpers::jsonResponse(true, 'Ajustes guardados correctamente');

} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    Helpers::jsonResponse(false, 'Fallo al guardar: ' . $e->getMessage(), 500);
}
?>
