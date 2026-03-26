<?php
/**
 * Clase CSRF (Cross-Site Request Forgery) Protection
 */

class CSRF {

    /**
     * Genera y retorna un token CSRF atado a la sesión
     */
    public static function generateToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida el token pasado contra el de la sesión
     */
    public static function validate($token) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Retorna el input HTML listo para formularios
     */
    public static function getField() {
        $token = self::generateToken();
        // Usamos htmlspecialchars por buenas prácticas aunque sea alfanumérico
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }
}
?>
