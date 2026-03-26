<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/Helpers.php';

if (!Auth::check() || !in_array($_SESSION['user_rol'], ['super_admin', 'admin', 'recepcionista'])) {
    header('Location: /hotel/admin/login.php'); exit;
}

$formulario_id = (int)($_GET['formulario_id'] ?? 0);
$pageTitle = 'Buzón de Respuestas';
$activeMenu = 'buzon';
require_once __DIR__ . '/../includes/header.php';
?>

<div x-data="respuestasBuzon()" class="space-y-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Buzón / Respuestas</h2>
            <p class="text-xs text-gray-500 mt-1">Gestión de mensajes y solicitudes recibidas por los formularios.</p>
        </div>
        <div class="flex items-center gap-2">
            <select x-model="filtroForm" @change="cargar()" class="bg-white border rounded-lg px-3 py-1.5 text-sm outline-none focus:border-admin-accent shadow-sm">
                <option value="0">Todos los formularios</option>
                <template x-for="f in formularios" :key="f.id">
                    <option :value="f.id" x-text="f.nombre"></option>
                </template>
            </select>
            <button @click="cargar()" class="bg-white text-gray-500 hover:text-indigo-600 p-2 rounded-lg border shadow-sm transition-colors">
                <i class="fa-solid fa-rotate"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6">
        <!-- LISTA DE RESPUESTAS (IZQUIERDA) -->
        <div class="col-span-12 lg:col-span-5 space-y-3 max-h-[700px] overflow-y-auto pr-2 custom-scrollbar">
            <template x-for="r in respuestas" :key="r.id">
                <div @click="verDetalle(r)" 
                     class="group bg-white p-4 rounded-xl border border-gray-200 cursor-pointer transition-all hover:bg-indigo-50/30 hover:shadow-md"
                     :class="{'border-l-4 border-l-indigo-600 shadow-sm': seleccionada && seleccionada.id === r.id, 'opacity-70 bg-gray-50/50': r.leida}">
                    
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400" x-text="r.formulario_nombre"></span>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] text-gray-400" x-text="formatearFecha(r.fecha_creacion)"></span>
                            <template x-if="!r.leida">
                                <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                            </template>
                        </div>
                    </div>
                    
                    <div class="font-bold text-gray-800 group-hover:text-indigo-700 transition-colors" x-text="r.datos.nombre || r.datos.email || 'Anónimo'"></div>
                    <p class="text-[12px] text-gray-500 line-clamp-2 mt-1" x-text="r.datos.mensaje || r.datos.asunto || r.datos.comentarios || 'Sin vista previa...'"></p>
                    
                    <div class="flex items-center gap-2 mt-3">
                        <span x-show="r.respondida" class="text-[10px] font-bold px-2 py-0.5 bg-green-100 text-green-700 rounded-full">
                            <i class="fa-solid fa-reply mr-1"></i> Respondido
                        </span>
                        <span x-show="r.tipo_entidad_origen === 'habitacion'" class="text-[10px] font-bold px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">
                            <i class="fa-solid fa-bed mr-1"></i> Reserva
                        </span>
                    </div>
                </div>
            </template>
            <div x-show="respuestas.length === 0" class="text-center py-20 text-gray-400 bg-white rounded-xl border border-dashed border-gray-200">
                <i class="fa-solid fa-inbox text-3xl mb-3 opacity-30"></i>
                <p>No se encontraron mensajes.</p>
            </div>
        </div>

        <!-- DETALLE DE RESPUESTA (DERECHA) -->
        <div class="col-span-12 lg:col-span-7">
            <template x-if="seleccionada">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden sticky top-6">
                    <div class="bg-gray-50 p-6 border-b border-gray-100">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800" x-text="seleccionada.datos.nombre || 'Sin Nombre'"></h3>
                                <p class="text-sm text-gray-500" x-text="seleccionada.datos.email"></p>
                            </div>
                            <div class="flex gap-2">
                                <button @click="borrarRespuesta(seleccionada.id)" class="text-gray-400 hover:text-red-500 p-2"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-3">
                            <div class="bg-white px-3 py-1.5 rounded-lg border text-[11px] shadow-xs">
                                <span class="text-gray-400 font-bold uppercase tracking-tighter block">Enviado desde</span>
                                <span class="text-gray-700" x-text="seleccionada.pagina_origen || 'Directo'"></span>
                            </div>
                            <div class="bg-white px-3 py-1.5 rounded-lg border text-[11px] shadow-xs">
                                <span class="text-gray-400 font-bold uppercase tracking-tighter block">IP Cliente</span>
                                <span class="text-gray-700" x-text="seleccionada.ip_cliente"></span>
                            </div>
                            <div class="bg-white px-3 py-1.5 rounded-lg border text-[11px] shadow-xs">
                                <span class="text-gray-400 font-bold uppercase tracking-tighter block">Idioma</span>
                                <span class="text-gray-700" x-text="seleccionada.idioma_origen === 'en' ? '🇺🇸 Inglés' : '🇪🇸 Español'"></span>
                            </div>
                        </div>
                    </div>

                    <div class="p-8 space-y-6">
                        <!-- Campos del formulario -->
                        <div class="grid grid-cols-2 gap-4 border-b pb-6">
                            <template x-for="(val, key) in seleccionada.datos" :key="key">
                                <div class="col-span-2 sm:col-span-1" x-show="!['nombre','email'].includes(key)">
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest" x-text="key.replace('_',' ')"></label>
                                    <div class="text-sm text-gray-800 font-medium" x-text="val"></div>
                                </div>
                            </template>
                        </div>

                        <!-- Respuesta Admin -->
                        <div class="bg-indigo-50/50 p-6 rounded-xl border border-indigo-100">
                            <h4 class="font-bold text-indigo-900 text-sm mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-comment-dots"></i> Seguimiento Interno / Respuesta
                            </h4>
                            <div x-show="seleccionada.respondida">
                                <div class="text-sm text-gray-700 whitespace-pre-wrap mb-4 italic p-3 bg-white rounded border border-indigo-100" x-text="seleccionada.respuesta_admin"></div>
                                <div class="text-[10px] text-indigo-500 font-bold uppercase tracking-widest flex justify-between">
                                    <span x-text="'Respondido por: ' + (seleccionada.respondida_por || 'Sistema')"></span>
                                    <span x-text="formatearFechaFull(seleccionada.fecha_respuesta)"></span>
                                </div>
                            </div>
                            
                            <div x-show="!seleccionada.respondida">
                                <textarea x-model="respuestaTexto" placeholder="Escribe aquí tu respuesta o notas internas..." 
                                          class="w-full h-32 p-3 border border-indigo-200 rounded-lg text-sm bg-white outline-none focus:ring-2 focus:ring-indigo-500/20"></textarea>
                                <div class="mt-3 flex justify-end">
                                    <button @click="enviarRespuesta()" :disabled="!respuestaTexto.trim() || enviando"
                                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all disabled:opacity-50">
                                        <i class="fa-solid fa-paper-plane mr-2" :class="enviando ? 'fa-spinner fa-spin' : ''"></i> Registrar Respuesta
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <div x-show="!seleccionada" class="h-full flex flex-col items-center justify-center p-20 text-center bg-gray-50 border border-dashed border-gray-200 rounded-xl text-gray-400">
                <i class="fa-solid fa-envelope-open text-5xl mb-4 opacity-20"></i>
                <h3 class="text-lg font-bold uppercase tracking-widest mb-2">Buzón de Mensajes</h3>
                <p class="text-sm max-w-xs">Selecciona un mensaje de la izquierda para ver los detalles, datos de contexto y dar respuesta.</p>
            </div>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>

<script>
function respuestasBuzon() {
    return {
        formularios: [],
        respuestas: [],
        seleccionada: null,
        filtroForm: <?= $formulario_id ?: 0 ?>,
        respuestaTexto: '',
        enviando: false,
        async cargar() {
            // Cargar lista de formularios para el filtro si es primera vez
            if (this.formularios.length === 0) {
                const fRes = await fetch('/hotel/api/formularios/formularios.php');
                const fData = await fRes.json();
                if (fData.exito) this.formularios = fData.formularios;
            }
            
            const rRes = await fetch(`/hotel/api/formularios/respuestas.php?formulario_id=${this.filtroForm}`);
            const rData = await rRes.json();
            if (rData.exito) this.respuestas = rData.respuestas;
            
            // Si el filtro cambió y la seleccionada ya no aplica, quitarla
            if (this.seleccionada && this.filtroForm != 0 && this.seleccionada.formulario_id != this.filtroForm) {
                this.seleccionada = null;
            }
        },
        async verDetalle(r) {
            this.seleccionada = r;
            this.respuestaTexto = '';
            // Marcar como leída visualmente sin esperar recarga
            r.leida = 1;
            // No necesitamos llamar explícitamente a marcar_leida porque la API lo hace al obtener detalle por ID
            const res = await fetch(`/hotel/api/formularios/respuestas.php?id=${r.id}`);
            const data = await res.json();
            if (data.exito) this.seleccionada = data.respuesta;
        },
        async enviarRespuesta() {
            this.enviando = true;
            try {
                const res = await fetch('/hotel/api/formularios/respuestas.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        csrf_token: '<?= CSRF::generateToken() ?>',
                        id: this.seleccionada.id,
                        accion: 'responder',
                        mensaje: this.respuestaTexto
                    })
                });
                const data = await res.json();
                if (data.exito) {
                    this.seleccionada.respondida = 1;
                    this.seleccionada.respuesta_admin = this.respuestaTexto;
                    this.seleccionada.respondida_por = '<?= $_SESSION['user_nombre'] ?>';
                    this.seleccionada.fecha_respuesta = new Date().toISOString();
                } else alert(data.error);
            } catch(e) { alert('Error al procesar'); }
            this.enviando = false;
        },
        async borrarRespuesta(id) {
            if (!confirm('¿Seguro que deseas eliminar esta respuesta?')) return;
            const res = await fetch(`/hotel/api/formularios/respuestas.php?id=${id}`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ csrf_token: '<?= CSRF::generateToken() ?>' })
            });
            if ((await res.json()).exito) {
                this.seleccionada = null;
                this.cargar();
            }
        },
        formatearFecha(f) {
            const date = new Date(f);
            return date.toLocaleDateString('es-ES', { day:'2-digit', month:'2-digit', year:'numeric' }) + ' ' + date.toLocaleTimeString('es-ES', { hour:'2-digit', minute:'2-digit' });
        },
        formatearFechaFull(f) {
            if (!f) return '';
            return new Date(f).toLocaleString('es-ES');
        },
        init() { this.cargar(); }
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
