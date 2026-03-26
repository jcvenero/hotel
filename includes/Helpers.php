<?php
/**
 * Funciones Auxiliares Generales (Helpers)
 */

class Helpers {
    /**
     * Genera un SLUG limpio a partir de un string
     */
    public static function slugify($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }

    /**
     * Retorna JSON estándar para API
     */
    public static function jsonResponse($exito, $data = [], $codigo = 200) {
        http_response_code($codigo);
        header('Content-Type: application/json');
        
        $response = ['exito' => $exito];
        if ($exito) {
            $response['data'] = $data;
        } else {
            $response['error'] = escapeshellarg($data); // if $data is the error message
        }

        echo json_encode($response);
        exit;
    }
}
?>
