<?php
/**
 * Auth Class
 * Manejo de Sesiones, Login, Logout, y Permisos Base Módulo de Usuarios
 */

require_once __DIR__ . '/Database.php';

class Auth {

    /**
     * Login seguro usando PDO preparado
     */
    public static function login($email, $password) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            if (!$user['activo']) {
                return ['exito' => false, 'error' => 'Usuario inactivo o suspendido.'];
            }

            // Regenerar ID de sesión crítico preventivo Session Fixation
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_rol'] = $user['rol'];
            $_SESSION['user_nombre'] = $user['nombre_completo'];

            // Registrar log de sesión
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $updateStmt = $db->prepare("UPDATE usuarios SET ultima_sesion = NOW(), ip_ultima_sesion = ? WHERE id = ?");
            $updateStmt->execute([$ip, $user['id']]);

            return ['exito' => true, 'usuario' => $user];
        }

        return ['exito' => false, 'error' => 'Credenciales incorrectas.'];
    }

    /**
     * Destruye la sesión de manera segura
     */
    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    /**
     * Verifica si el usuario actual tiene sesión válida iniciada
     */
    public static function check() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }

    /**
     * Retorna el rol del usuario actual
     */
    public static function role() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_rol'] ?? null;
    }

    /**
     * Validar que solo usuarios con determinados roles puedan pasar
     */
    public static function requireRole($rolesPermitidos) {
        if (!self::check()) {
            header('Location: /hotel/admin/login.php');
            exit;
        }

        // Si es un array de roles
        if (is_array($rolesPermitidos)) {
            if (!in_array(self::role(), $rolesPermitidos)) {
                die("Error 403: No tienes permisos para acceder a este módulo.");
            }
        } else {
            // Si es un solo string
            if (self::role() !== $rolesPermitidos) {
                die("Error 403: No tienes permisos para acceder a este módulo.");
            }
        }
    }
}
?>
