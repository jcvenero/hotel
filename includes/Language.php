<?php
/**
 * Clase Gestora de Idiomas
 * Decide qué idioma cargar en el frontend e interactuar con datos JSON.
 */

class Language {

    private static $current = null;
    private static $supported = ['es', 'en'];

    /**
     * Inicializa y determina el idioma actual activo del visitante
     */
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Si se pasa por URL ej: ?lang=en
        if (isset($_GET['lang']) && in_array($_GET['lang'], self::$supported)) {
            $_SESSION['site_lang'] = $_GET['lang'];
        }

        // Por defecto usar el de sesión o el principal español
        self::$current = $_SESSION['site_lang'] ?? 'es';
    }

    /**
     * Devuelve el idioma activo en la ejecución actual (ej: 'es' o 'en')
     */
    public static function current() {
        if (!self::$current) {
            self::init();
        }
        return self::$current;
    }

    /**
     * Retorna el valor traducido de un campo JSON (ej: de la base de datos)
     * Si no encuentra el idioma actual, usa un fallback al español.
     */
    public static function translateJsonField($jsonString) {
        if (empty($jsonString)) return '';

        // Si es array asociativo (como lo saca PDO a veces parseado o de nuestro cache)
        $data = is_array($jsonString) ? $jsonString : json_decode($jsonString, true);
        
        if (!$data || !is_array($data)) return $jsonString; // Si no es JSON valido, retornar flat string

        $lang = self::current();

        if (!empty($data[$lang])) {
            return $data[$lang];
        }

        // Fallback al español por seguridad si ingles no está en BD
        return $data['es'] ?? '';
    }

    /**
     * Helper rápido para procesar ajustes y campos repetitivos
     */
    public static function get($ajustesArray, $clave) {
        if (!isset($ajustesArray[$clave])) return null;

        $valorRaw = $ajustesArray[$clave];

        // Si es un string que parece array/json, parsearlo
        if (is_string($valorRaw) && (strpos($valorRaw, '{') === 0 || strpos($valorRaw, '[') === 0)) {
            $parsed = json_decode($valorRaw, true);
            if(json_last_error() === JSON_ERROR_NONE) {
                return self::translateJsonField($parsed);
            }
        }
        
        // Si el PDO devolvió un array (como en el api de obtener ajustes)
        if (is_array($valorRaw)) {
            return self::translateJsonField($valorRaw);
        }

        return $valorRaw;
    }
}
?>
