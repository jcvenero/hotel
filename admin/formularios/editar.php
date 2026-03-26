<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Helpers.php';

if (!Auth::check() || !in_array($_SESSION['user_rol'], ['super_admin', 'admin'])) {
    header('Location: /hotel/admin/login.php'); exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pageTitle = $id ? 'Editar Formulario' : 'Nuevo Formulario';
$activeMenu = 'formularios';
require_once __DIR__ . '/../includes/header.php';
?>

<div x-data="formularioEditor()" class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="index.php" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg w-9 h-9 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-800" x-text="id ? 'Editar Formulario' : 'Nuevo Formulario'"></h2>
        </div>
        <button @click="save()" class="bg-admin-accent hover:opacity-90 text-white px-6 py-2 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
        </button>
    </div>

    <div class="grid grid-cols-3 gap-6">
        <!-- Panel Izquierdo: Configuración General -->
        <div class="col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 italic">
                    <i class="fa-solid fa-gear text-blue-500"></i> Ajustes Básicos
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Nombre del Formulario</label>
                        <input type="text" x-model="form.nombre" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:border-admin-accent outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Tipo</label>
                        <select x-model="form.tipo" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:border-admin-accent outline-none bg-white">
                            <option value="custom">Personalizado / Generico</option>
                            <option value="contacto">Contacto Oficial</option>
                            <option value="reserva">Reserva Oficial</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Email de Notificación</label>
                        <input type="email" x-model="form.email_notificacion" placeholder="admin@hotel.com" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:border-admin-accent outline-none">
                    </div>
                    <div class="flex items-center gap-3 py-2">
                        <input type="checkbox" x-model="form.activo" class="w-4 h-4 accent-admin-accent">
                        <label class="text-sm font-medium text-gray-700">Formulario Activo</label>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 italic">
                    <i class="fa-solid fa-bullhorn text-emerald-500"></i> Post-Envío
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Mensaje de Éxito</label>
                        <textarea x-model="form.mensaje_exito" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:border-admin-accent outline-none h-20 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Redirigir a (URL)</label>
                        <input type="text" x-model="form.redirigir_a" placeholder="/gracias" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:border-admin-accent outline-none">
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Constructor de Campos -->
        <div class="col-span-2">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 min-h-[500px]">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2 italic">
                        <i class="fa-solid fa-list-check text-indigo-500"></i> Estructura de Campos
                    </h3>
                    <button @click="addField()" class="text-sm bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-bold transition-all">
                        <i class="fa-solid fa-plus mr-1"></i> Añadir Campo
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(campo, index) in form.campos" :key="index">
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 relative group">
                            <div class="grid grid-cols-12 gap-4">
                                <!-- Tipo y Requerido -->
                                <div class="col-span-3">
                                    <label class="block text-[10px] font-bold text-gray-400 mb-1">TIPO</label>
                                    <select x-model="campo.tipo_campo" class="w-full p-1.5 border border-gray-300 rounded text-xs outline-none bg-white">
                                        <option value="texto">Texto Corto</option>
                                        <option value="email">Email</option>
                                        <option value="telefono">Teléfono</option>
                                        <option value="textarea">Área de Texto</option>
                                        <option value="select">Selección (Select)</option>
                                        <option value="checkbox">Casilla (Checkbox)</option>
                                        <option value="fecha">Fecha</option>
                                    </select>
                                    <label class="flex items-center gap-2 mt-2 cursor-pointer">
                                        <input type="checkbox" x-model="campo.requerido" class="w-3 h-3 accent-blue-600">
                                        <span class="text-[10px] font-bold text-gray-500 uppercase">Requerido</span>
                                    </label>
                                </div>
                                
                                <!-- Labels bilingües -->
                                <div class="col-span-4">
                                    <label class="block text-[10px] font-bold text-gray-400 mb-1">LABEL (ES)</label>
                                    <input type="text" x-model="campo.label" class="w-full p-1.5 border border-gray-300 rounded text-xs outline-none mb-2" placeholder="Ej: Tu Nombre">
                                    <label class="block text-[10px] font-bold text-gray-400 mb-1">PLACEHOLDER</label>
                                    <input type="text" x-model="campo.placeholder" class="w-full p-1.5 border border-gray-300 rounded text-xs outline-none" placeholder="Ej: Pedro Perez">
                                </div>

                                <div class="col-span-4 border-l border-gray-200 pl-4">
                                    <label class="block text-[10px] font-bold text-blue-400 mb-1 italic">LABEL (EN)</label>
                                    <input type="text" x-model="campo.label_en" class="w-full p-1.5 border border-blue-100 bg-blue-50/30 rounded text-xs outline-none" placeholder="Ej: Your Name">
                                    
                                    <template x-if="['select'].includes(campo.tipo_campo)">
                                        <div class="mt-2">
                                            <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">Opciones (comma sep)</label>
                                            <input type="text" x-model="campo.opciones" class="w-full p-1.5 border border-gray-300 rounded text-xs" placeholder="Op 1, Op 2, Op 3">
                                        </div>
                                    </template>
                                </div>

                                <!-- Botones de Orden/Eliminar -->
                                <div class="col-span-1 flex flex-col items-center justify-between py-1">
                                    <button @click="removeField(index)" class="text-red-300 hover:text-red-600 transition-colors">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                    <div class="flex flex-col gap-1 text-[10px] text-gray-300">
                                        <button @click="moveUp(index)" :disabled="index === 0" class="hover:text-admin-accent disabled:opacity-30"><i class="fa-solid fa-chevron-up"></i></button>
                                        <button @click="moveDown(index)" :disabled="index === form.campos.length - 1" class="hover:text-admin-accent disabled:opacity-30"><i class="fa-solid fa-chevron-down"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div x-show="form.campos.length === 0" class="border-2 border-dashed border-gray-200 rounded-xl p-10 text-center text-gray-400 italic">
                        No hay campos. Usa el botón superior para añadir uno.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function formularioEditor() {
    return {
        id: <?= $id ?>,
        form: {
            nombre: '',
            tipo: 'custom',
            email_notificacion: '',
            redirigir_a: '',
            mensaje_exito: 'El formulario se envió correctamente.',
            activo: 1,
            campos: []
        },
        async init() {
            if (this.id) {
                const res = await fetch(`/hotel/api/formularios/formularios.php?id=${this.id}`);
                const data = await res.json();
                if (data.exito) {
                    this.form = data.formulario;
                    // Normalizar opciones de JSON a string si es necesario para el editor
                    this.form.campos.forEach(c => {
                        if (c.opciones && typeof c.opciones === 'object') {
                            c.opciones = c.opciones.join(', ');
                        }
                    });
                }
            }
        },
        addField() {
            this.form.campos.push({
                tipo_campo: 'texto',
                label: '',
                label_en: '',
                placeholder: '',
                requerido: 1,
                opciones: '',
                orden: this.form.campos.length
            });
        },
        removeField(index) {
            this.form.campos.splice(index, 1);
        },
        moveUp(index) {
            if (index > 0) {
                const item = this.form.campos.splice(index, 1)[0];
                this.form.campos.splice(index - 1, 0, item);
            }
        },
        moveDown(index) {
            if (index < this.form.campos.length - 1) {
                const item = this.form.campos.splice(index, 1)[0];
                this.form.campos.splice(index + 1, 0, item);
            }
        },
        async save() {
            const payload = {
                ...this.form,
                csrf_token: '<?= CSRF::generateToken() ?>',
                id: this.id || null
            };
            
            // Procesar opciones como array
            payload.campos.forEach(c => {
                if (typeof c.opciones === 'string' && c.opciones.trim()) {
                    c.opciones = c.opciones.split(',').map(o => o.trim());
                }
            });

            try {
                const res = await fetch('/hotel/api/formularios/formularios.php', {
                    method: 'POST',
                    body: JSON.stringify(payload),
                    headers: { 'Content-Type': 'application/json' }
                });
                const data = await res.json();
                if (data.exito) {
                    window.location.href = 'index.php';
                } else alert(data.error);
            } catch(e) { alert('Error al guardar'); }
        }
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
