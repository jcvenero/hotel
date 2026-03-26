<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/CSRF.php';
require_once __DIR__ . '/../includes/Security.php';

// Si ya esta logueado, redirigir al dashboard
if (Auth::check()) {
    header('Location: ' . ADMIN_URL . '/dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
        $error = 'Token de seguridad inválido. Recarga la página.';
    } else {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $login = Auth::login($email, $password);
        
        if ($login['exito']) {
            header('Location: ' . ADMIN_URL . '/dashboard.php');
            exit;
        } else {
            $error = $login['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Administrador | HotelCore</title>
    <!-- Serviremos el CSS mediante Tailwind v4 (CLI runtime para develope) temporal hasta compilación final -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-hotel-dark: #1E293B;
            --color-hotel-brand: #0F172A;
            --color-hotel-accent: #3B82F6;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-hotel-brand">HotelCore Admin</h1>
            <p class="text-sm text-gray-500 mt-2">Ingresa tus credenciales para continuar</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 text-sm flex items-center gap-2 border border-red-100">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <?= CSRF::getField() ?>

            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                <input type="email" name="email" id="email" required autocomplete="email"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-hotel-accent focus:border-hotel-accent outline-none transition-all">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" id="password" required autocomplete="current-password"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-hotel-accent focus:border-hotel-accent outline-none transition-all">
            </div>

            <button type="submit" 
                    class="w-full bg-hotel-brand hover:bg-hotel-dark text-white font-medium py-2.5 rounded-lg transition-colors duration-200">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-sm text-gray-500 hover:text-hotel-accent transition-colors">&larr; Volver al sitio web</a>
        </div>
    </div>

</body>
</html>
