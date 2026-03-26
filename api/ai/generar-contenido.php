<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido");
    }
    if (!Auth::check()) {
        http_response_code(401);
        throw new Exception("No autorizado");
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!CSRF::validate($input['csrf_token'] ?? '')) {
        throw new Exception("Token CSRF inválido");
    }

    $tipo = $input['tipo'] ?? '';
    $contexto = trim($input['contexto'] ?? '');
    $idioma_destino = $input['idioma_destino'] ?? 'en';

    if (!$tipo) throw new Exception("Tipo de solicitud requerido");

    // Obtener API Key
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT valor FROM sitio_ajustes WHERE clave = 'gemini_api_key'");
    $stmt->execute();
    $keyRow = $stmt->fetch();
    $apiKey = $keyRow['valor'] ?? '';
    if (!$apiKey) throw new Exception("No hay API Key de Gemini configurada en Ajustes");

    // Construir prompt según tipo
    switch ($tipo) {
        case 'descripcion':
            $prompt = "Eres un redactor profesional de contenido hotelero de alta gama. Genera una descripción comercial atractiva en español para una habitación de hotel con estas características: {$contexto}. La descripción debe ser de 2-3 párrafos, tono elegante y acogedor, destacando confort y experiencia del huésped. Solo devuelve la descripción, sin títulos ni formato markdown.";
            break;

        case 'traduccion':
            $targetLang = $idioma_destino === 'en' ? 'inglés' : 'español';
            $prompt = "Traduce el siguiente texto de forma profesional y natural al {$targetLang}, manteniendo el tono comercial hotelero elegante. Solo devuelve la traducción, sin notas ni explicaciones:\n\n{$contexto}";
            break;

        case 'seo':
            $prompt = "Eres un experto SEO hotelero. Basándote en este contenido de habitación: \"{$contexto}\", genera en formato JSON exacto (sin markdown, sin bloques de código) lo siguiente:
{\"titulo_es\": \"Título SEO en español (max 60 chars)\", \"titulo_en\": \"SEO Title in English (max 60 chars)\", \"descripcion_es\": \"Meta description en español (max 155 chars)\", \"descripcion_en\": \"Meta description in English (max 155 chars)\", \"keywords_es\": \"palabras, clave, separadas, por, comas\", \"keywords_en\": \"keywords, separated, by, commas\"}";
            break;

        case 'schema':
            $prompt = "Eres un experto en Schema.org para hoteles. Genera un JSON-LD válido de tipo HotelRoom para esta habitación: \"{$contexto}\". Incluye @context, @type, name, description, occupancy, bed. Devuelve SOLO el JSON válido sin bloques de código ni markdown.";
            break;

        case 'faqs':
            $prompt = "Genera 4 preguntas frecuentes (FAQ) relevantes para un huésped potencial sobre esta habitación de hotel: \"{$contexto}\". Formato JSON exacto (sin markdown, sin bloques de código):
[{\"pregunta_es\": \"...\", \"respuesta_es\": \"...\", \"pregunta_en\": \"...\", \"respuesta_en\": \"...\"}]";
            break;

        case 'comodidades':
            $prompt = "Sugiere 8 comodidades típicas para esta habitación de hotel: \"{$contexto}\". Formato JSON exacto (sin markdown):
[{\"nombre\": \"WiFi Gratuito\", \"icono\": \"fa-wifi\"}, {\"nombre\": \"Aire Acondicionado\", \"icono\": \"fa-snowflake\"}] Usa iconos de FontAwesome 6 (sin el prefijo fa-solid, solo el nombre como fa-wifi).";
            break;

        case 'amenities':
            $prompt = "Sugiere 6 amenities/servicios adicionales para esta habitación de hotel: \"{$contexto}\". Formato JSON exacto (sin markdown):
[{\"nombre\": \"Desayuno Incluido\", \"icono\": \"fa-mug-saucer\"}, {\"nombre\": \"Estacionamiento\", \"icono\": \"fa-square-parking\"}] Usa iconos de FontAwesome 6.";
            break;

        default:
            throw new Exception("Tipo de solicitud IA no reconocido: $tipo");
    }

    // Llamar a Gemini 2.5 Flash (texto)
    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . urlencode($apiKey);

    $payload = [
        "contents" => [["parts" => [["text" => $prompt]]]],
        "generationConfig" => [
            "temperature" => 0.7,
            "maxOutputTokens" => 2048
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new Exception("Error Gemini (Cod $httpCode): " . mb_substr($response, 0, 500));
    }

    $data = json_decode($response, true);
    $texto = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

    if (!$texto) {
        throw new Exception("Respuesta vacía de Gemini");
    }

    // Limpiar markdown code blocks si vienen
    $texto = preg_replace('/^```(?:json)?\s*/m', '', $texto);
    $texto = preg_replace('/```\s*$/m', '', $texto);
    $texto = trim($texto);

    // Para tipos JSON, intentar parsear
    $resultado = ['texto' => $texto];
    if (in_array($tipo, ['seo', 'schema', 'faqs', 'comodidades', 'amenities'])) {
        $parsed = json_decode($texto, true);
        if ($parsed) {
            $resultado['json'] = $parsed;
        }
    }

    echo json_encode(['exito' => true, 'resultado' => $resultado]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['exito' => false, 'error' => $e->getMessage()]);
}
