<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/CSRF.php';
require_once __DIR__ . '/../../includes/Helpers.php';

if (!Auth::check()) { header('Location: /hotel/admin/login.php'); exit; }

$db = Database::getInstance();
$rolUsuario = $_SESSION['user_rol'] ?? 'editor';
$esAdmin = in_array($rolUsuario, ['super_admin', 'admin']);

$habitacion_id = (int)($_GET['id'] ?? 0);
$esNuevo = isset($_GET['nuevo']) || !$habitacion_id;

$hab = $idioma_es = $idioma_en = $geo = null;
$comodidades = $amenities = $camas = $imagenes = $faqs_es = $faqs_en = [];

if ($habitacion_id) {
    $s = $db->prepare("SELECT * FROM habitaciones WHERE id = ?"); $s->execute([$habitacion_id]); $hab = $s->fetch();
    if ($hab) {
        $s = $db->prepare("SELECT * FROM habitaciones_idiomas WHERE habitacion_id = ? AND idioma = 'es'"); $s->execute([$habitacion_id]); $idioma_es = $s->fetch();
        $s = $db->prepare("SELECT * FROM habitaciones_idiomas WHERE habitacion_id = ? AND idioma = 'en'"); $s->execute([$habitacion_id]); $idioma_en = $s->fetch();
        $s = $db->prepare("SELECT * FROM seo_geo WHERE tipo = 'habitacion' AND tipo_id = ? AND idioma = 'es'"); $s->execute([$habitacion_id]); $geo = $s->fetch();
        $s = $db->prepare("SELECT comodidades FROM habitacion_comodidades WHERE habitacion_id = ? AND idioma = 'es'"); $s->execute([$habitacion_id]); $r = $s->fetch(); $comodidades = $r ? json_decode($r['comodidades'], true) : [];
        $s = $db->prepare("SELECT amenities FROM habitacion_amenities WHERE habitacion_id = ? AND idioma = 'es'"); $s->execute([$habitacion_id]); $r = $s->fetch(); $amenities = $r ? json_decode($r['amenities'], true) : [];
        $s = $db->prepare("SELECT camas FROM habitacion_configuracion_camas WHERE habitacion_id = ?"); $s->execute([$habitacion_id]); $r = $s->fetch(); $camas = $r ? json_decode($r['camas'], true) : [];
        $s = $db->prepare("SELECT hi.*, i.ruta_webp, i.ruta_thumbnail, i.alt_text, i.nombre_archivo FROM habitacion_imagenes hi JOIN imagenes i ON hi.imagen_id = i.id WHERE hi.habitacion_id = ? ORDER BY hi.es_principal DESC, hi.orden ASC"); $s->execute([$habitacion_id]); $imagenes = $s->fetchAll();
        $s = $db->prepare("SELECT * FROM habitacion_faqs WHERE habitacion_id = ? AND idioma = 'es' ORDER BY orden ASC"); $s->execute([$habitacion_id]); $faqs_es = $s->fetchAll();
        $s = $db->prepare("SELECT * FROM habitacion_faqs WHERE habitacion_id = ? AND idioma = 'en' ORDER BY orden ASC"); $s->execute([$habitacion_id]); $faqs_en = $s->fetchAll();
    }
}

$tipos = $db->query("SELECT * FROM tipos_habitacion WHERE activo = 1 ORDER BY nombre")->fetchAll();
$pageTitle = $esNuevo ? 'Nueva Habitación' : 'Editar Habitación';
$activeMenu = 'habitaciones';
require_once __DIR__ . '/../includes/header.php';
?>

<style>
    .section-card{background:white;border-radius:12px;border:1px solid #e5e7eb;overflow:hidden;margin-bottom:16px}
    .section-header{padding:16px 20px;cursor:pointer;display:flex;align-items:center;justify-content:space-between;background:#f9fafb;border-bottom:1px solid #e5e7eb;transition:background .2s}
    .section-header:hover{background:#f1f5f9}
    .section-header h3{font-size:15px;font-weight:700;color:#1e293b;display:flex;align-items:center;gap:10px}
    .section-body{padding:20px}
    .section-body.collapsed{display:none}
    .ai-btn{background:linear-gradient(135deg,#8b5cf6,#6366f1);color:#fff;border:none;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .2s}
    .ai-btn:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(99,102,241,.35)}
    .ai-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}
    .repeater-item{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px;display:flex;align-items:center;gap:10px;margin-bottom:8px}
    .repeater-item input,.repeater-item select{flex:1;padding:8px 12px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;outline:none}
    .repeater-item input:focus,.repeater-item select:focus{border-color:#2563eb}
    .btn-remove{background:#fee2e2;color:#dc2626;border:none;width:30px;height:30px;border-radius:6px;cursor:pointer;font-size:12px}
    .btn-remove:hover{background:#fca5a5}
    .role-badge{position:absolute;top:12px;right:20px;background:#fef3c7;color:#92400e;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px;z-index:2}
    .lang-en{border-color:#93c5fd!important;background-color:#eff6ff!important}
    .translate-btn{font-size:11px;background:#dbeafe;color:#1e40af;border:none;padding:4px 12px;border-radius:6px;cursor:pointer;font-weight:600;transition:all .2s}
    .translate-btn:hover{background:#bfdbfe}
    .img-card{position:relative;border-radius:10px;overflow:hidden;border:2px solid #e5e7eb;transition:all .2s;cursor:default}
    .img-card:hover{border-color:#93c5fd}
    .img-card.principal{border-color:#2563eb;box-shadow:0 0 0 2px rgba(37,99,235,0.2)}
    .img-card .img-badge{position:absolute;bottom:4px;left:4px;background:#2563eb;color:#fff;font-size:9px;padding:2px 8px;border-radius:4px;font-weight:700}
    .img-card .img-delete{position:absolute;top:4px;right:4px;background:#ef4444;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:10px;cursor:pointer;opacity:0;transition:opacity .2s}
    .img-card:hover .img-delete{opacity:1}
</style>

<!-- HEADER BAR -->
<div class="flex items-center justify-between mb-5">
    <div class="flex items-center gap-3">
        <a href="/hotel/admin/habitaciones/index.php" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg w-9 h-9 flex items-center justify-center transition-colors"><i class="fa-solid fa-arrow-left"></i></a>
        <h2 class="text-xl font-bold text-gray-800"><?= $esNuevo ? 'Nueva Habitación' : 'Editar: ' . htmlspecialchars($idioma_es['nombre'] ?? '') ?></h2>
    </div>
    <div class="flex items-center gap-3">
        <!-- LANGUAGE TOGGLE -->
        <div class="flex bg-gray-200 rounded-lg p-1 border border-gray-300">
            <button onclick="switchLang('es')" id="btnLangES" class="px-4 py-1 text-sm font-medium rounded-md bg-white shadow-sm transition-all focus:outline-none flex items-center gap-1">🇪🇸 ES</button>
            <button onclick="switchLang('en')" id="btnLangEN" class="px-4 py-1 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all focus:outline-none flex items-center gap-1">🇺🇸 EN</button>
        </div>
        <button onclick="guardarTodo()" id="btn-guardar-global" class="bg-admin-accent hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow transition-colors flex items-center gap-2">
            <i class="fa-solid fa-floppy-disk"></i> Guardar Todo
        </button>
    </div>
</div>

<form id="formHabitacion" autocomplete="off">
<input type="hidden" id="hab-id" value="<?= $habitacion_id ?>">

<!-- ===== 1. DATOS BÁSICOS ===== -->
<div class="section-card">
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-circle-info text-blue-500"></i> Datos Básicos</h3>
        <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
    </div>
    <div class="section-body">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Nº Habitación</label>
                <input type="number" id="f-numero" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-admin-accent" value="<?= $hab['numero_habitacion'] ?? '' ?>">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Tipo de Habitación *</label>
                <select id="f-tipo" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-admin-accent bg-white">
                    <option value="">-- Seleccionar --</option>
                    <?php foreach ($tipos as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= ($hab['tipo_habitacion_id'] ?? '') == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Estado</label>
                <select id="f-estado" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-admin-accent bg-white">
                    <option value="disponible" <?= ($hab['estado'] ?? '') === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                    <option value="ocupada" <?= ($hab['estado'] ?? '') === 'ocupada' ? 'selected' : '' ?>>Ocupada</option>
                    <option value="mantenimiento" <?= ($hab['estado'] ?? '') === 'mantenimiento' ? 'selected' : '' ?>>Mantenimiento</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">¿Activa?</label>
                <label class="flex items-center gap-2 mt-2 cursor-pointer">
                    <input type="checkbox" id="f-activa" <?= ($hab['activa'] ?? 1) ? 'checked' : '' ?> class="w-4 h-4 accent-blue-600">
                    <span class="text-sm text-gray-600">Visible en web</span>
                </label>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div><label class="block text-xs font-bold text-gray-600 mb-1">Capacidad Huéspedes</label><input type="number" id="f-capacidad" min="1" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-admin-accent" value="<?= $hab['capacidad_huespedes'] ?? 2 ?>"></div>
            <div><label class="block text-xs font-bold text-gray-600 mb-1">Nº Camas</label><input type="number" id="f-camas-num" min="1" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-admin-accent" value="<?= $hab['num_camas'] ?? 1 ?>"></div>
        </div>
    </div>
</div>

<!-- ===== 2. CONTENIDO BILINGÜE ===== -->
<div class="section-card">
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-language text-indigo-500"></i> Contenido</h3>
        <div class="flex items-center gap-2">
            <button type="button" onclick="event.stopPropagation(); iaGenerar('descripcion')" class="ai-btn"><i class="fa-solid fa-wand-magic-sparkles"></i> Generar con IA</button>
            <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
        </div>
    </div>
    <div class="section-body">
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Nombre Comercial <span class="lang-indicator text-blue-500">(ES)</span></label>
                <input type="text" id="f-nombre-es" class="lang-field lang-es w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-admin-accent" value="<?= htmlspecialchars($idioma_es['nombre'] ?? '') ?>">
                <div class="lang-field lang-en hidden flex gap-2 mt-1">
                    <input type="text" id="f-nombre-en" class="lang-en w-full p-2.5 border rounded-lg text-sm outline-none focus:border-admin-accent" value="<?= htmlspecialchars($idioma_en['nombre'] ?? '') ?>" placeholder="Room Name in English">
                    <button type="button" onclick="iaTraducir('nombre')" class="translate-btn whitespace-nowrap"><i class="fa-solid fa-globe mr-1"></i>Traducir del ES</button>
                </div>
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-xs font-bold text-gray-600">Descripción <span class="lang-indicator text-blue-500">(ES)</span></label>
                    <button type="button" onclick="iaGenerar('descripcion')" class="ai-btn-es ai-btn text-[11px] py-1"><i class="fa-solid fa-wand-magic-sparkles"></i> Generar</button>
                </div>
                <textarea id="f-desc-es" class="lang-field lang-es w-full p-3 border border-gray-300 rounded-lg text-sm outline-none focus:border-admin-accent h-32 resize-y"><?= htmlspecialchars($idioma_es['descripcion'] ?? '') ?></textarea>
                <div class="lang-field lang-en hidden relative mt-1">
                    <textarea id="f-desc-en" class="lang-en w-full p-3 border rounded-lg text-sm outline-none focus:border-admin-accent h-32 resize-y" placeholder="Description in English..."><?= htmlspecialchars($idioma_en['descripcion'] ?? '') ?></textarea>
                    <button type="button" onclick="iaTraducir('descripcion')" class="translate-btn absolute bottom-3 right-3"><i class="fa-solid fa-globe mr-1"></i>Traducir del ES</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== 3. COMODIDADES ===== -->
<div class="section-card">
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-wifi text-emerald-500"></i> Comodidades</h3>
        <div class="flex items-center gap-2">
            <button type="button" onclick="event.stopPropagation(); iaGenerar('comodidades')" class="ai-btn"><i class="fa-solid fa-wand-magic-sparkles"></i> Sugerir IA</button>
            <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
        </div>
    </div>
    <div class="section-body collapsed">
        <p class="text-xs text-gray-400 mb-3">WiFi, Aire Acondicionado, TV, Minibar, etc.</p>
        <div id="comodidades-list"></div>
        <button type="button" onclick="addRepeater('comodidades')" class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-bold"><i class="fa-solid fa-plus"></i> Agregar Comodidad</button>
    </div>
</div>

<!-- ===== 4. AMENITIES ===== -->
<div class="section-card">
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-concierge-bell text-amber-500"></i> Amenities</h3>
        <div class="flex items-center gap-2">
            <button type="button" onclick="event.stopPropagation(); iaGenerar('amenities')" class="ai-btn"><i class="fa-solid fa-wand-magic-sparkles"></i> Sugerir IA</button>
            <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
        </div>
    </div>
    <div class="section-body collapsed">
        <p class="text-xs text-gray-400 mb-3">Desayuno, Parking, Room Service, Spa, etc.</p>
        <div id="amenities-list"></div>
        <button type="button" onclick="addRepeater('amenities')" class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-bold"><i class="fa-solid fa-plus"></i> Agregar Amenity</button>
    </div>
</div>

<!-- ===== 5. CAMAS ===== -->
<div class="section-card">
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-bed text-purple-500"></i> Distribución de Camas</h3>
        <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
    </div>
    <div class="section-body collapsed">
        <div id="camas-list"></div>
        <button type="button" onclick="addCama()" class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-bold"><i class="fa-solid fa-plus"></i> Agregar Cama</button>
    </div>
</div>

<!-- ===== 6. IMÁGENES ===== -->
<div class="section-card">
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-images text-pink-500"></i> Imágenes</h3>
        <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
    </div>
    <div class="section-body collapsed">
        <!-- Imagen Principal -->
        <div class="mb-6">
            <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2"><i class="fa-solid fa-star text-yellow-500"></i> Imagen Principal <span class="text-[10px] text-gray-400 font-normal">(Tarjetas, listados y redes sociales)</span></h4>
            <div class="flex items-start gap-4">
                <div id="img-principal-preview" class="w-48 h-32 bg-gray-100 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center overflow-hidden">
                    <?php
                    $imgPrincipal = null;
                    foreach ($imagenes as $img) { if ($img['es_principal']) { $imgPrincipal = $img; break; } }
                    if ($imgPrincipal): ?>
                        <img src="<?= $imgPrincipal['ruta_webp'] ?: $imgPrincipal['ruta_thumbnail'] ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <span class="text-xs text-gray-400">Sin imagen</span>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col gap-2">
                    <input type="file" id="upload-principal" class="hidden" accept=".jpg,.jpeg,.png,.webp">
                    <button type="button" onclick="document.getElementById('upload-principal').click()" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-2 rounded-lg font-medium shadow-sm flex items-center gap-2"><i class="fa-solid fa-upload"></i> Subir Imagen Principal</button>
                    <button type="button" onclick="quitarPrincipal()" class="text-red-500 hover:text-red-700 text-xs font-bold"><i class="fa-solid fa-trash"></i> Quitar</button>
                    <input type="hidden" id="f-img-principal-id" value="<?= $imgPrincipal['imagen_id'] ?? '' ?>">
                </div>
            </div>
        </div>

        <!-- Galería -->
        <div>
            <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2"><i class="fa-solid fa-grip text-blue-500"></i> Galería de Imágenes <span class="text-[10px] text-gray-400 font-normal">(Fotos adicionales de la habitación)</span></h4>
            <div id="galeria-grid" class="grid grid-cols-5 gap-3 mb-3">
                <?php foreach ($imagenes as $img): if ($img['es_principal']) continue; ?>
                <div class="img-card" data-img-id="<?= $img['imagen_id'] ?>">
                    <img src="<?= $img['ruta_thumbnail'] ?: $img['ruta_webp'] ?>" alt="<?= htmlspecialchars($img['alt_text']) ?>" class="w-full h-24 object-cover">
                    <button type="button" onclick="removeGaleriaImg(this)" class="img-delete"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="flex gap-2">
                <input type="file" id="upload-galeria" class="hidden" multiple accept=".jpg,.jpeg,.png,.webp">
                <button type="button" onclick="document.getElementById('upload-galeria').click()" class="text-sm text-blue-600 hover:text-blue-800 font-bold flex items-center gap-1"><i class="fa-solid fa-upload"></i> Subir a Galería</button>
            </div>
            <!-- Upload Progress -->
            <div id="img-upload-progress" class="hidden mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="text-xs font-bold text-blue-700 mb-1">Subiendo... <span id="img-up-status">0/0</span></div>
                <div class="w-full bg-gray-200 rounded-full h-2"><div id="img-up-bar" class="bg-blue-600 h-2 rounded-full transition-all" style="width:0%"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- ===== 7. FAQs ===== -->
<div class="section-card">
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-circle-question text-cyan-500"></i> Preguntas Frecuentes (FAQs)</h3>
        <div class="flex items-center gap-2">
            <button type="button" onclick="event.stopPropagation(); iaGenerar('faqs')" class="ai-btn"><i class="fa-solid fa-wand-magic-sparkles"></i> Generar FAQs IA</button>
            <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
        </div>
    </div>
    <div class="section-body collapsed">
        <div id="faqs-list"></div>
        <button type="button" onclick="addFaq()" class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-bold"><i class="fa-solid fa-plus"></i> Agregar FAQ</button>
    </div>
</div>

<!-- ===== 8. SEO BÁSICO ===== -->
<div class="section-card">
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-magnifying-glass-chart text-green-600"></i> SEO Básico</h3>
        <div class="flex items-center gap-2">
            <button type="button" onclick="event.stopPropagation(); iaGenerar('seo')" class="ai-btn"><i class="fa-solid fa-wand-magic-sparkles"></i> Sugerir SEO IA</button>
            <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
        </div>
    </div>
    <div class="section-body collapsed">
        <div class="space-y-4 max-w-2xl">
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Título SEO <span class="lang-indicator text-blue-500">(ES)</span> <span id="cnt-t-es" class="font-normal text-gray-400">0/60</span></label>
                <input type="text" id="f-seo-titulo-es" maxlength="60" oninput="document.getElementById('cnt-t-es').textContent=this.value.length+'/60'" class="lang-field lang-es w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-green-500" value="<?= htmlspecialchars($idioma_es['seo_titulo'] ?? '') ?>">
                <div class="lang-field lang-en hidden mt-1 flex gap-2">
                    <input type="text" id="f-seo-titulo-en" maxlength="60" class="lang-en flex-1 p-2.5 border rounded-lg text-sm outline-none focus:border-green-500" value="<?= htmlspecialchars($idioma_en['seo_titulo'] ?? '') ?>" placeholder="SEO Title (EN)">
                    <button type="button" onclick="iaTraducirCampo('f-seo-titulo-es','f-seo-titulo-en')" class="translate-btn whitespace-nowrap">Traducir</button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Meta Descripción <span class="lang-indicator text-blue-500">(ES)</span> <span id="cnt-d-es" class="font-normal text-gray-400">0/155</span></label>
                <textarea id="f-seo-desc-es" maxlength="155" oninput="document.getElementById('cnt-d-es').textContent=this.value.length+'/155'" class="lang-field lang-es w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-green-500 h-20 resize-none"><?= htmlspecialchars($idioma_es['seo_descripcion'] ?? '') ?></textarea>
                <div class="lang-field lang-en hidden relative mt-1">
                    <textarea id="f-seo-desc-en" maxlength="155" class="lang-en w-full p-2.5 border rounded-lg text-sm outline-none focus:border-green-500 h-20 resize-none" placeholder="Meta Description (EN)"><?= htmlspecialchars($idioma_en['seo_descripcion'] ?? '') ?></textarea>
                    <button type="button" onclick="iaTraducirCampo('f-seo-desc-es','f-seo-desc-en')" class="translate-btn absolute bottom-2 right-2">Traducir</button>
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">Palabras Clave <span class="lang-indicator text-blue-500">(ES)</span></label>
                <input type="text" id="f-seo-kw-es" class="lang-field lang-es w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-green-500" placeholder="hotel, cusco, suite" value="<?= htmlspecialchars($idioma_es['seo_palabras_clave'] ?? '') ?>">
                <div class="lang-field lang-en hidden mt-1 flex gap-2">
                    <input type="text" id="f-seo-kw-en" class="lang-en flex-1 p-2.5 border rounded-lg text-sm outline-none focus:border-green-500" placeholder="Keywords (EN)" value="<?= htmlspecialchars($idioma_en['seo_palabras_clave'] ?? '') ?>">
                    <button type="button" onclick="iaTraducirCampo('f-seo-kw-es','f-seo-kw-en')" class="translate-btn whitespace-nowrap">Traducir</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($esAdmin): ?>
<!-- ===== 9. SCHEMA JSON-LD ===== -->
<div class="section-card" style="position:relative">
    <span class="role-badge"><i class="fa-solid fa-lock text-[9px] mr-1"></i> Solo Admin</span>
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-code text-orange-500"></i> Schema JSON-LD (Avanzado)</h3>
        <div class="flex items-center gap-2">
            <button type="button" onclick="event.stopPropagation(); iaGenerar('schema')" class="ai-btn"><i class="fa-solid fa-wand-magic-sparkles"></i> Generar Schema IA</button>
            <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
        </div>
    </div>
    <div class="section-body collapsed">
        <p class="text-xs text-gray-400 mb-3">Schema.org JSON-LD. Se inyectará en el &lt;head&gt; del frontend.</p>
        <textarea id="f-schema" class="w-full p-3 border border-gray-300 rounded-lg text-sm outline-none focus:border-orange-500 h-48 resize-y font-mono bg-gray-900 text-green-400" placeholder='{"@context":"https://schema.org","@type":"HotelRoom",...}'><?= htmlspecialchars($idioma_es['schema_json'] ?? '') ?></textarea>
    </div>
</div>

<!-- ===== 10. GEO ===== -->
<div class="section-card" style="position:relative">
    <span class="role-badge"><i class="fa-solid fa-lock text-[9px] mr-1"></i> Solo Admin</span>
    <div class="section-header" onclick="toggleSection(this)">
        <h3><i class="fa-solid fa-map-location-dot text-red-500"></i> GEO / Ubicación</h3>
        <i class="fa-solid fa-chevron-down text-gray-400 sect-arrow"></i>
    </div>
    <div class="section-body collapsed">
        <div class="grid grid-cols-2 gap-4">
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Latitud</label><input type="text" id="f-geo-lat" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none" value="<?= $geo['latitud'] ?? '' ?>"></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Longitud</label><input type="text" id="f-geo-lng" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none" value="<?= $geo['longitud'] ?? '' ?>"></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">País</label><input type="text" id="f-geo-pais" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none" value="<?= htmlspecialchars($geo['pais'] ?? '') ?>"></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Región</label><input type="text" id="f-geo-region" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none" value="<?= htmlspecialchars($geo['region'] ?? '') ?>"></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Ciudad</label><input type="text" id="f-geo-ciudad" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none" value="<?= htmlspecialchars($geo['ciudad'] ?? '') ?>"></div>
            <div><label class="block text-xs font-bold text-gray-500 mb-1">Keywords GEO</label><input type="text" id="f-geo-kw" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm outline-none" placeholder="cusco, plaza regocijo" value="<?= htmlspecialchars($geo['keywords_geo'] ?? '') ?>"></div>
        </div>
    </div>
</div>
<?php endif; ?>

</form>
<div class="h-20"></div>

<script>
const CSRF = '<?= CSRF::generateToken() ?>';
const HAB_ID = <?= $habitacion_id ?: 'null' ?>;
const API_HAB = '/hotel/api/habitaciones/habitaciones.php';
const API_IA = '/hotel/api/ai/generar-contenido.php';
const API_UPLOAD = '/hotel/api/galeria/subir.php';
const ES_ADMIN = <?= $esAdmin ? 'true' : 'false' ?>;
let currentLang = 'es';

// ===== LANGUAGE TOGGLE (patrón ajustes.php) =====
function switchLang(lang) {
    currentLang = lang;
    const btnEs = document.getElementById('btnLangES');
    const btnEn = document.getElementById('btnLangEN');
    if (lang === 'es') {
        btnEs.className = "px-4 py-1 text-sm font-medium rounded-md bg-white shadow-sm transition-all focus:outline-none flex items-center gap-1";
        btnEn.className = "px-4 py-1 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all focus:outline-none flex items-center gap-1";
    } else {
        btnEn.className = "px-4 py-1 text-sm font-medium rounded-md bg-blue-100 shadow-sm transition-all text-blue-800 focus:outline-none flex items-center gap-1";
        btnEs.className = "px-4 py-1 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all focus:outline-none flex items-center gap-1";
    }
    document.querySelectorAll('.lang-field').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll(`.lang-${lang}`).forEach(el => el.classList.remove('hidden'));
    // Ocultar botones IA de generación en EN
    document.querySelectorAll('.ai-btn-es').forEach(el => el.style.display = lang === 'es' ? 'inline-flex' : 'none');
    document.querySelectorAll('.lang-indicator').forEach(el => el.textContent = `(${lang.toUpperCase()})`);
}

// ===== ACCORDION =====
function toggleSection(header) {
    const body = header.nextElementSibling;
    const arrow = header.querySelector('.sect-arrow');
    body.classList.toggle('collapsed');
    if (arrow) arrow.style.transform = body.classList.contains('collapsed') ? '' : 'rotate(180deg)';
}

// ===== REPEATERS =====
const repeaterData = {
    comodidades: <?= json_encode($comodidades ?: []) ?>,
    amenities: <?= json_encode($amenities ?: []) ?>
};
function renderRepeater(type) {
    const list = document.getElementById(type + '-list');
    list.innerHTML = '';
    (repeaterData[type] || []).forEach((item, i) => {
        list.innerHTML += `<div class="repeater-item">
            <input type="text" value="${item.icono || ''}" placeholder="fa-wifi" style="max-width:100px" onchange="repeaterData['${type}'][${i}].icono=this.value" title="Icono FontAwesome">
            <input type="text" value="${item.nombre || ''}" placeholder="Nombre" onchange="repeaterData['${type}'][${i}].nombre=this.value">
            <button type="button" class="btn-remove" onclick="repeaterData['${type}'].splice(${i},1);renderRepeater('${type}')"><i class="fa-solid fa-trash"></i></button>
        </div>`;
    });
}
function addRepeater(type) { repeaterData[type].push({nombre:'',icono:''}); renderRepeater(type); }
renderRepeater('comodidades'); renderRepeater('amenities');

// ===== CAMAS =====
let camasData = <?= json_encode($camas ?: []) ?>;
function renderCamas() {
    const list = document.getElementById('camas-list'); list.innerHTML = '';
    camasData.forEach((c, i) => {
        list.innerHTML += `<div class="repeater-item">
            <select onchange="camasData[${i}].tipo=this.value" style="max-width:160px">
                <option value="individual" ${c.tipo==='individual'?'selected':''}>Individual</option>
                <option value="doble" ${c.tipo==='doble'?'selected':''}>Doble</option>
                <option value="queen" ${c.tipo==='queen'?'selected':''}>Queen</option>
                <option value="king" ${c.tipo==='king'?'selected':''}>King</option>
                <option value="litera" ${c.tipo==='litera'?'selected':''}>Litera</option>
                <option value="sofa_cama" ${c.tipo==='sofa_cama'?'selected':''}>Sofá Cama</option>
            </select>
            <input type="number" value="${c.cantidad||1}" min="1" style="max-width:80px" onchange="camasData[${i}].cantidad=parseInt(this.value)">
            <button type="button" class="btn-remove" onclick="camasData.splice(${i},1);renderCamas()"><i class="fa-solid fa-trash"></i></button>
        </div>`;
    });
}
function addCama() { camasData.push({tipo:'individual',cantidad:1}); renderCamas(); }
renderCamas();

// ===== FAQs =====
let faqsData = <?= json_encode(array_map(function($f) { return ['pregunta_es'=>$f['pregunta'],'respuesta_es'=>$f['respuesta']]; }, $faqs_es) ?: '[]') ?>;
<?php foreach ($faqs_en as $i => $fen): ?>
if(faqsData[<?=$i?>]){faqsData[<?=$i?>].pregunta_en=<?=json_encode($fen['pregunta'])?>;faqsData[<?=$i?>].respuesta_en=<?=json_encode($fen['respuesta'])?>;}
<?php endforeach; ?>
function renderFaqs() {
    const list = document.getElementById('faqs-list'); list.innerHTML = '';
    faqsData.forEach((f, i) => {
        list.innerHTML += `<div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-3">
            <div class="flex justify-between items-start mb-2"><span class="text-xs font-bold text-gray-400">FAQ #${i+1}</span><button type="button" class="btn-remove" onclick="faqsData.splice(${i},1);renderFaqs()"><i class="fa-solid fa-trash"></i></button></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-[10px] font-bold text-gray-400">Pregunta (ES)</label><input type="text" value="${f.pregunta_es||''}" class="w-full p-2 border border-gray-300 rounded text-sm mt-0.5" onchange="faqsData[${i}].pregunta_es=this.value"></div>
                <div><label class="text-[10px] font-bold text-gray-400">Question (EN)</label><input type="text" value="${f.pregunta_en||''}" class="w-full p-2 border border-gray-300 rounded text-sm mt-0.5" onchange="faqsData[${i}].pregunta_en=this.value"></div>
                <div><label class="text-[10px] font-bold text-gray-400">Respuesta (ES)</label><textarea class="w-full p-2 border border-gray-300 rounded text-sm mt-0.5 h-16 resize-none" onchange="faqsData[${i}].respuesta_es=this.value">${f.respuesta_es||''}</textarea></div>
                <div><label class="text-[10px] font-bold text-gray-400">Answer (EN)</label><textarea class="w-full p-2 border border-gray-300 rounded text-sm mt-0.5 h-16 resize-none" onchange="faqsData[${i}].respuesta_en=this.value">${f.respuesta_en||''}</textarea></div>
            </div>
        </div>`;
    });
}
function addFaq() { faqsData.push({pregunta_es:'',respuesta_es:'',pregunta_en:'',respuesta_en:''}); renderFaqs(); }
renderFaqs();

// ===== IMÁGENES: SUBIDA DIRECTA =====
// Imagen Principal
document.getElementById('upload-principal').addEventListener('change', async function(e) {
    const file = e.target.files[0]; if (!file) return;
    const formData = new FormData();
    formData.append('archivo', file);
    formData.append('csrf_token', CSRF);
    formData.append('tipo', 'habitacion');
    try {
        const res = await fetch(API_UPLOAD, { method: 'POST', body: formData });
        const d = await res.json();
        if (d.exito) {
            document.getElementById('f-img-principal-id').value = d.imagen_id;
            document.getElementById('img-principal-preview').innerHTML = `<img src="${d.ruta_thumbnail || d.ruta_original}" class="w-full h-full object-cover">`;
        } else { alert('Error: ' + d.error); }
    } catch(err) { alert('Error de red al subir imagen'); }
    this.value = '';
});

function quitarPrincipal() {
    document.getElementById('f-img-principal-id').value = '';
    document.getElementById('img-principal-preview').innerHTML = '<span class="text-xs text-gray-400">Sin imagen</span>';
}

// Galería
document.getElementById('upload-galeria').addEventListener('change', async function(e) {
    const files = [...e.target.files]; if (!files.length) return;
    const progress = document.getElementById('img-upload-progress');
    const status = document.getElementById('img-up-status');
    const bar = document.getElementById('img-up-bar');
    progress.classList.remove('hidden');
    let current = 0;
    for (const file of files) {
        const formData = new FormData();
        formData.append('archivo', file);
        formData.append('csrf_token', CSRF);
        formData.append('tipo', 'habitacion');
        try {
            const res = await fetch(API_UPLOAD, { method: 'POST', body: formData });
            const d = await res.json();
            if (d.exito) {
                const grid = document.getElementById('galeria-grid');
                grid.innerHTML += `<div class="img-card" data-img-id="${d.imagen_id}">
                    <img src="${d.ruta_thumbnail || d.ruta_original}" class="w-full h-24 object-cover">
                    <button type="button" onclick="removeGaleriaImg(this)" class="img-delete"><i class="fa-solid fa-xmark"></i></button>
                </div>`;
            }
        } catch(err) { console.error(err); }
        current++;
        status.textContent = `${current}/${files.length}`;
        bar.style.width = (current/files.length*100) + '%';
    }
    setTimeout(() => progress.classList.add('hidden'), 1000);
    this.value = '';
});

function removeGaleriaImg(btn) { btn.closest('.img-card').remove(); }

// ===== IA =====
async function iaGenerar(tipo) {
    const contexto = `Tipo: ${document.getElementById('f-tipo').selectedOptions[0]?.text || ''}. Nombre: ${document.getElementById('f-nombre-es').value}. Descripción: ${document.getElementById('f-desc-es').value}. Capacidad: ${document.getElementById('f-capacidad').value} huéspedes.`;
    const btn = event?.target?.closest('.ai-btn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> IA...'; }
    try {
        const res = await fetch(API_IA, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({csrf_token:CSRF, tipo, contexto}) });
        const d = await res.json();
        if (!d.exito) throw new Error(d.error);
        switch(tipo) {
            case 'descripcion': document.getElementById('f-desc-es').value = d.resultado.texto; break;
            case 'seo':
                if (d.resultado.json) {
                    document.getElementById('f-seo-titulo-es').value = d.resultado.json.titulo_es || '';
                    document.getElementById('f-seo-titulo-en').value = d.resultado.json.titulo_en || '';
                    document.getElementById('f-seo-desc-es').value = d.resultado.json.descripcion_es || '';
                    document.getElementById('f-seo-desc-en').value = d.resultado.json.descripcion_en || '';
                    document.getElementById('f-seo-kw-es').value = d.resultado.json.keywords_es || '';
                    document.getElementById('f-seo-kw-en').value = d.resultado.json.keywords_en || '';
                } break;
            case 'schema': document.getElementById('f-schema').value = d.resultado.json ? JSON.stringify(d.resultado.json,null,2) : d.resultado.texto; break;
            case 'faqs': if (d.resultado.json) { faqsData = d.resultado.json; renderFaqs(); } break;
            case 'comodidades': if (d.resultado.json) { repeaterData.comodidades = d.resultado.json; renderRepeater('comodidades'); } break;
            case 'amenities': if (d.resultado.json) { repeaterData.amenities = d.resultado.json; renderRepeater('amenities'); } break;
        }
    } catch(e) { alert('Error IA: ' + e.message); }
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Listo'; setTimeout(() => { btn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> Sugerir IA'; }, 2000); }
}

async function iaTraducir(campo) {
    const textoEs = campo === 'nombre' ? document.getElementById('f-nombre-es').value : document.getElementById('f-desc-es').value;
    if (!textoEs) return alert('Escribe primero el contenido en español');
    const btn = event?.target?.closest('.translate-btn');
    if (btn) { btn.disabled = true; btn.textContent = 'Trad...'; }
    try {
        const res = await fetch(API_IA, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({csrf_token:CSRF, tipo:'traduccion', contexto:textoEs, idioma_destino:'en'}) });
        const d = await res.json();
        if (!d.exito) throw new Error(d.error);
        document.getElementById(campo === 'nombre' ? 'f-nombre-en' : 'f-desc-en').value = d.resultado.texto;
    } catch(e) { alert('Error: ' + e.message); }
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-globe mr-1"></i>Traducir del ES'; }
}

async function iaTraducirCampo(srcId, dstId) {
    const src = document.getElementById(srcId)?.value;
    if (!src) return alert('Campo español vacío');
    const btn = event?.target?.closest('.translate-btn');
    if (btn) { btn.disabled = true; btn.textContent = 'Trad...'; }
    try {
        const res = await fetch(API_IA, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify({csrf_token:CSRF, tipo:'traduccion', contexto:src, idioma_destino:'en'}) });
        const d = await res.json();
        if (d.exito) document.getElementById(dstId).value = d.resultado.texto;
    } catch(e) { alert('Error: ' + e.message); }
    if (btn) { btn.disabled = false; btn.textContent = 'Traducir'; }
}

// ===== GUARDAR TODO =====
async function guardarTodo() {
    const btn = document.getElementById('btn-guardar-global');
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...'; btn.disabled = true;

    // Recopilar imágenes galería
    const imagenesIds = [];
    const principalId = document.getElementById('f-img-principal-id').value;
    if (principalId) imagenesIds.push({ imagen_id: parseInt(principalId), es_principal: 1, orden: 0 });
    document.querySelectorAll('#galeria-grid .img-card').forEach((el, i) => {
        imagenesIds.push({ imagen_id: parseInt(el.dataset.imgId), es_principal: 0, orden: i + 1 });
    });

    const body = {
        csrf_token: CSRF,
        numero_habitacion: document.getElementById('f-numero').value || 0,
        tipo_habitacion_id: document.getElementById('f-tipo').value,
        estado: document.getElementById('f-estado').value,
        activa: document.getElementById('f-activa').checked ? 1 : 0,
        capacidad_huespedes: document.getElementById('f-capacidad').value,
        num_camas: document.getElementById('f-camas-num').value,
        nombre_es: document.getElementById('f-nombre-es').value,
        descripcion_es: document.getElementById('f-desc-es').value,
        nombre_en: document.getElementById('f-nombre-en').value,
        descripcion_en: document.getElementById('f-desc-en').value,
        seo_titulo_es: document.getElementById('f-seo-titulo-es')?.value || '',
        seo_descripcion_es: document.getElementById('f-seo-desc-es')?.value || '',
        seo_palabras_clave_es: document.getElementById('f-seo-kw-es')?.value || '',
        seo_titulo_en: document.getElementById('f-seo-titulo-en')?.value || '',
        seo_descripcion_en: document.getElementById('f-seo-desc-en')?.value || '',
        seo_palabras_clave_en: document.getElementById('f-seo-kw-en')?.value || '',
        comodidades: repeaterData.comodidades,
        amenities: repeaterData.amenities,
        camas: camasData,
        faqs: faqsData,
        imagenes: imagenesIds
    };

    if (ES_ADMIN) {
        body.schema_json = document.getElementById('f-schema')?.value || '';
        body.geo = {
            latitud: document.getElementById('f-geo-lat')?.value || '',
            longitud: document.getElementById('f-geo-lng')?.value || '',
            pais: document.getElementById('f-geo-pais')?.value || '',
            region: document.getElementById('f-geo-region')?.value || '',
            ciudad: document.getElementById('f-geo-ciudad')?.value || '',
            keywords_geo: document.getElementById('f-geo-kw')?.value || ''
        };
    }

    if (HAB_ID) { body._method = 'PUT'; body.id = HAB_ID; }
    if (!body.tipo_habitacion_id || !body.nombre_es) { alert('Tipo y nombre español son obligatorios'); btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar Todo'; return; }

    try {
        const res = await fetch(API_HAB, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) });
        const d = await res.json();
        if (d.exito) {
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Guardado';
            btn.classList.replace('bg-admin-accent','bg-green-600');
            if (!HAB_ID && d.id) window.location.href = '/hotel/admin/habitaciones/editar.php?id=' + d.id;
            else setTimeout(() => { btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar Todo'; btn.classList.replace('bg-green-600','bg-admin-accent'); btn.disabled = false; }, 2000);
        } else throw new Error(d.error);
    } catch(e) { alert('Error: ' + e.message); btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar Todo'; }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
