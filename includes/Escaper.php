<?php
/**
 * Clase Escaper
 * Previene Cross-Site Scripting (XSS) al imprimir datos en las vistas
 */

class Escaper {
    /**
     * Sanea strings básicos para imprimir en HTML
     */
    public static function html($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Sanea atributos HTML (como href, src, title)
     */
    public static function attr($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Sanea texto que ira dentro de JS
     */
    public static function js($str) {
        return json_encode($str, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
    }
}
?>
