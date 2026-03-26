<?php
/**
 * Validator Helper Class
 */

class Validator {
    
    /**
     * Valida formato de email
     */
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Sanea email (remueve caracteres ilegales)
     */
    public static function sanitizeEmail($email) {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Revisa que el string tenga entre $min y $max caracteres
     */
    public static function stringLength($string, $min = 1, $max = 255) {
        $length = mb_strlen(trim($string), 'UTF-8');
        return $length >= $min && $length <= $max;
    }

    /**
     * Limpia un string de todo riesgo básico para inputs generales de texto
     */
    public static function sanitizeString($string) {
        $string = trim($string ?? '');
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Limpia HTML permitiendo ciertas etiquetas (Se usará HTMLPurifier en Fase 2)
     */
    public static function sanitizeHTML($html) {
        // Por ahora, sólo quitamos scripts o cosas nocivas básicas.
        // PRECAUCIÓN: No es un purificador profundo.
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        return trim($html);
    }

    /**
     * Valida números enteros
     */
    public static function integer($int) {
        return filter_var($int, FILTER_VALIDATE_INT) !== false;
    }
}
?>
