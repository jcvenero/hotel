<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Validator.php';
require_once __DIR__ . '/../../includes/Helpers.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    $form_id = (int)($_POST['formulario_id'] ?? 0);
    if (!$form_id) throw new Exception("Formulario no especificado");

    $db = Database::getInstance();
    
    // 1. Verificar si formulario existe y está activo
    $stmt = $db->prepare("SELECT * FROM formularios WHERE id = ? AND activo = 1");
    $stmt->execute([$form_id]);
    $form = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$form) throw new Exception("El formulario no está disponible");

    // 2. Obtener campos requeridos para validación
    $stmt = $db->prepare("SELECT nombre_campo, label, requerido FROM formulario_campos WHERE formulario_id = ?");
    $stmt->execute([$form_id]);
    $campos_config = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $datos_usuario = [];
    foreach ($campos_config as $config) {
        $val = $_POST[$config['nombre_campo']] ?? '';
        if ($config['requerido'] && empty($val)) {
            throw new Exception("El campo '{$config['label']}' es obligatorio");
        }
        $datos_usuario[$config['nombre_campo']] = Validator::sanitizeString($val);
    }

    // 3. Capturar Contexto
    $pagina_origen = Validator::sanitizeString($_POST['pagina_origen'] ?? $_SERVER['HTTP_REFERER'] ?? '');
    $tipo_entidad = Validator::sanitizeString($_POST['tipo_entidad'] ?? '');
    $entidad_id = (int)($_POST['entidad_id'] ?? 0);
    $idioma = Validator::sanitizeString($_POST['idioma'] ?? 'es');
    $ip = Helpers::getIP();
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';

    // 4. Guardar Respuesta
    $stmt = $db->prepare("INSERT INTO formulario_respuestas 
        (formulario_id, pagina_origen, tipo_entidad_origen, entidad_origen_id, idioma_origen, datos, ip_cliente, user_agent) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $form_id,
        $pagina_origen,
        $tipo_entidad,
        $entidad_id,
        $idioma,
        json_encode($datos_usuario),
        $ip,
        $ua
    ]);

    // 5. Enviar Notificación por Email (Simulado o real si hay Mailer configurado)
    // if($form['email_notificacion']) { ... }

    echo json_encode([
        'exito' => true,
        'mensaje' => $form['mensaje_exito'] ?: 'Mensaje enviado correctamente',
        'redirect' => $form['redirigir_a'] ?: null
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}
