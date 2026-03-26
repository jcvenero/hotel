<!-- Media Picker Component -->
<div id="mediaPickerModal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300 items-center justify-center p-4 sm:p-6 lg:p-12">
    <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="closeMediaPicker()"></div>
    
    <div class="relative w-full max-w-6xl mx-auto bg-white rounded-2xl shadow-2xl flex flex-col h-full max-h-[90vh] sm:max-h-[85vh] overflow-hidden transform scale-95 transition-transform duration-300" id="mpBox">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-white z-10">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Biblioteca Multimedia
            </h3>
            <button onclick="closeMediaPicker()" class="text-gray-400 hover:text-gray-700 hover:bg-gray-100 w-8 h-8 rounded-full flex items-center justify-center transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <!-- Toolbar -->
        <div class="bg-gray-50/80 border-b border-gray-200 px-6 py-3 flex flex-wrap gap-4 items-center justify-between z-10">
            <div class="flex flex-1 min-w-[300px] gap-3">
                <div class="relative flex-1 max-w-sm">
                    <input type="text" id="mp_search" placeholder="Buscar imagen..." class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white shadow-sm">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <select id="mp_filter" class="text-sm border border-gray-300 rounded-lg outline-none px-3 py-2 bg-white shadow-sm cursor-pointer hover:border-gray-400">
                    <option value="">Todos los Tipos</option>
                    <option value="logo">Logos/Iconos</option>
                    <option value="habitacion">Habitaciones</option>
                    <option value="hotel">Espacios</option>
                    <option value="amenidades">Amenidades</option>
                    <option value="vista">Vistas</option>
                    <option value="general">Uso General</option>
                </select>
            </div>
            
            <div>
                <input type="file" id="mp_upload" class="hidden" accept=".jpg,.jpeg,.png,.webp,.svg,.ico" multiple>
                <button onclick="document.getElementById('mp_upload').click()" class="text-sm border border-blue-600 text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Subir Nueva
                </button>
            </div>
        </div>
        
        <!-- Uploading Indicator -->
        <div id="mp_uploading" class="hidden bg-blue-600 text-white px-6 py-2 text-xs font-bold flex justify-between items-center shadow-inner">
            <span>Optimizando e insertando imagen(es) en galería... <span id="mp_up_stats"></span></span>
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
        </div>

        <!-- Working Area -->
        <div class="flex-1 flex overflow-hidden bg-gray-100/50">
            <!-- Grid -->
            <div class="flex-1 overflow-y-auto p-6" id="mp_grid_container">
                <div id="mp_empty" class="hidden h-full flex-col justify-center items-center text-gray-400">
                    <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="font-medium text-gray-500">Ninguna imagen coincide</p>
                </div>
                <div id="mp_grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    <!-- Images -->
                </div>
                <div id="mp_pagination" class="mt-8 flex justify-center gap-2"></div>
            </div>
            
            <!-- Sidebar Selection -->
            <div class="w-72 bg-white border-l border-gray-200 p-5 flex flex-col shadow-[-4px_0_15px_-3px_rgba(0,0,0,0.05)] z-10 relative">
                <h4 class="font-bold text-gray-800 border-b border-gray-100 pb-2 mb-4">Selección Actual</h4>
                
                <div id="mp_preview_box" class="flex-1">
                    <div class="h-32 bg-gray-50 border border-gray-200 rounded-lg flex items-center justify-center mb-4 relative overflow-hidden group">
                        <!-- Transparency Grid Background -->
                        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 8px 8px;"></div>
                        <span id="mp_preview_placeholder" class="text-xs font-medium text-gray-400">Sin seleccionar</span>
                        <img id="mp_preview_img" class="hidden max-h-full max-w-full drop-shadow-md relative z-10 p-2 object-contain hidden transition-transform group-hover:scale-105">
                    </div>
                    
                    <div id="mp_details" class="hidden space-y-3">
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Nombre</p>
                            <p class="text-sm font-medium text-gray-900 truncate" title="" id="mp_d_name">-</p>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Peso Original</p>
                                <p class="text-sm text-gray-700 font-mono" id="mp_d_sizeorig">-</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Peso WebP</p>
                                <p class="text-sm text-green-600 font-bold font-mono" id="mp_d_sizewebp">-</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wide">Dimensiones</p>
                            <p class="text-sm text-gray-700 font-mono">1920x1080 (Máx)</p>
                        </div>
                    </div>
                </div>
                
                <div class="pt-4 mt-auto">
                    <button id="btnMpConfirm" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-500/30 opacity-50 cursor-not-allowed transition-all transform active:scale-95 flex items-center justify-center gap-2" disabled>
                        Insertar Seleccionada
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const CSRF_TOKEN_MP = '<?= CSRF::generateToken() ?>';
    let mpCallback = null;
    let mpCurrentPage = 1;
    let mpSelectedObj = null;
    let mpSearchTimeout = null;

    window.openMediaPicker = function(callback) {
        mpCallback = callback;
        mpSelectedObj = null;
        updateMpSidebar();
        
        const modal = document.getElementById('mediaPickerModal');
        modal.classList.remove('hidden');
        // Pequeño delay para la transicion
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            document.getElementById('mpBox').classList.remove('scale-95');
        }, 10);
        
        fetchMpImages();
    };

    window.closeMediaPicker = function() {
        const modal = document.getElementById('mediaPickerModal');
        modal.classList.add('opacity-0');
        document.getElementById('mpBox').classList.add('scale-95');
        
        setTimeout(() => {
            modal.classList.add('hidden');
            mpCallback = null;
            mpSelectedObj = null;
        }, 300);
    };

    async function fetchMpImages() {
        const q = document.getElementById('mp_search').value;
        const tipo = document.getElementById('mp_filter').value;
        try {
            const res = await fetch(`/hotel/api/galeria/obtener-imagenes.php?pagina=${mpCurrentPage}&limite=24&q=${encodeURIComponent(q)}&tipo=${encodeURIComponent(tipo)}`);
            const data = await res.json();
            if (data.exito) {
                renderMpGrid(data.imagenes);
                renderMpPagination(data.pagina, data.paginas);
            }
        } catch (e) {
            console.error('Error MP:', e);
        }
    }

    function renderMpGrid(images) {
        const grid = document.getElementById('mp_grid');
        const empty = document.getElementById('mp_empty');
        grid.innerHTML = '';
        
        if (images.length === 0) {
            empty.classList.remove('hidden');
            empty.classList.add('flex');
        } else {
            empty.classList.add('hidden');
            empty.classList.remove('flex');
            
            images.forEach(img => {
                const isSelected = mpSelectedObj && mpSelectedObj.id === img.id;
                const borderClass = isSelected ? 'border-blue-500 ring-2 ring-blue-500/50 shadow-md' : 'border-gray-200 hover:border-gray-400';
                
                grid.innerHTML += `
                    <div id="mp_img_${img.id}" class="bg-white p-2 border ${borderClass} rounded-lg cursor-pointer transition-all aspect-square flex flex-col justify-center relative group" onclick='selectMpImage(${JSON.stringify(img).replace(/'/g, "&#39;")})'>
                        ${isSelected ? '<div class="absolute -top-1.5 -right-1.5 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white z-10 shadow-sm border-2 border-white"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>' : ''}
                        
                        <div class="h-full w-full flex items-center justify-center relative pointer-events-none">
                            <div class="absolute inset-0 opacity-[0.03] rounded" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 8px 8px;"></div>
                            <img src="${img.ruta_thumbnail}" class="max-h-full max-w-full drop-shadow-sm rounded object-contain relative z-10" loading="lazy">
                        </div>
                    </div>
                `;
            });
        }
    }

    window.selectMpImage = function(imgData) {
        mpSelectedObj = imgData;
        fetchMpImages(); // Re-render to show selection ring
        updateMpSidebar();
    }

    function updateMpSidebar() {
        const btn = document.getElementById('btnMpConfirm');
        const d_box = document.getElementById('mp_details');
        const p_holder = document.getElementById('mp_preview_placeholder');
        const p_img = document.getElementById('mp_preview_img');

        if (mpSelectedObj) {
            d_box.classList.remove('hidden');
            p_holder.classList.add('hidden');
            p_img.classList.remove('hidden');
            p_img.src = mpSelectedObj.ruta_thumbnail;

            document.getElementById('mp_d_name').innerText = mpSelectedObj.nombre_original;
            document.getElementById('mp_d_name').title = mpSelectedObj.nombre_original;
            document.getElementById('mp_d_sizeorig').innerText = (mpSelectedObj.peso_original/1024/1024).toFixed(2)+'MB';
            document.getElementById('mp_d_sizewebp').innerText = mpSelectedObj.peso_mb+'MB';

            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            // Remove previous listeners cleanly
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            newBtn.addEventListener('click', () => {
                if(mpCallback) mpCallback(mpSelectedObj);
                closeMediaPicker();
            });
        } else {
            d_box.classList.add('hidden');
            p_holder.classList.remove('hidden');
            p_img.classList.add('hidden');
            p_img.src = "";

            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    function renderMpPagination(current, total) {
        const p = document.getElementById('mp_pagination');
        p.innerHTML = '';
        if (total <= 1) return;
        
        if (current > 1) {
            p.innerHTML += `<button onclick="mpCurrentPage=${current-1}; fetchMpImages()" class="px-2.5 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50 shadow-sm">&laquo;</button>`;
        }
        for (let i=1; i<=total; i++) {
            if (i === current) {
                p.innerHTML += `<button class="px-2.5 py-1 text-xs bg-blue-600 text-white rounded shadow-sm font-medium">${i}</button>`;
            } else {
                p.innerHTML += `<button onclick="mpCurrentPage=${i}; fetchMpImages()" class="px-2.5 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50 shadow-sm font-medium">${i}</button>`;
            }
        }
        if (current < total) {
            p.innerHTML += `<button onclick="mpCurrentPage=${current+1}; fetchMpImages()" class="px-2.5 py-1 text-xs bg-white border border-gray-300 rounded hover:bg-gray-50 shadow-sm">&raquo;</button>`;
        }
    }

    // UPLOAD MP
    document.getElementById('mp_upload').addEventListener('change', async (e) => {
        const files = [...e.target.files];
        if(files.length === 0) return;

        const upUi = document.getElementById('mp_uploading');
        const upStats = document.getElementById('mp_up_stats');
        upUi.classList.remove('hidden');

        let defaultType = document.getElementById('mp_filter').value || 'general';
        let current = 0;
        let selectedIdToPick = null;

        for (const file of files) {
            current++;
            upStats.innerText = `${current}/${files.length}`;
            
            const formData = new FormData();
            formData.append('archivo', file);
            formData.append('csrf_token', CSRF_TOKEN_MP);
            formData.append('tipo', defaultType);
            
            try {
                const res = await fetch('/hotel/api/galeria/subir.php', { method: 'POST', body: formData });
                const data = await res.json();
                if(data.exito && current === files.length) {
                    // Auto-select the last uploaded file to make workflow insanely fast
                    selectedIdToPick = data.imagen_id; 
                }
            } catch(e) {}
        }
        
        setTimeout(() => {
            upUi.classList.add('hidden');
            mpCurrentPage = 1;
            document.getElementById('mp_search').value = ""; // Clear search to see new files
            
            fetchMpImages().then(() => {
                // If we want to auto-select, we don't have the object directly. So we just refresh view.
            });
        }, 600);
        e.target.value = '';
    });

    // Listeners
    document.getElementById('mp_search').addEventListener('input', () => {
        clearTimeout(mpSearchTimeout);
        mpSearchTimeout = setTimeout(() => { mpCurrentPage=1; fetchMpImages(); }, 350);
    });
    
    document.getElementById('mp_filter').addEventListener('change', () => { 
        mpCurrentPage=1; fetchMpImages(); 
    });
</script>
