<?php
/**
 * Clase de Seguridad General
 * Funciones anti-XSS, rate limiting y resiliencia de sesión
 */

class Security {
    
    /**
     * Aplica los headers de seguridad más comunes
     */
    public static function setSecurityHeaders() {
        header("X-Frame-Options: SAMEORIGIN");
        header("X-XSS-Protection: 1; mode=block");
        header("X-Content-Type-Options: nosniff");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
        // CSP se puede incorporar progresivamente según las necesidades del frontend
    }

    /**
     * Fuerza el uso de HTTPS en producción
     */
    public static function forceHTTPS() {
        if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                return;
            }
            if (php_sapi_name() != "cli") { // Ignorar en linea de comandos
                $redirectUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                header("HTTP/1.1 301 Moved Permanently");
                header('Location: ' . $redirectUrl);
                exit;
            }
        }
    }

    /**
     * Sanea de manera básica un string (Escape contra XSS)
     * Para HTML enriquecido (TinyMCE) se usará HTML Purifier más adelante
     */
    public static function escape($string) {
        return htmlspecialchars($string ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
?>
