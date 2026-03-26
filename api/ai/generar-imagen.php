<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/ImageHandler.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }

    if (!Auth::check()) {
        http_response_code(401);
        throw new Exception("No autorizado");
    }

    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        throw new Exception("Token CSRF inválido");
    }

    $promptOriginal = $_POST['prompt'] ?? '';
    if (empty(trim($promptOriginal))) {
        throw new Exception("El prompt no puede estar vacío");
    }
    
    // Potenciador mágico recomendado por el usuario para estética de Plaza Regocijo
    $prompt = $promptOriginal . ", High-end boutique hotel photography, Cusco colonial aesthetic, soft warm lighting, ultra-realistic 4k UHD, architectural photography quality.";

    // Obtener API KEY de base de datos (sitio_ajustes)
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT valor FROM sitio_ajustes WHERE clave = 'gemini_api_key'");
    $stmt->execute();
    $keyRow = $stmt->fetch();
    
    $apiKey = $keyRow['valor'] ?? '';
    if (!$apiKey) {
        throw new Exception("No hay API Key configurada en Ajustes (gemini_api_key)");
    }

    // Usamos gemini-2.5-flash-image (Free Tier) sugerido
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-image:generateContent?key=" . urlencode($apiKey);
    
    $payload = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $prompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    // Importante para no fallar por SSL en XAMPP local
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("Error en API Gemini (Cod $httpCode): " . $response);
    }

    $data = json_decode($response, true);
    
    // Localizar la base64. En generateContent de imagen, suele venir en candidate->content->parts->inlineData
    $base64Image = $data['predictions'][0]['bytesBase64Encoded'] ?? null;
    if (!$base64Image) {
        $base64Image = $data['candidates'][0]['content']['parts'][0]['inlineData']['data'] ?? null;
    }
    
    if (!$base64Image) {
        throw new Exception("Formato irreconocible en AI: " . mb_substr($response, 0, 800));
    }
    $imageBytes = base64_decode($base64Image);

    if ($imageBytes === false) {
        throw new Exception("Error al decodificar la imagen Base64");
    }

    // Guardar imagen temporal
    $tmpFilename = tempnam(sys_get_temp_dir(), 'ai_img_');
    file_put_contents($tmpFilename, $imageBytes);

    // Procesar con nuestro ImageHandler (que la pasará a WebP y todo el pipeline de BD)
    // Extraemos las primeras 5 palabras del prompt como nombre base
    $words = explode(' ', substr(trim($prompt), 0, 30));
    $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '', implode('-', $words));
    if(!$cleanName) $cleanName = 'ai-generated';
    $finalOriginalName = $cleanName . '-gemini.png';
    
    $resultado = ImageHandler::procesarImagen($tmpFilename, $finalOriginalName, 'general', 'Generado por IA Gemini', 'ia, gemini');
    
    unlink($tmpFilename); // Limpiar TMP

    if (!$resultado['exito']) {
        throw new Exception($resultado['error']);
    }

    // Insertar en BD 
    $datos = $resultado['datos'];
    $datos['subida_por'] = $_SESSION['user_id'] ?? null;
    
    $campos = implode(', ', array_keys($datos));
    $placeholders = implode(', ', array_fill(0, count($datos), '?'));
    $stmtIns = $db->prepare("INSERT INTO imagenes ($campos) VALUES ($placeholders)");
    $stmtIns->execute(array_values($datos));

    echo json_encode(['exito' => true, 'mensaje' => 'Imagen generada y subida con éxito.']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
