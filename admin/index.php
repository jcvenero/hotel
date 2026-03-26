<?php
require_once __DIR__ . '/../includes/Auth.php';

// Si el usuario ya está autenticado, lo enviamos al dashboard
if (Auth::check()) {
    header('Location: dashboard.php');
} else {
    // Si no está autenticado, lo enviamos al login
    header('Location: login.php');
}
exit;
?>
