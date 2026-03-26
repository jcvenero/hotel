<?php
session_start();
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Security.php';
require_once __DIR__ . '/../../includes/CSRF.php';

if (!Auth::check()) {
    header('Location: /hotel/admin/login.php');
    exit;
}
$pageTitle = 'Configuración de Habitaciones';
$activeMenu = 'habitaciones_config';

require_once __DIR__ . '/../includes/header.php';
?>

<div class="flex gap-4 items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800">Tarifas, Tipos & Temporadas</h2>
</div>

<!-- TABS -->
<div x-data="{ tab: 'tipos' }" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[70vh]">
    <div class="border-b border-gray-200 bg-gray-50 px-6 flex gap-1">
        <button @click="tab='tipos'" :class="tab==='tipos' ? 'border-b-2 border-admin-accent text-admin-accent font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-4 text-sm transition-colors -mb-px">
            <i class="fa-solid fa-layer-group mr-1.5"></i> Tipos de Habitación
        </button>
        <button @click="tab='temporadas'" :class="tab==='temporadas' ? 'border-b-2 border-admin-accent text-admin-accent font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-4 text-sm transition-colors -mb-px">
            <i class="fa-solid fa-calendar-days mr-1.5"></i> Temporadas
        </button>
        <button @click="tab='tarifas'" :class="tab==='tarifas' ? 'border-b-2 border-admin-accent text-admin-accent font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-4 text-sm transition-colors -mb-px">
            <i class="fa-solid fa-tags mr-1.5"></i> Tarifas por Tipo
        </button>
    </div>

    <!-- ======================== TAB 1: TIPOS ======================== -->
    <div x-show="tab==='tipos'" class="p-6">
        <div class="flex justify-between items-center mb-6">
            <p class="text-sm text-gray-500">Define las categorías que organizarán tu inventario (Simple, Doble, Suite...)</p>
            <button onclick="abrirModalTipo()" class="bg-admin-accent hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition-colors flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Nuevo Tipo
            </button>
        </div>
        <div id="tabla-tipos">
            <div class="text-center py-12 text-gray-400"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Cargando...</div>
        </div>
    </div>

    <!-- ======================== TAB 2: TEMPORADAS ======================== -->
    <div x-show="tab==='temporadas'" class="p-6">
        <div class="flex justify-between items-center mb-6">
            <p class="text-sm text-gray-500">Administra los rangos de fecha de tu hotel. Puedes tener múltiples rangos por temporada.</p>
            <button onclick="abrirModalTemp()" class="bg-admin-accent hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition-colors flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Agregar Rango
            </button>
        </div>
        <div id="tabla-temporadas">
            <div class="text-center py-12 text-gray-400"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Cargando...</div>
        </div>
    </div>

    <!-- ======================== TAB 3: TARIFAS POR TIPO ======================== -->
    <div x-show="tab==='tarifas'" class="p-6">
        <p class="text-sm text-gray-500 mb-6">Selecciona un Tipo de Habitación para configurar sus 4 tarifas oficiales.</p>
        <div class="max-w-2xl mx-auto">
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-2">Elegir Tipo de Habitación</label>
                <select id="sel-tipo-tarifa" onchange="cargarTarifasTipo(this.value)" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white shadow-sm text-sm outline-none focus:border-admin-accent focus:ring-1 focus:ring-admin-accent">
                    <option value="">-- Selecciona --</option>
                </select>
            </div>

            <div id="form-tarifas-container" class="hidden">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl border border-indigo-100 p-6 space-y-5">
                    <input type="hidden" id="tf-tipo-id">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase"><i class="fa-solid fa-snowflake text-blue-400 mr-1"></i> Temporada Baja</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400 text-sm font-bold">$</span>
                                <input type="number" step="0.01" id="tf-baja" class="w-full pl-8 pr-4 py-3 border border-blue-200 rounded-lg outline-none focus:border-admin-accent text-lg font-bold bg-white shadow-sm" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase"><i class="fa-solid fa-cloud-sun text-amber-400 mr-1"></i> Temporada Regular</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400 text-sm font-bold">$</span>
                                <input type="number" step="0.01" id="tf-regular" class="w-full pl-8 pr-4 py-3 border border-amber-200 rounded-lg outline-none focus:border-admin-accent text-lg font-bold bg-white shadow-sm" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase"><i class="fa-solid fa-sun text-orange-500 mr-1"></i> Temporada Alta</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400 text-sm font-bold">$</span>
                                <input type="number" step="0.01" id="tf-alta" class="w-full pl-8 pr-4 py-3 border border-orange-200 rounded-lg outline-none focus:border-admin-accent text-lg font-bold bg-white shadow-sm" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase"><i class="fa-solid fa-hand text-purple-500 mr-1"></i> Tarifa Manual</label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-400 text-sm font-bold">$</span>
                                <input type="number" step="0.01" id="tf-manual" class="w-full pl-8 pr-4 py-3 border border-purple-200 rounded-lg outline-none focus:border-admin-accent text-lg font-bold bg-white shadow-sm" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-purple-50 border border-purple-100 rounded-lg p-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="tf-manual-switch" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                        <div>
                            <span class="text-sm font-bold text-purple-800">Activar Tarifa Manual</span>
                            <p class="text-xs text-purple-500">Si está activa, domina sobre cualquier temporada por fecha.</p>
                        </div>
                    </div>
                    <button onclick="guardarTarifas()" id="btn-guardar-tarifas" class="w-full bg-admin-accent hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md transition-all text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Guardar Tarifas
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL: Tipo de Habitación ==================== -->
<div id="modal-tipo" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4" id="modal-tipo-titulo">Nuevo Tipo de Habitación</h3>
        <input type="hidden" id="tipo-edit-id">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Nombre *</label>
                <input type="text" id="tipo-nombre" class="w-full p-3 border border-gray-300 rounded-lg outline-none focus:border-admin-accent focus:ring-1 focus:ring-admin-accent" placeholder="Ej: Suite Presidencial">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Descripción (Opcional)</label>
                <textarea id="tipo-desc" class="w-full p-3 border border-gray-300 rounded-lg outline-none focus:border-admin-accent h-20 resize-none" placeholder="Breve descripción para identificar este tipo..."></textarea>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button onclick="cerrarModalTipo()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">Cancelar</button>
            <button onclick="guardarTipo()" id="btn-guardar-tipo" class="flex-1 py-2.5 bg-admin-accent hover:bg-blue-700 text-white rounded-lg font-bold shadow transition-colors">Guardar</button>
        </div>
    </div>
</div>

<!-- ==================== MODAL: Temporada ==================== -->
<div id="modal-temp" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4" id="modal-temp-titulo">Agregar Rango de Temporada</h3>
        <input type="hidden" id="temp-edit-id">
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de Temporada *</label>
                <select id="temp-tipo" class="w-full p-3 border border-gray-300 rounded-lg outline-none focus:border-admin-accent bg-white">
                    <option value="baja">🟦 Temporada Baja</option>
                    <option value="regular">🟧 Temporada Regular</option>
                    <option value="alta">🟥 Temporada Alta</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Fecha Inicio *</label>
                    <input type="date" id="temp-inicio" class="w-full p-3 border border-gray-300 rounded-lg outline-none focus:border-admin-accent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Fecha Fin *</label>
                    <input type="date" id="temp-fin" class="w-full p-3 border border-gray-300 rounded-lg outline-none focus:border-admin-accent">
                </div>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Descripción (Opcional)</label>
                <input type="text" id="temp-desc" class="w-full p-3 border border-gray-300 rounded-lg outline-none focus:border-admin-accent" placeholder="Ej: Verano Cusco">
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button onclick="cerrarModalTemp()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">Cancelar</button>
            <button onclick="guardarTemp()" id="btn-guardar-temp" class="flex-1 py-2.5 bg-admin-accent hover:bg-blue-700 text-white rounded-lg font-bold shadow transition-colors">Guardar</button>
        </div>
    </div>
</div>

<script>
const CSRF = '<?= CSRF::generateToken() ?>';
const API_TIPOS = '/hotel/api/habitaciones/tipos.php';
const API_TEMPS = '/hotel/api/habitaciones/temporadas.php';
const API_TARIFAS = '/hotel/api/habitaciones/tarifas.php';

// ===================== TIPOS =====================
async function cargarTipos() {
    const res = await fetch(API_TIPOS);
    const d = await res.json();
    if (!d.exito) return;
    const ct = document.getElementById('tabla-tipos');
    if (d.tipos.length === 0) {
        ct.innerHTML = '<div class="text-center py-12 text-gray-400"><i class="fa-solid fa-folder-open text-4xl mb-3 block"></i>No hay tipos de habitación. Crea el primero.</div>';
        return;
    }
    let html = `<table class="w-full text-sm"><thead><tr class="text-left text-xs text-gray-500 uppercase border-b border-gray-200"><th class="pb-3 px-4">Nombre</th><th class="pb-3 px-4">Slug</th><th class="pb-3 px-4">Descripción</th><th class="pb-3 px-4">Estado</th><th class="pb-3 px-4 text-right">Acciones</th></tr></thead><tbody>`;
    d.tipos.forEach(t => {
        html += `<tr class="border-b border-gray-100 hover:bg-blue-50/40 transition-colors">
            <td class="py-3.5 px-4 font-bold text-gray-800">${t.nombre}</td>
            <td class="py-3.5 px-4 text-gray-500 font-mono text-xs">${t.slug}</td>
            <td class="py-3.5 px-4 text-gray-500 truncate max-w-[200px]">${t.descripcion || '<span class=text-gray-300>—</span>'}</td>
            <td class="py-3.5 px-4"><span class="px-2.5 py-1 rounded text-xs font-bold ${t.activo == 1 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600'}">${t.activo == 1 ? 'Activo' : 'Inactivo'}</span></td>
            <td class="py-3.5 px-4 text-right space-x-2">
                <button onclick='editarTipo(${JSON.stringify(t)})' class="text-blue-600 hover:text-blue-800 text-xs font-bold"><i class="fa-solid fa-pen-to-square"></i> Editar</button>
                <button onclick="eliminarTipo(${t.id})" class="text-red-500 hover:text-red-700 text-xs font-bold"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>`;
    });
    html += '</tbody></table>';
    ct.innerHTML = html;

    // También poblar select de tarifas
    const sel = document.getElementById('sel-tipo-tarifa');
    sel.innerHTML = '<option value="">-- Selecciona --</option>';
    d.tipos.forEach(t => { if(t.activo == 1) sel.innerHTML += `<option value="${t.id}">${t.nombre}</option>`; });
}

function abrirModalTipo(edit = null) {
    document.getElementById('tipo-edit-id').value = edit ? edit.id : '';
    document.getElementById('tipo-nombre').value = edit ? edit.nombre : '';
    document.getElementById('tipo-desc').value = edit ? (edit.descripcion || '') : '';
    document.getElementById('modal-tipo-titulo').textContent = edit ? 'Editar Tipo' : 'Nuevo Tipo de Habitación';
    document.getElementById('modal-tipo').classList.remove('hidden');
}
function cerrarModalTipo() { document.getElementById('modal-tipo').classList.add('hidden'); }
function editarTipo(t) { abrirModalTipo(t); }

async function guardarTipo() {
    const id = document.getElementById('tipo-edit-id').value;
    const nombre = document.getElementById('tipo-nombre').value;
    const desc = document.getElementById('tipo-desc').value;
    if (!nombre) return alert('El nombre es obligatorio');

    const body = { csrf_token: CSRF, nombre, descripcion: desc };
    if (id) { body._method = 'PUT'; body.id = id; }

    const res = await fetch(API_TIPOS, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(body) });
    const d = await res.json();
    if (d.exito) { cerrarModalTipo(); cargarTipos(); } else { alert(d.error); }
}

async function eliminarTipo(id) {
    if (!confirm('¿Seguro de eliminar este tipo de habitación?')) return;
    const res = await fetch(API_TIPOS, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ csrf_token: CSRF, _method: 'DELETE', id }) });
    const d = await res.json();
    if (d.exito) cargarTipos(); else alert(d.error);
}

// ===================== TEMPORADAS =====================
const coloresTemp = { baja: 'bg-blue-100 text-blue-700', regular: 'bg-amber-100 text-amber-700', alta: 'bg-red-100 text-red-700' };
const iconosTemp = { baja: 'fa-snowflake text-blue-500', regular: 'fa-cloud-sun text-amber-500', alta: 'fa-sun text-red-500' };

async function cargarTemporadas() {
    const res = await fetch(API_TEMPS);
    const d = await res.json();
    if (!d.exito) return;
    const ct = document.getElementById('tabla-temporadas');
    if (d.temporadas.length === 0) {
        ct.innerHTML = '<div class="text-center py-12 text-gray-400"><i class="fa-solid fa-calendar-xmark text-4xl mb-3 block"></i>No hay temporadas configuradas aún.</div>';
        return;
    }
    let html = `<table class="w-full text-sm"><thead><tr class="text-left text-xs text-gray-500 uppercase border-b border-gray-200"><th class="pb-3 px-4">Tipo</th><th class="pb-3 px-4">Fecha Inicio</th><th class="pb-3 px-4">Fecha Fin</th><th class="pb-3 px-4">Descripción</th><th class="pb-3 px-4 text-right">Acciones</th></tr></thead><tbody>`;
    d.temporadas.forEach(t => {
        html += `<tr class="border-b border-gray-100 hover:bg-blue-50/40 transition-colors">
            <td class="py-3.5 px-4"><span class="px-3 py-1.5 rounded-full text-xs font-bold ${coloresTemp[t.tipo_temporada]}"><i class="fa-solid ${iconosTemp[t.tipo_temporada]} mr-1"></i>${t.tipo_temporada.toUpperCase()}</span></td>
            <td class="py-3.5 px-4 font-medium">${t.fecha_inicio}</td>
            <td class="py-3.5 px-4 font-medium">${t.fecha_fin}</td>
            <td class="py-3.5 px-4 text-gray-500">${t.descripcion || '—'}</td>
            <td class="py-3.5 px-4 text-right space-x-2">
                <button onclick='editarTemp(${JSON.stringify(t)})' class="text-blue-600 hover:text-blue-800 text-xs font-bold"><i class="fa-solid fa-pen-to-square"></i></button>
                <button onclick="eliminarTemp(${t.id})" class="text-red-500 hover:text-red-700 text-xs font-bold"><i class="fa-solid fa-trash"></i></button>
            </td>
        </tr>`;
    });
    html += '</tbody></table>';
    ct.innerHTML = html;
}

function abrirModalTemp(edit = null) {
    document.getElementById('temp-edit-id').value = edit ? edit.id : '';
    document.getElementById('temp-tipo').value = edit ? edit.tipo_temporada : 'baja';
    document.getElementById('temp-inicio').value = edit ? edit.fecha_inicio : '';
    document.getElementById('temp-fin').value = edit ? edit.fecha_fin : '';
    document.getElementById('temp-desc').value = edit ? (edit.descripcion || '') : '';
    document.getElementById('modal-temp-titulo').textContent = edit ? 'Editar Rango' : 'Agregar Rango de Temporada';
    document.getElementById('modal-temp').classList.remove('hidden');
}
function cerrarModalTemp() { document.getElementById('modal-temp').classList.add('hidden'); }
function editarTemp(t) { abrirModalTemp(t); }

async function guardarTemp() {
    const id = document.getElementById('temp-edit-id').value;
    const body = {
        csrf_token: CSRF,
        tipo_temporada: document.getElementById('temp-tipo').value,
        fecha_inicio: document.getElementById('temp-inicio').value,
        fecha_fin: document.getElementById('temp-fin').value,
        descripcion: document.getElementById('temp-desc').value
    };
    if (!body.fecha_inicio || !body.fecha_fin) return alert('Las fechas son obligatorias');
    if (id) { body._method = 'PUT'; body.id = id; }

    const res = await fetch(API_TEMPS, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(body) });
    const d = await res.json();
    if (d.exito) { cerrarModalTemp(); cargarTemporadas(); } else alert(d.error);
}

async function eliminarTemp(id) {
    if (!confirm('¿Eliminar este rango de temporada?')) return;
    const res = await fetch(API_TEMPS, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ csrf_token: CSRF, _method: 'DELETE', id }) });
    const d = await res.json();
    if (d.exito) cargarTemporadas(); else alert(d.error);
}

// ===================== TARIFAS POR TIPO =====================
async function cargarTarifasTipo(tipoId) {
    const container = document.getElementById('form-tarifas-container');
    if (!tipoId) { container.classList.add('hidden'); return; }
    container.classList.remove('hidden');
    document.getElementById('tf-tipo-id').value = tipoId;

    const res = await fetch(API_TARIFAS + '?tipo_habitacion_id=' + tipoId);
    const d = await res.json();
    if (d.exito && d.tarifa) {
        document.getElementById('tf-baja').value = d.tarifa.precio_baja || '';
        document.getElementById('tf-regular').value = d.tarifa.precio_regular || '';
        document.getElementById('tf-alta').value = d.tarifa.precio_alta || '';
        document.getElementById('tf-manual').value = d.tarifa.precio_manual || '';
        document.getElementById('tf-manual-switch').checked = d.tarifa.tarifa_manual_activa == 1;
    } else {
        document.getElementById('tf-baja').value = '';
        document.getElementById('tf-regular').value = '';
        document.getElementById('tf-alta').value = '';
        document.getElementById('tf-manual').value = '';
        document.getElementById('tf-manual-switch').checked = false;
    }
}

async function guardarTarifas() {
    const btn = document.getElementById('btn-guardar-tarifas');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
    const body = {
        csrf_token: CSRF,
        tipo_habitacion_id: document.getElementById('tf-tipo-id').value,
        precio_baja: document.getElementById('tf-baja').value,
        precio_regular: document.getElementById('tf-regular').value,
        precio_alta: document.getElementById('tf-alta').value,
        precio_manual: document.getElementById('tf-manual').value,
        tarifa_manual_activa: document.getElementById('tf-manual-switch').checked ? 1 : 0
    };
    const res = await fetch(API_TARIFAS, { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(body) });
    const d = await res.json();
    if (d.exito) {
        btn.innerHTML = '<i class="fa-solid fa-check"></i> Tarifas Guardadas';
        btn.classList.replace('bg-admin-accent', 'bg-green-600');
        setTimeout(() => { btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar Tarifas'; btn.classList.replace('bg-green-600', 'bg-admin-accent'); }, 2000);
    } else { alert(d.error); btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar Tarifas'; }
}

// Initial load
document.addEventListener('DOMContentLoaded', () => {
    cargarTipos();
    cargarTemporadas();
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
