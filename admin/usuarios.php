<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Auth.php';

// Solo Admins y Super Admins pueden ver usuarios
Auth::requireRole(['super_admin', 'admin']);

$pageTitle = 'Gestión de Usuarios';
$activeMenu = 'usuarios';

require_once __DIR__ . '/includes/header.php';
?>

<div class="flex justify-between items-center mb-6">
    <p class="text-gray-500">Administra los accesos y roles del equipo del hotel.</p>
    <button onclick="openModal()" class="bg-admin-accent hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
        <svg fill="none" class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nuevo Usuario
    </button>
</div>

<!-- Tabla de datos -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50 text-gray-500 text-sm border-b border-gray-100">
                <th class="px-6 py-4 font-medium">Nombre</th>
                <th class="px-6 py-4 font-medium">Email</th>
                <th class="px-6 py-4 font-medium">Rol</th>
                <th class="px-6 py-4 font-medium">Estado</th>
                <th class="px-6 py-4 font-medium">Última Sesión</th>
                <th class="px-6 py-4 font-medium text-right">Acciones</th>
            </tr>
        </thead>
        <tbody id="usuariosTable" class="divide-y divide-gray-100 text-sm">
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-400">Cargando usuarios...</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div id="userModal" class="fixed inset-0 bg-gray-900/50 hidden items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="font-bold text-gray-800" id="modalTitle">Nuevo Usuario</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="userForm" class="p-6">
            <input type="hidden" id="userId" name="id">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre_completo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-admin-accent outline-none">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-admin-accent outline-none">
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                    <select id="rol" name="rol" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-admin-accent outline-none bg-white">
                        <option value="editor">Editor</option>
                        <option value="recepcionista">Recepcionista</option>
                        <option value="admin">Admin</option>
                        <?php if($_SESSION['user_rol'] === 'super_admin'): ?>
                        <option value="super_admin">Super Admin</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select id="activo" name="activo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-admin-accent outline-none bg-white">
                        <option value="1">Activo</option>
                        <option value="0">Suspendido</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña <span id="pwdHint" class="text-xs text-gray-400 font-normal ml-2"></span></label>
                <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-admin-accent outline-none">
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 rounded-lg font-medium">Cancelar</button>
                <button type="submit" class="px-4 py-2 text-sm bg-admin-accent hover:bg-blue-700 text-white rounded-lg font-medium">Guardar Usuario</button>
            </div>
        </form>
    </div>
</div>

<script>
    const API_URL = '/hotel/api/usuarios';

    document.addEventListener("DOMContentLoaded", () => loadUsers());

    async function loadUsers() {
        try {
            const res = await apiCall(`${API_URL}/obtener.php`);
            if(res.exito) renderTable(res.data);
            else alert(res.error);
        } catch(e) { console.error("Error cargando usuarios."); }
    }

    function renderTable(users) {
        const tbody = document.getElementById('usuariosTable');
        tbody.innerHTML = '';
        users.forEach(u => {
            const bgStatus = u.activo == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
            const txtStatus = u.activo == 1 ? 'Activo' : 'Inactivo';
            const badgeRol = `bg-gray-100 text-gray-700 border border-gray-200`; // Se puede sofisticar
            
            tbody.innerHTML += `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 font-medium text-gray-900">${u.nombre_completo}</td>
                <td class="px-6 py-4 text-gray-500">${u.email}</td>
                <td class="px-6 py-4 capitalize"><span class="px-2 py-1 text-xs rounded-full ${badgeRol}">${u.rol.replace('_',' ')}</span></td>
                <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs font-medium ${bgStatus}">${txtStatus}</span></td>
                <td class="px-6 py-4 text-gray-500 text-xs">${u.ultima_sesion || 'Nunca'}</td>
                <td class="px-6 py-4 text-right">
                    <button onclick='editUser(${JSON.stringify(u).replace(/'/g, "&apos;")})' class="text-blue-500 hover:text-blue-700 font-medium mr-3">Editar</button>
                    <button onclick="deleteUser(${u.id})" class="text-red-500 hover:text-red-700 font-medium">Eliminar</button>
                </td>
            </tr>`;
        });
    }

    function openModal() {
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('modalTitle').innerText = 'Nuevo Usuario';
        document.getElementById('pwdHint').innerText = '(Requerida)';
        document.getElementById('password').required = true;
        document.getElementById('userModal').classList.replace('hidden', 'flex');
    }

    function closeModal() {
        document.getElementById('userModal').classList.replace('flex', 'hidden');
    }

    function editUser(user) {
        document.getElementById('userId').value = user.id;
        document.getElementById('nombre').value = user.nombre_completo;
        document.getElementById('email').value = user.email;
        document.getElementById('rol').value = user.rol;
        document.getElementById('activo').value = user.activo;
        
        document.getElementById('modalTitle').innerText = 'Editar Usuario';
        document.getElementById('pwdHint').innerText = '(Déjala en blanco para no cambiarla)';
        document.getElementById('password').required = false;
        
        document.getElementById('userModal').classList.replace('hidden', 'flex');
    }

    document.getElementById('userForm').onsubmit = async (e) => {
        e.preventDefault();
        const fd = new FormData(e.target);
        const data = Object.fromEntries(fd.entries());
        
        const endpoint = data.id ? `${API_URL}/actualizar.php` : `${API_URL}/crear.php`;
        
        try {
            const res = await apiCall(endpoint, 'POST', data);
            if(res.exito) {
                closeModal();
                loadUsers();
            } else {
                alert(res.error);
            }
        } catch(err) { alert("Error de conexión"); }
    };

    async function deleteUser(id) {
        if(!confirm("¿Deseas eliminar este usuario definitivamente?")) return;
        try {
            const res = await apiCall(`${API_URL}/eliminar.php`, 'POST', {id});
            if(res.exito) loadUsers();
            else alert(res.error);
        } catch(e) { alert("Error borrando el usuario"); }
    }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
