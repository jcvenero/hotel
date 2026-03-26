<?php
session_start();
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Security.php';
require_once __DIR__ . '/../../includes/CSRF.php';

if (!Auth::check()) {
    header('Location: /hotel/admin/login.php');
    exit;
}
$pageTitle = 'Catálogo de Habitaciones';
$activeMenu = 'habitaciones';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex gap-4 items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800">Inventario de Habitaciones</h2>
    <a href="/hotel/admin/habitaciones/editar.php?nuevo=1" class="bg-admin-accent hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition-colors flex items-center gap-2 no-underline">
        <i class="fa-solid fa-plus"></i> Nueva Habitación
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[60vh]">
    <div id="tabla-habitaciones" class="p-6">
        <div class="text-center py-12 text-gray-400"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Cargando...</div>
    </div>
</div>

<script>
const CSRF = '<?= CSRF::generateToken() ?>';
const API_HAB = '/hotel/api/habitaciones/habitaciones.php';

const estadoClases = {
    'disponible': 'bg-green-100 text-green-700',
    'ocupada': 'bg-orange-100 text-orange-700',
    'mantenimiento': 'bg-red-100 text-red-600'
};

async function cargarHabitaciones() {
    const res = await fetch(API_HAB);
    const d = await res.json();
    if (!d.exito) return;
    const ct = document.getElementById('tabla-habitaciones');

    if (d.habitaciones.length === 0) {
        ct.innerHTML = '<div class="text-center py-16 text-gray-400"><i class="fa-solid fa-bed text-5xl mb-4 block"></i><p class="text-lg font-medium text-gray-500">Sin habitaciones todavía</p><p class="text-sm mt-1">Primero configura los Tipos y Tarifas, luego crea tu primera habitación.</p></div>';
        return;
    }

    let html = `<table class="w-full text-sm"><thead><tr class="text-left text-xs text-gray-500 uppercase border-b border-gray-200">
        <th class="pb-3 px-4">Nº</th><th class="pb-3 px-4">Nombre</th><th class="pb-3 px-4">Tipo</th>
        <th class="pb-3 px-4">Estado</th><th class="pb-3 px-4">Capacidad</th><th class="pb-3 px-4">Precio Actual</th>
        <th class="pb-3 px-4 text-right">Acciones</th></tr></thead><tbody>`;

    d.habitaciones.forEach(h => {
        const precio = h.precio_mostrado ? `$${parseFloat(h.precio_mostrado).toFixed(2)}` : '<span class="text-gray-300 italic">Sin precio</span>';
        html += `<tr class="border-b border-gray-100 hover:bg-blue-50/40 transition-colors">
            <td class="py-3.5 px-4 font-bold text-gray-800">${h.numero_habitacion || '—'}</td>
            <td class="py-3.5 px-4 font-medium">${h.nombre_es || h.slug}</td>
            <td class="py-3.5 px-4"><span class="bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded text-xs font-bold">${h.tipo_nombre || '?'}</span></td>
            <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded text-xs font-bold ${estadoClases[h.estado] || ''}">${h.estado}</span></td>
            <td class="py-3.5 px-4">${h.capacidad_huespedes} <i class="fa-solid fa-user-group text-gray-400 text-xs ml-1"></i></td>
            <td class="py-3.5 px-4 font-bold text-green-700">${precio} <span class="text-[10px] text-gray-400 font-normal">(${h.tarifa_tipo})</span></td>
            <td class="py-3.5 px-4 text-right space-x-2">
                <a href="/hotel/admin/habitaciones/editar.php?id=${h.id}" class="text-blue-600 hover:text-blue-800 text-xs font-bold no-underline"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
                <button onclick="eliminarHab(${h.id})" class="text-red-500 hover:text-red-700 text-xs font-bold"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>`;
    });
    html += '</tbody></table>';
    ct.innerHTML = html;
}

async function eliminarHab(id) {
    if (!confirm('¿Seguro de eliminar esta habitación? Se borrarán todos sus datos asociados.')) return;
    const res = await fetch(API_HAB, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ csrf_token: CSRF, _method: 'DELETE', id }) });
    const d = await res.json();
    if (d.exito) cargarHabitaciones(); else alert(d.error);
}

document.addEventListener('DOMContentLoaded', cargarHabitaciones);
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
