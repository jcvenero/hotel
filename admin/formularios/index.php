<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Helpers.php';

if (!Auth::check() || !in_array($_SESSION['user_rol'], ['super_admin', 'admin'])) {
    header('Location: /hotel/admin/login.php');
    exit;
}

$pageTitle = 'Gestión de Formularios';
$activeMenu = 'formularios';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Gestión de Formularios</h2>
        <p class="text-sm text-gray-500">Crea y configura formularios personalizados para tu sitio.</p>
    </div>
    <a href="editar.php" class="bg-admin-accent hover:opacity-90 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition-all flex items-center gap-2">
        <i class="fa-solid fa-plus"></i> Nuevo Formulario
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipo / Slug</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Respuestas</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Acciones</th>
            </tr>
        </thead>
        <tbody id="lista-formularios" class="divide-y divide-gray-100">
            <tr>
                <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                    <i class="fa-solid fa-circle-notch fa-spin text-2xl mb-2"></i><br>Cargando formularios...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
const API_URL = '/hotel/api/formularios/formularios.php';

async function cargarFormularios() {
    try {
        const res = await fetch(API_URL);
        const data = await res.json();
        
        if (data.exito) {
            const tbody = document.getElementById('lista-formularios');
            if (data.formularios.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">No hay formularios creados.</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.formularios.map(f => `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">${f.nombre}</div>
                        <div class="text-[10px] text-gray-400 font-mono">${f.email_notificacion || 'Sin email de notificación'}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded text-[10px] font-bold uppercase">${f.tipo}</span>
                        <div class="text-[11px] text-gray-400 mt-1">/${f.slug}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="flex items-center gap-1.5 ${f.activo ? 'text-green-600' : 'text-red-500'} font-medium text-xs">
                            <span class="w-2 h-2 rounded-full ${f.activo ? 'bg-green-500' : 'bg-red-500'}"></span>
                            ${f.activo ? 'Activo' : 'Inactivo'}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="respuestas.php?formulario_id=${f.id}" class="text-xs font-bold text-indigo-600 hover:underline">
                             Ver Buzón
                        </a>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="editar.php?id=${f.id}" class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Editar">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <button onclick="eliminarFormulario(${f.id})" class="text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Eliminar">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }
    } catch (err) {
        console.error(err);
    }
}

async function eliminarFormulario(id) {
    if (!confirm('¿Estás seguro de eliminar este formulario? Se borrarán todos sus campos y respuestas asociadas.')) return;
    
    try {
        const res = await fetch(API_URL, {
            method: 'DELETE',
            body: JSON.stringify({ id, csrf_token: '<?= CSRF::generateToken() ?>' }),
            headers: { 'Content-Type': 'application/json' }
        });
        const data = await res.json();
        if (data.exito) cargarFormularios();
        else alert(data.error);
    } catch (err) {
        alert('Error al conectar con la API');
    }
}

document.addEventListener('DOMContentLoaded', cargarFormularios);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
