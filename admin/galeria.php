<?php
session_start();
require_once __DIR__ . '/../includes/Auth.php';
require_once __DIR__ . '/../includes/Security.php';
require_once __DIR__ . '/../includes/CSRF.php';

if (!Auth::check()) {
    header('Location: /hotel/admin/login.php');
    exit;
}
$pageTitle = 'Galería Multimedia';
$activeMenu = 'galeria';

require_once __DIR__ . '/includes/header.php';
?>
<div class="flex gap-4 items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800">Tus Archivos</h2>
    <div>
        <div class="flex gap-2">
            <button onclick="openAiModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                Generar Fotografía IA
            </button>
            <input type="file" id="file_upload" class="hidden" multiple accept=".jpg,.jpeg,.png,.webp,.svg,.ico">
            <button onclick="document.getElementById('file_upload').click()" class="bg-admin-accent hover:opacity-90 text-white px-4 py-2 rounded-lg text-sm font-medium shadow transition-colors flex items-center gap-2 relative overflow-hidden group">
                <span class="absolute inset-0 w-full h-full bg-white/20 scale-x-0 group-hover:scale-x-100 transform origin-left transition-transform duration-300"></span>
                <svg class="w-4 h-4 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                <span class="relative z-10">Subir Imágenes</span>
            </button>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="flex-1 flex overflow-hidden bg-white border border-gray-200 rounded-xl shadow-sm min-h-[70vh]">
    <!-- Gallery Grid -->
    <div class="flex-1 overflow-y-auto p-6 flex flex-col transition-colors duration-300 relative" id="dropzone">
            <!-- Filters -->
            <div class="flex gap-4 mb-6">
                <div class="flex-1 relative">
                    <input type="text" id="search_q" placeholder="Buscar por nombre, etiqueta o texto alternativo..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg outline-none focus:ring-1 focus:ring-admin-accent bg-white shadow-sm">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <select id="filter_tipo" class="border border-gray-300 rounded-lg outline-none px-4 bg-white shadow-sm cursor-pointer">
                    <option value="">Todos los tipos</option>
                    <option value="habitacion">Habitaciones</option>
                    <option value="hotel">Espacios del Hotel</option>
                    <option value="amenidades">Amenidades</option>
                    <option value="vista">Vistas</option>
                    <option value="logo">Logos e Iconos</option>
                    <option value="general">Uso General</option>
                </select>
            </div>
            
            <!-- Upload Progress UI -->
            <div id="upload_progress" class="hidden mb-6 bg-white p-4 rounded-lg shadow-sm border border-blue-200">
                <div class="text-sm font-bold text-blue-800 mb-2">Optimizando y subiendo archivos... (<span id="up_current">0</span>/<span id="up_total">0</span>)</div>
                <div class="w-full bg-gray-200 rounded-full h-2.5"><div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" id="up_bar" style="width: 0%"></div></div>
            </div>

            <!-- Empty State -->
            <div id="empty_state" class="hidden flex-1 flex-col justify-center items-center text-gray-400 opacity-80">
                <div class="w-24 h-24 mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-xl font-medium text-gray-600">Galería vacía o sin resultados</p>
                <p class="text-sm mt-2">Arrastra imágenes a esta pantalla o usa el buscador de otra forma.</p>
            </div>

            <!-- Grid -->
            <div id="gallery_grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-5">
                <!-- Javascript will render images here -->
            </div>
            
            <!-- Pagination -->
            <div id="pagination" class="mt-8 flex justify-center gap-2"></div>
        </div>
        
        <!-- Right Sidebar Details -->
        <div id="sidebar" class="w-80 bg-gray-50 border-l border-gray-200 p-6 overflow-y-auto hidden shadow-xl z-20 transition-transform transform translate-x-full duration-300 absolute right-0 h-full lg:static lg:transform-none lg:shadow-none lg:h-auto border-t-0 border-r-0 border-b-0">
            <!-- Sidebar Content dynamically built in JS -->
        </div>
</div>

<!-- LIGHBOX MODAL -->
<div id="lightbox-modal" class="fixed inset-0 bg-black/90 z-[60] hidden flex items-center justify-center p-4 lg:p-12 cursor-zoom-out" onclick="closeLightbox()">
    <img id="lightbox-img" src="" class="max-w-full max-h-full object-contain drop-shadow-2xl">
    <button class="absolute top-4 right-4 text-white hover:text-gray-300 z-50 p-2 bg-black/50 rounded-full"><svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
</div>

<!-- AI MODAL -->
<div id="ai-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex justify-center items-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg p-6 relative">
        <button onclick="closeAiModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 bg-gray-100 hover:bg-gray-200 rounded-full w-8 h-8 flex items-center justify-center transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
        <h3 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 mb-2 flex items-center gap-2"><svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg> Generador Gemini</h3>
        <p class="text-sm text-gray-500 mb-6">Redacta una descripción clara para conectarnos al modelo Fotográfico AI.</p>
        <textarea id="ai-prompt" class="w-full border py-3 px-4 rounded-xl outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 h-28 mb-4 resize-none shadow-inner" placeholder="Ej: Una habitacion de lujo tipo rustica con pared de piedra, iluminación cálida al atardecer, fotografía realista 4k UHD..."></textarea>
        <button id="btn-ai-gen" onclick="generarIA()" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 rounded-xl shadow cursor-pointer transition-all disabled:opacity-50">Generar, Optimizar a WebP y Guardar <i class="fa-solid fa-wand-magic-sparkles ml-1"></i></button>
    </div>
</div>

<script>
    const CSRF_TOKEN = '<?= CSRF::generateToken() ?>';
    let currentPage = 1;

    // Throttle for search input
    let searchTimeout = null;

    async function fetchImages() {
        const q = document.getElementById('search_q').value;
        const tipo = document.getElementById('filter_tipo').value;
        try {
            const res = await fetch(`/hotel/api/galeria/obtener-imagenes.php?pagina=${currentPage}&q=${encodeURIComponent(q)}&tipo=${encodeURIComponent(tipo)}`);
            const data = await res.json();
            if (data.exito) {
                renderGrid(data.imagenes);
                renderPagination(data.pagina, data.paginas);
                closeSidebar(); // Cierra el sidebar si hay fetch masivo
            }
        } catch (e) {
            console.error('Error fetching gallery:', e);
        }
    }

    function renderGrid(images) {
        const grid = document.getElementById('gallery_grid');
        const empty = document.getElementById('empty_state');
        grid.innerHTML = '';
        
        if (images.length === 0) {
            empty.classList.remove('hidden');
            empty.classList.add('flex');
        } else {
            empty.classList.add('hidden');
            empty.classList.remove('flex');
            
            images.forEach(img => {
                grid.innerHTML += `
                    <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200 cursor-pointer hover:border-admin-accent hover:shadow-md hover:-translate-y-1 transition-all duration-200 group aspect-square flex flex-col items-center justify-center relative overflow-hidden" onclick='showDetails(${JSON.stringify(img).replace(/'/g, "&#39;")})'>
                        <div class="h-3/4 w-full flex items-center justify-center mb-2 pointer-events-none">
                            <img src="${img.ruta_thumbnail}" class="max-h-full max-w-full object-contain rounded" alt="${img.alt_text || img.nombre_original}" loading="lazy">
                        </div>
                        <div class="absolute bottom-0 w-full bg-gradient-to-t from-gray-900/90 to-transparent pt-4 pb-2 px-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            <p class="text-[11px] text-white truncate font-medium" title="${img.nombre_original}">${img.nombre_original}</p>
                            <div class="flex justify-between items-center mt-0.5">
                                <span class="bg-white/20 px-1.5 rounded text-[9px] text-white uppercase">${img.tipo || 'GEN'}</span>
                                <p class="text-[10px] text-gray-300">${img.peso_mb} MB</p>
                            </div>
                        </div>
                    </div>
                `;
            });
        }
    }

    function renderPagination(current, total) {
        const p = document.getElementById('pagination');
        p.innerHTML = '';
        if (total <= 1) return;
        
        if (current > 1) {
            p.innerHTML += `<button onclick="currentPage=${current-1}; fetchImages()" class="px-3 py-1 bg-white border border-gray-300 rounded text-sm hover:bg-gray-50 focus:outline-none">&laquo;</button>`;
        }
        for (let i=1; i<=total; i++) {
            if (i === current) {
                p.innerHTML += `<button class="px-3 py-1 bg-admin-accent text-white rounded text-sm focus:outline-none">${i}</button>`;
            } else {
                p.innerHTML += `<button onclick="currentPage=${i}; fetchImages()" class="px-3 py-1 bg-white border border-gray-300 rounded text-sm hover:bg-gray-50 focus:outline-none">${i}</button>`;
            }
        }
        if (current < total) {
            p.innerHTML += `<button onclick="currentPage=${current+1}; fetchImages()" class="px-3 py-1 bg-white border border-gray-300 rounded text-sm hover:bg-gray-50 focus:outline-none">&raquo;</button>`;
        }
    }

    function showDetails(img) {
        const s = document.getElementById('sidebar');
        s.classList.remove('hidden', 'translate-x-full'); 
        
        s.innerHTML = `
            <div class="flex justify-between items-start mb-6">
                <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2"><i class="fa-solid fa-sliders text-gray-400"></i> Metadatos</h3>
                <button onclick="closeSidebar()" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-full w-8 h-8 flex items-center justify-center transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            
            <div class="bg-gray-100 rounded-xl p-3 mb-6 flex items-center justify-center h-48 relative overflow-hidden border border-gray-200 shadow-inner group cursor-zoom-in" onclick="openLightbox('${img.ruta_webp}')">
                <div class="absolute inset-0 bg-transparent opacity-10" style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 10px 10px;"></div>
                <img src="${img.ruta_thumbnail}" class="max-w-full max-h-full rounded relative z-10 drop-shadow-md">
                ${parseFloat(img.porcentaje_ahorro) > 0 ? `<div class="absolute top-2 right-2 bg-green-500 text-white text-[10px] font-bold px-2.5 py-1 rounded shadow-sm z-20">Optimizado -${Math.round(img.porcentaje_ahorro)}%</div>` : ''}
                <div class="absolute inset-0 bg-black/40 z-30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity text-white text-sm font-bold"><i class="fa-solid fa-expand mr-2"></i> Abrir Grande</div>
            </div>

            <form id="edit-img-form" onsubmit="event.preventDefault(); actualizarImagen(${img.id})" class="space-y-4 text-sm pb-6">
                <!-- Data Base -->
                <div class="bg-gray-50 border border-gray-200 p-3 rounded-lg mb-4">
                    <label class="block text-[10px] uppercase font-bold text-gray-500">Ruta Pública (URL)</label>
                    <div class="flex items-center gap-2 mt-1">
                        <input type="text" readonly value="${img.ruta_webp}" class="w-full text-[10px] bg-white text-gray-600 p-1.5 rounded border border-gray-300 outline-none select-all font-mono">
                        <button type="button" onclick="navigator.clipboard.writeText('${img.ruta_webp}'); this.innerHTML='<i class=\\'fa-solid fa-check\\'></i>'; setTimeout(()=>this.innerHTML='<i class=\\'fa-regular fa-copy\\'></i>', 2000)" class="text-gray-500 hover:text-admin-accent w-6 h-6 flex items-center justify-center bg-white border border-gray-300 rounded"><i class="fa-regular fa-copy"></i></button>
                    </div>
                </div>

                <!-- Formularios -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1" title="Para accesibilidad y SEO basico">Alt Text (Texto Alternativo)</label>
                    <input type="text" id="img_alt" value="${img.alt_text||''}" class="w-full p-2 border border-gray-300 rounded-lg text-sm outline-none focus:border-admin-accent focus:ring-1 focus:ring-admin-accent shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 mb-1">Etiquetas (Separadas por comas)</label>
                    <input type="text" id="img_tags" value="${img.etiquetas||''}" class="w-full p-2 border border-gray-300 rounded-lg text-sm outline-none focus:border-admin-accent focus:ring-1 focus:ring-admin-accent shadow-sm" placeholder="interior, madera, lujo">
                </div>
                
                <h4 class="font-bold text-xs text-indigo-700 uppercase mt-8 mb-3 border-b border-indigo-100 pb-1 flex items-center gap-2"><i class="fa-solid fa-magnifying-glass"></i> SEO Principal</h4>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">SEO Title (Máx 60 caracteres)</label>
                    <input type="text" id="img_seo_ti" value="${img.seo_titulo||''}" class="w-full p-2 border border-indigo-100 bg-indigo-50/30 rounded text-sm outline-none focus:border-indigo-400 transition-colors">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">SEO Description (Máx 160 caracteres)</label>
                    <textarea id="img_seo_de" class="w-full p-2 border border-indigo-100 bg-indigo-50/30 rounded text-xs outline-none focus:border-indigo-400 h-16 resize-none transition-colors">${img.seo_descripcion||''}</textarea>
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">SEO Keywords</label>
                    <input type="text" id="img_seo_kw" value="${img.seo_palabras_clave||''}" class="w-full p-2 border border-indigo-100 bg-indigo-50/30 rounded text-xs outline-none focus:border-indigo-400 transition-colors">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1 flex items-center gap-1">Schema.org JSON-LD <i class="fa-brands fa-js text-yellow-500"></i></label>
                    <textarea id="img_schema" class="w-full p-2 border border-indigo-100 bg-indigo-50/30 rounded text-[10px] outline-none h-20 font-mono focus:border-indigo-400 transition-colors" placeholder='{ "@context": "https://schema.org/", "@type": "ImageObject"... }'>${img.schema_json||''}</textarea>
                </div>

                <h4 class="font-bold text-xs text-emerald-700 uppercase mt-8 mb-3 border-b border-emerald-100 pb-1 flex items-center gap-2"><i class="fa-solid fa-location-dot"></i> Geo Localización</h4>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Latitud</label>
                        <input type="number" step="any" id="img_lat" value="${img.geo_latitud||''}" class="w-full p-2 border border-emerald-100 bg-emerald-50/30 rounded text-xs outline-none focus:border-emerald-400 transition-colors">
                    </div>
                    <div>
                        <label class="block text-[11px] text-gray-600 mb-1">Longitud</label>
                        <input type="number" step="any" id="img_lng" value="${img.geo_longitud||''}" class="w-full p-2 border border-emerald-100 bg-emerald-50/30 rounded text-xs outline-none focus:border-emerald-400 transition-colors">
                    </div>
                </div>
                <div class="mt-3">
                    <label class="block text-[11px] text-gray-600 mb-1">Región / Ciudad</label>
                    <input type="text" id="img_region" value="${img.geo_region||''}" placeholder="Cusco, Perú" class="w-full p-2 border border-emerald-100 bg-emerald-50/30 rounded text-xs outline-none focus:border-emerald-400 transition-colors">
                </div>

                <div class="pt-6 mt-6 border-t border-gray-200 space-y-3">
                    <button type="submit" id="btn-save" class="w-full bg-admin-accent hover:bg-blue-700 text-white py-2.5 rounded-lg font-bold shadow-sm transition-all text-sm flex items-center justify-center gap-2"><i class="fa-solid fa-floppy-disk"></i> Guardar Metadatos</button>
                    <button type="button" onclick="eliminarImagen(${img.id})" class="w-full bg-white text-red-500 shadow-sm hover:shadow-md hover:border-red-500 border border-gray-200 py-2.5 rounded-lg font-bold transition-all text-sm flex items-center justify-center gap-2 group">
                        <svg class="w-4 h-4 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Borrar Fotografía
                    </button>
                    <p class="text-[10px] text-gray-400 text-center leading-tight">Borrar eliminará físicamente las versiones Original, WebP, Miniatura y Tiny.</p>
                </div>
            </form>
        `;
    }

    function closeSidebar() {
        const s = document.getElementById('sidebar');
        if (!s.classList.contains('hidden')) {
            s.classList.add('translate-x-full');
            setTimeout(() => { s.classList.add('hidden'); }, 300);
        }
    }

    async function eliminarImagen(id) {
        if(!confirm('¿Estás SEGURO de eliminar esta imagen definitivamente?')) return;
        
        try {
            const res = await fetch('/hotel/api/galeria/eliminar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, csrf_token: CSRF_TOKEN })
            });
            const data = await res.json();
            if(data.exito) {
                closeSidebar();
                fetchImages();
            } else {
                alert(data.error);
            }
        } catch(e) {
            alert('Error de conexión con el motor de borrado.');
        }
    }

    // UPLOAD LOGIC
    const fileInput = document.getElementById('file_upload');
    const dropzone = document.getElementById('dropzone');

    fileInput.addEventListener('change', handleFiles);
    
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => {
        dropzone.addEventListener(e, preventDefaults, false);
    });
    function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }
    
    dropzone.addEventListener('dragover', () => dropzone.classList.add('bg-blue-50/50'));
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('bg-blue-50/50'));
    dropzone.addEventListener('drop', (e) => {
        dropzone.classList.remove('bg-blue-50/50');
        handleFiles({ target: { files: e.dataTransfer.files } });
    });

    async function handleFiles(e) {
        const files = [...e.target.files];
        if(files.length === 0) return;

        let defaultType = document.getElementById('filter_tipo').value || 'general';

        const upUi = document.getElementById('upload_progress');
        const upCurr = document.getElementById('up_current');
        const upTot = document.getElementById('up_total');
        const upBar = document.getElementById('up_bar');

        upTot.innerText = files.length;
        upCurr.innerText = '0';
        upBar.style.width = '0%';
        upUi.classList.remove('hidden');

        let current = 0;
        let errores = [];

        for (const file of files) {
            // Validate client-side somewhat before slamming server
            if (file.size > 5 * 1024 * 1024) {
                errores.push(`${file.name}: Pesa más de 5MB`);
            } else {
                const formData = new FormData();
                formData.append('archivo', file);
                formData.append('csrf_token', CSRF_TOKEN);
                formData.append('tipo', defaultType);
                
                try {
                    const res = await fetch('/hotel/api/galeria/subir.php', { method: 'POST', body: formData });
                    const data = await res.json();
                    if(!data.exito) {
                        errores.push(`${file.name}: ${data.error}`);
                    }
                } catch(e) {
                    errores.push(`${file.name}: Error de red o timeout intentando escalar WebP`);
                }
            }
            
            current++;
            upCurr.innerText = current;
            upBar.style.width = (current / files.length * 100) + '%';
        }

        setTimeout(() => {
            upUi.classList.add('hidden');
            if(errores.length > 0) alert("Reporte de Fallos de Carga:\n\n" + errores.join("\n"));
            currentPage = 1; // Return to first page to see uploads
            fetchImages();
        }, 800);
        
        fileInput.value = '';
    }

    // Listeners for filters
    document.getElementById('search_q').addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1; fetchImages();
        }, 400); // 400ms delay to avoid spamming
    });
    
    document.getElementById('filter_tipo').addEventListener('change', () => { 
        currentPage=1; 
        fetchImages(); 
    });

    // Lightbox & UI
    function openLightbox(url) {
        document.getElementById('lightbox-img').src = url;
        document.getElementById('lightbox-modal').classList.remove('hidden');
    }
    function closeLightbox() { document.getElementById('lightbox-modal').classList.add('hidden'); }

    function openAiModal() { document.getElementById('ai-modal').classList.remove('hidden'); }
    function closeAiModal() { document.getElementById('ai-modal').classList.add('hidden'); }

    // Generar con IA
    async function generarIA() {
        const p = document.getElementById('ai-prompt').value;
        if(!p) return alert("Escribe un parámetro de generación");
        const btn = document.getElementById('btn-ai-gen');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Procesando Fotografía con Gemini IA...';

        try {
            const fd = new FormData();
            fd.append('prompt', p);
            fd.append('csrf_token', CSRF_TOKEN);
            const res = await fetch('/hotel/api/ai/generar-imagen.php', { method: 'POST', body: fd });
            const data = await res.json();
            if(data.exito) {
                closeAiModal();
                alert('¡Magia Realizada! La API de Gemini generó, optimizó e indexó la fotografía exitosamente.');
                document.getElementById('ai-prompt').value = ''; // Clean
                currentPage = 1;
                fetchImages();
            } else {
                alert(data.error);
            }
        } catch(e) {
            alert("Error conectando con la API de IA Local.");
        }
        btn.disabled = false;
        btn.innerHTML = 'Generar, Optimizar a WebP y Guardar <i class="fa-solid fa-wand-magic-sparkles ml-1"></i>';
    }

    // Update Image DB
    async function actualizarImagen(id) {
        const btn = document.getElementById('btn-save');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';
        
        const fd = new FormData();
        fd.append('id', id);
        fd.append('csrf_token', CSRF_TOKEN);
        fd.append('alt_text', document.getElementById('img_alt').value);
        fd.append('etiquetas', document.getElementById('img_tags').value);
        fd.append('seo_titulo', document.getElementById('img_seo_ti').value);
        fd.append('seo_descripcion', document.getElementById('img_seo_de').value);
        fd.append('seo_palabras_clave', document.getElementById('img_seo_kw').value);
        fd.append('schema_json', document.getElementById('img_schema').value);
        fd.append('geo_latitud', document.getElementById('img_lat').value);
        fd.append('geo_longitud', document.getElementById('img_lng').value);
        fd.append('geo_region', document.getElementById('img_region').value);

        try {
            const r = await fetch('/hotel/api/galeria/actualizar.php', { method: 'POST', body: fd });
            const d = await r.json();
            if(d.exito) {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Activo';
                btn.classList.replace('bg-admin-accent', 'bg-green-600');
                btn.classList.replace('hover:bg-blue-700', 'hover:bg-green-700');
                
                setTimeout(()=> { 
                    btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar Metadatos';
                    btn.classList.replace('bg-green-600', 'bg-admin-accent');
                    btn.classList.replace('hover:bg-green-700', 'hover:bg-blue-700');
                }, 2000);
            } else { 
                alert(d.error); 
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar Metadatos'; 
            }
        } catch(e) { 
            alert("Error Comunicando con API"); 
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Guardar Metadatos'; 
        }
    }

    // Initial load
    document.addEventListener("DOMContentLoaded", () => fetchImages());

</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
