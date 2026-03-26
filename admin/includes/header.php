<?php
/**
 * Header genérico para panel de administración
 * Variables esperadas: $pageTitle, $activeMenu
 */

require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';

// Obtener detalles básicos
$nombreUsuario = $_SESSION['user_nombre'] ?? 'Usuario';
$rolUsuario = $_SESSION['user_rol'] ?? 'Invitado';
$pageTitle = $pageTitle ?? 'HotelCore Admin';
$activeMenu = $activeMenu ?? 'dashboard';

// Helpers para menú activo
function isActive($menu, $active) {
    return $menu === $active 
        ? 'bg-admin-active text-white' 
        : 'text-gray-300 hover:bg-gray-800 hover:text-white';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | HotelCore</title>
    <meta name="csrf-token" content="<?= CSRF::generateToken() ?>">
    <!-- Tailwind v4 Runtime JS (temporal para entorno de dev) -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --color-admin-bg: #F8FAFC;
            --color-admin-sidebar: #0F172A;
            --color-admin-active: #1E293B;
            --color-admin-accent: #2563EB;
        }
        body { background-color: theme('color.admin-bg'); }
    </style>
    <!-- FontAwesome 6 para íconos sociales y generales del CMS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Alpine.js para submenús -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex h-screen overflow-hidden text-gray-800">

    <!-- SIDEBAR -->
    <aside class="w-64 bg-admin-sidebar text-white flex flex-col h-full shadow-lg z-20">
        <div class="p-6 border-b border-gray-800">
            <h2 class="text-xl font-bold tracking-wider">HOTEL<span class="text-blue-400">CORE</span></h2>
            <p class="text-xs text-gray-400 mt-1">Panel de Control</p>
        </div>
        
        <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
            <a href="dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?= isActive('dashboard', $activeMenu) ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            
            <?php if(in_array($rolUsuario, ['super_admin', 'admin'])): ?>
            <a href="usuarios.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?= isActive('usuarios', $activeMenu) ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Usuarios
            </a>
            <?php endif; ?>

            <!-- Habitaciones (Submenú Desplegable) -->
            <div x-data="{ open: <?= in_array($activeMenu, ['habitaciones', 'habitaciones_config']) ? 'true' : 'false' ?> }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors <?= in_array($activeMenu, ['habitaciones', 'habitaciones_config']) ? 'bg-admin-active text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>Habitaciones</span>
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-transition class="pl-12 pr-4 py-1 space-y-1">
                    <a href="/hotel/admin/habitaciones/configuracion.php" class="block text-sm py-2 px-3 rounded-md transition-colors <?= $activeMenu === 'habitaciones_config' ? 'text-white font-medium bg-admin-accent/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?>">
                        <i class="fa-solid fa-sliders mr-2 text-xs"></i>Tarifas y Tipos
                    </a>
                    <a href="/hotel/admin/habitaciones/index.php" class="block text-sm py-2 px-3 rounded-md transition-colors <?= $activeMenu === 'habitaciones' ? 'text-white font-medium bg-admin-accent/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?>">
                        <i class="fa-solid fa-bed mr-2 text-xs"></i>Catálogo Inventario
                    </a>
                </div>
            </div>
            <a href="paginas.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?= isActive('paginas', $activeMenu) ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                Páginas
            </a>
            <a href="galeria.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?= isActive('galeria', $activeMenu) ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Galería
            </a>

            <!-- Formularios (Submenú Desplegable) -->
            <div x-data="{ open: <?= in_array($activeMenu, ['formularios', 'buzon']) ? 'true' : 'false' ?> }">
                <button @click="open = !open" class="w-full flex items-center justify-between px-4 py-3 rounded-lg transition-colors <?= in_array($activeMenu, ['formularios', 'buzon']) ? 'bg-admin-active text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' ?>">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Formularios</span>
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-transition class="pl-12 pr-4 py-1 space-y-1">
                    <?php if(in_array($rolUsuario, ['super_admin', 'admin'])): ?>
                    <a href="/hotel/admin/formularios/index.php" class="block text-sm py-2 px-3 rounded-md transition-colors <?= $activeMenu === 'formularios' ? 'text-white font-medium bg-admin-accent/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?>">
                        <i class="fa-solid fa-gear mr-2 text-xs"></i>Gestión
                    </a>
                    <?php endif; ?>
                    <a href="/hotel/admin/formularios/respuestas.php" class="block text-sm py-2 px-3 rounded-md transition-colors <?= $activeMenu === 'buzon' ? 'text-white font-medium bg-admin-accent/30' : 'text-gray-400 hover:text-white hover:bg-gray-800' ?>">
                        <i class="fa-solid fa-inbox mr-2 text-xs"></i>Buzón / Respuestas
                    </a>
                </div>
            </div>
            <a href="ajustes.php" class="flex items-center gap-3 px-4 py-3 rounded-lg transition-colors <?= isActive('ajustes', $activeMenu) ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Ajustes Globales
            </a>
        </nav>

        <div class="p-4 border-t border-gray-800">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="h-8 w-8 rounded-full bg-admin-accent flex items-center justify-center font-bold">
                    <?= substr($nombreUsuario, 0, 1) ?>
                </div>
                <div>
                    <p class="text-sm font-medium"><?= htmlspecialchars($nombreUsuario) ?></p>
                    <p class="text-xs text-gray-400 capitalize"><?= htmlspecialchars(str_replace('_', ' ', $rolUsuario)) ?></p>
                </div>
            </div>
            <a href="logout.php" class="block w-full text-center py-2 text-sm text-red-400 hover:text-red-300 hover:bg-gray-800 rounded transition-colors">
                Cerrar Sesión
            </a>
        </div>
    </aside>

    <!-- CONTENT -->
    <main class="flex-1 flex flex-col h-full relative overflow-y-auto">
        <header class="bg-white px-8 py-4 border-b border-gray-200 flex justify-between items-center sticky top-0 z-10">
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($pageTitle) ?></h1>
            <div class="flex items-center gap-4">
                <a href="/hotel/" target="_blank" class="px-4 py-2 text-sm text-admin-accent hover:bg-blue-50 rounded-lg transition-colors border border-blue-100 font-medium">
                    Ver sitio web &nearr;
                </a>
            </div>
        </header>
        
        <!-- MAIN CONTENT WRAPPER -->
        <div class="p-8">
