<?php
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Helpers.php';
require_once __DIR__ . '/../../includes/CSRF.php';

Auth::requireRole(['super_admin', 'admin', 'editor']);

$data = json_decode(file_get_contents('php://input'), true);

if (!CSRF::validate(apache_request_headers()['X-CSRF-Token'] ?? '')) {
    Helpers::jsonResponse(false, 'Token CSRF inválido', 403);
}

try {
    $db = Database::getInstance();
    $stmt = $db->query("SELECT clave, valor FROM sitio_ajustes WHERE clave IN ('gemini_api_key', 'gemini_system_prompt')");
    $config = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $apiKey = $config['gemini_api_key'] ?? '';
    if (empty($apiKey)) {
        Helpers::jsonResponse(false, 'Error: No se encontró tu Api Key de Gemini en Ajustes > API.', 400);
    }

    $prompt = $data['prompt'] ?? '';
    
    if (empty($prompt)) {
        Helpers::jsonResponse(false, 'El prompt provisto está vacío.', 400);
    }

    $systemInstruction = $config['gemini_system_prompt'] ?? '';
    
    if (!empty($systemInstruction)) {
        $prompt = "CONTEXTO MAESTRO DEL HOTEL Y PERSONALIDAD:\n" . $systemInstruction . "\n\n---\nSOLICITUD ACTUAL:\n" . $prompt;
    }

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apiKey;
    
    $payload = [
        "contents" => [
            ["parts" => [["text" => $prompt]]]
        ],
        "generationConfig" => [
            "temperature" => 0.7
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        $error = json_decode($response, true);
        $errMsg = $error['error']['message'] ?? 'Error desconocido de Google API';
        Helpers::jsonResponse(false, 'API Gemini falló: ' . $errMsg, 500);
    }

    $resData = json_decode($response, true);
    $text = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';

    Helpers::jsonResponse(true, trim($text));

} catch (Exception $e) {
    Helpers::jsonResponse(false, 'Error interno del servidor: ' . $e->getMessage(), 500);
}
?>
