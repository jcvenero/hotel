<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Auth.php';

Auth::requireRole(['super_admin', 'admin']);

$pageTitle = 'Ajustes Globales y SEO';
$activeMenu = 'ajustes';
$isSuperAdmin = ($_SESSION['user_rol'] === 'super_admin');

require_once __DIR__ . '/includes/header.php';
?>

<div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4 mb-8">
    <div>
        <h2 class="text-gray-800 text-lg font-semibold">Configuración del Sitio</h2>
        <p class="text-gray-500 text-sm">Administra la base SEO, información del Hotel y claves de API.</p>
    </div>
    <div class="flex items-center gap-3">
        <!-- Multi-language Toggle UX -->
        <div class="flex bg-gray-200 rounded-lg p-1 border border-gray-300">
            <button onclick="switchLang('es')" id="btnLangES" class="px-4 py-1 text-sm font-medium rounded-md bg-white shadow-sm transition-all focus:outline-none flex items-center gap-1">🇪🇸 ES</button>
            <button onclick="switchLang('en')" id="btnLangEN" class="px-4 py-1 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all focus:outline-none flex items-center gap-1">🇺🇸 EN</button>
        </div>
        <button onclick="saveSettings()" class="bg-admin-accent hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium shadow-sm transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
            Guardar Cambios
        </button>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-8">
    <!-- Sidebar Navegación Interna Tabs -->
    <div class="w-full lg:w-64 flex-shrink-0">
        <nav class="space-y-1">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-4">Básico</h4>
            <button onclick="changeTab('general')" id="tab_general" class="w-full text-left px-4 py-3 bg-blue-50 text-admin-accent font-medium rounded-lg transition-colors border-l-4 border-admin-accent flex items-center gap-2">General y RRSS</button>
            <button onclick="changeTab('seo')" id="tab_seo" class="w-full text-left px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-lg transition-colors border-l-4 border-transparent font-medium">Textos SEO / Contacto</button>
            
            <?php if ($isSuperAdmin): ?>
            <h4 class="text-xs font-bold text-purple-500 uppercase tracking-wider mt-6 mb-3 px-4 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Solo Super Admin
            </h4>
            <button onclick="changeTab('geo')" id="tab_geo" class="w-full text-left px-4 py-3 text-gray-600 hover:bg-purple-50 rounded-lg transition-colors border-l-4 border-transparent font-medium">Geo y Avanzado</button>
            <button onclick="changeTab('schema')" id="tab_schema" class="w-full text-left px-4 py-3 text-gray-600 hover:bg-purple-50 rounded-lg transition-colors border-l-4 border-transparent font-medium">Schema.org Base</button>
            <button onclick="changeTab('api')" id="tab_api" class="w-full text-left px-4 py-3 text-gray-600 hover:bg-purple-50 rounded-lg transition-colors border-l-4 border-transparent font-medium">Google Gemini API</button>
            <?php endif; ?>
        </nav>
    </div>

    <!-- Contenido del Formulario Global -->
    <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 p-8" id="settingsForm">
        
        <!-- Pestaña: General -->
        <div id="panel_general" class="tab-panel">
            <h3 class="text-xl font-bold mb-6 text-gray-800">Información del Hotel e Identidad</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre Hotel Multilenguaje -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Comercial del Hotel <span class="text-xs text-blue-500 font-normal ml-2 tracking-wider" id="lbl_hotel_name_lang">(ES)</span></label>
                    <input type="text" id="hotel_name_es" class="lang-field lang-es w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-admin-accent outline-none">
                    <input type="text" id="hotel_name_en" class="lang-field lang-en hidden w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-1 focus:ring-admin-accent bg-blue-50 outline-none" placeholder="Hotel Name in English">
                </div>
                
                <div class="md:col-span-2 relative">
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-gray-700">Descripción Corta / Eslogan <span class="text-xs text-blue-500 font-normal ml-2 tracking-wider" id="lbl_hotel_desc_lang">(ES)</span></label>
                        <button type="button" onclick="window.generateAI('hotel_description')" class="ai-btn-es flex items-center gap-1 text-xs bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded-full font-medium transition-colors shadow-sm">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            IA Magic
                        </button>
                    </div>
                    <textarea id="hotel_description_es" rows="3" class="lang-field lang-es w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-admin-accent outline-none"></textarea>
                    
                    <div class="lang-field lang-en hidden relative">
                        <textarea id="hotel_description_en" rows="3" class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-1 focus:ring-admin-accent bg-blue-50 outline-none" placeholder="Short description in english..."></textarea>
                        <button type="button" onclick="window.translateField('hotel_description_es', 'hotel_description_en')" class="absolute bottom-2 right-2 text-xs bg-blue-200 hover:bg-blue-300 text-blue-800 px-2 py-1 rounded transition-colors shadow-sm">
                            Traducir del ES
                        </button>
                    </div>
                </div>
                
                <!-- IDENTIDAD Y LOGOTIPOS -->
                <h4 class="md:col-span-2 text-md font-bold text-gray-800 border-b pb-2">Identidad Visual y Logotipos</h4>
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    
                    <!-- Logo Principal -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col gap-3 relative shadow-sm">
                        <label class="block text-sm font-bold text-gray-700">Logotipo Principal <span class="text-xs font-normal text-gray-500">(Header)</span></label>
                        <div id="prev_logo_main" class="h-20 bg-white border border-gray-200 rounded flex items-center justify-center relative overflow-hidden group">
                            <div class="absolute inset-0 opacity-[0.02]" style="background-image: radial-gradient(#000 1px, transparent 1px); background-size: 8px 8px;"></div>
                            <span class="text-xs text-gray-400 font-medium z-10 prev_empty_txt">Ninguno</span>
                            <img src="" class="hidden max-h-full max-w-full drop-shadow-md relative z-10 p-1 object-contain prev_img">
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="logo_main" readonly class="w-full text-xs px-2 py-1.5 bg-white border border-gray-300 rounded text-gray-500 font-mono" placeholder="/ruta/imagen.webp">
                            <button type="button" onclick="window.openMediaPicker((img)=>setMediaValue('logo_main', img))" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded font-medium shadow-sm whitespace-nowrap">Elegir Logo</button>
                            <button type="button" onclick="clearMediaValue('logo_main')" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded font-bold shadow-sm" title="Quitar logotipo">&times;</button>
                        </div>
                    </div>

                    <!-- Logo Secundario -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col gap-3 relative shadow-sm">
                        <label class="block text-sm font-bold text-gray-700">Logotipo Secundario <span class="text-xs font-normal text-gray-500">(Footer/Invertido)</span></label>
                        <div id="prev_logo_secondary" class="h-20 bg-gray-800 border border-gray-700 rounded flex items-center justify-center relative overflow-hidden group">
                            <div class="absolute inset-0 opacity-[0.1]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 8px 8px;"></div>
                            <span class="text-xs text-gray-400 font-medium z-10 prev_empty_txt">Ninguno</span>
                            <img src="" class="hidden max-h-full max-w-full drop-shadow-lg relative z-10 p-1 object-contain prev_img">
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="logo_secondary" readonly class="w-full text-xs px-2 py-1.5 bg-white border border-gray-300 rounded text-gray-500 font-mono" placeholder="/ruta/imagen.webp">
                            <button type="button" onclick="window.openMediaPicker((img)=>setMediaValue('logo_secondary', img))" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded font-medium shadow-sm whitespace-nowrap">Elegir Logo</button>
                            <button type="button" onclick="clearMediaValue('logo_secondary')" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded font-bold shadow-sm" title="Quitar logotipo">&times;</button>
                        </div>
                    </div>

                    <!-- Favicon -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col gap-3 relative shadow-sm">
                        <label class="block text-sm font-bold text-gray-700">Favicon <span class="text-xs font-normal text-gray-500">(.ico, .svg 32x32px)</span></label>
                        <div id="prev_logo_favicon" class="h-16 bg-white border border-gray-200 rounded flex items-center justify-center relative overflow-hidden group">
                            <span class="text-xs text-gray-400 font-medium z-10 prev_empty_txt">Ninguno</span>
                            <img src="" class="hidden max-h-8 max-w-8 relative z-10 object-contain prev_img">
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="logo_favicon" readonly class="w-full text-xs px-2 py-1.5 bg-white border border-gray-300 rounded text-gray-500 font-mono" placeholder="/ruta/favicon.ico">
                            <button type="button" onclick="window.openMediaPicker((img)=>setMediaValue('logo_favicon', img))" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded font-medium shadow-sm whitespace-nowrap">Elegir Icono</button>
                            <button type="button" onclick="clearMediaValue('logo_favicon')" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded font-bold shadow-sm" title="Quitar">&times;</button>
                        </div>
                    </div>

                    <!-- Social / OG Image -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex flex-col gap-3 relative shadow-sm">
                        <label class="block text-sm font-bold text-gray-700">OG / Social Image <span class="text-xs font-normal text-gray-500">(1200x630px min)</span></label>
                        <div id="prev_logo_og" class="h-16 bg-white border border-gray-200 rounded flex items-center justify-center relative overflow-hidden group">
                            <span class="text-xs text-gray-400 font-medium z-10 prev_empty_txt">Ninguno</span>
                            <img src="" class="hidden max-h-full max-w-full relative z-10 object-cover opacity-80 prev_img">
                        </div>
                        <div class="flex gap-2">
                            <input type="text" id="logo_og" readonly class="w-full text-xs px-2 py-1.5 bg-white border border-gray-300 rounded text-gray-500 font-mono" placeholder="/ruta/social.webp">
                            <button type="button" onclick="window.openMediaPicker((img)=>setMediaValue('logo_og', img))" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded font-medium shadow-sm whitespace-nowrap">Elegir Foto</button>
                            <button type="button" onclick="clearMediaValue('logo_og')" class="bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded font-bold shadow-sm" title="Quitar">&times;</button>
                        </div>
                    </div>

                </div>

                <h4 class="md:col-span-2 text-md font-bold mt-6 text-gray-800 border-b pb-2">Redes Sociales Dinámicas</h4>
                <div class="md:col-span-2 space-y-3" id="social_networks_container"></div>
                <div class="md:col-span-2 mb-4">
                    <button type="button" onclick="addRepeater('social_networks')" class="text-sm text-admin-accent hover:underline font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Agregar Red Social</button>
                </div>

                <h4 class="md:col-span-2 text-md font-bold mt-4 text-gray-800 border-b pb-2">Plataformas de Hotel (OTAs)</h4>
                <div class="md:col-span-2 space-y-3" id="hotel_platforms_container"></div>
                <div class="md:col-span-2 mb-4">
                    <button type="button" onclick="addRepeater('hotel_platforms')" class="text-sm text-admin-accent hover:underline font-medium flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Agregar Plataforma</button>
                </div>
                
                <h4 class="md:col-span-2 text-md font-bold mt-2 text-gray-800 border-b pb-2">WhatsApp Flotante</h4>
                <div class="flex items-center gap-4 mt-2 md:col-span-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Botón WhatsApp Número</label>
                        <input type="text" id="whatsapp_number" placeholder="+123456789" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none max-w-xs">
                    </div>
                    <div class="pt-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="whatsapp_enabled" value="1" class="w-5 h-5 rounded text-admin-accent focus:ring-admin-accent">
                            <span class="text-sm font-medium text-gray-700">Activar botón flotante en la web pública</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pestaña: SEO y Contacto -->
        <div id="panel_seo" class="tab-panel hidden">
            <h3 class="text-xl font-bold mb-6 text-gray-800">Metadatos SEO y Contacto</h3>
            
            <div class="space-y-6">
                <!-- Seccion Contacto -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg border border-gray-100 mb-6">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Teléfono Público</label><input type="text" id="contact_phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Email Público de Recepción</label><input type="email" id="contact_email" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none"></div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección Física del Hotel <span class="text-xs text-blue-500 font-normal ml-2 tracking-wider" id="lbl_address_lang">(ES)</span></label>
                    <input type="text" id="address_es" class="lang-field lang-es w-full px-4 py-2 border border-gray-300 rounded-lg outline-none">
                    <input type="text" id="address_en" class="lang-field lang-en hidden w-full px-4 py-2 border border-blue-300 rounded-lg bg-blue-50 outline-none">
                </div>

                <hr class="mb-4 border-gray-200">

                <!-- Seccion SEO -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título SEO Base Global <span class="text-xs text-blue-500 font-normal ml-2 tracking-wider" id="lbl_seo_title_lang">(ES)</span></label>
                    <input type="text" id="seo_title_es" class="lang-field lang-es w-full px-4 py-2 border border-gray-300 rounded-lg outline-none max-w-2xl">
                    
                    <div class="lang-field lang-en hidden relative max-w-2xl">
                        <input type="text" id="seo_title_en" class="w-full px-4 py-2 border border-blue-300 rounded-lg bg-blue-50 outline-none">
                        <button type="button" onclick="window.translateField('seo_title_es', 'seo_title_en')" class="absolute top-1 right-2 text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded">Traducir</button>
                    </div>
                </div>
                
                <div class="relative max-w-2xl">
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-gray-700">Meta Descripción SEO Base <span class="text-xs text-blue-500 font-normal ml-2 tracking-wider" id="lbl_seo_desc_lang">(ES)</span></label>
                        <button type="button" onclick="window.generateAI('seo_description', 'Escribe una meta description SEO muy atractiva de máximo 150 caracteres para la portada del sitio web del hotel.')" class="ai-btn-es flex items-center gap-1 text-xs bg-purple-100 hover:bg-purple-200 text-purple-700 px-3 py-1 rounded-full font-medium transition-colors shadow-sm">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Sugerir con IA
                        </button>
                    </div>
                    <textarea id="seo_description_es" rows="2" class="lang-field lang-es w-full px-4 py-2 border border-gray-300 rounded-lg outline-none"></textarea>
                    
                    <div class="lang-field lang-en hidden relative">
                        <textarea id="seo_description_en" rows="2" class="w-full px-4 py-2 border border-blue-300 rounded-lg bg-blue-50 outline-none"></textarea>
                        <button type="button" onclick="window.translateField('seo_description_es', 'seo_description_en')" class="absolute bottom-2 right-2 text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded">Traducir</button>
                    </div>
                </div>
                
                <div>
                    <div class="flex items-center justify-between max-w-2xl">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Palabras Clave (SEO Keywords Principales) <span class="text-xs text-blue-500 font-normal ml-2 tracking-wider" id="lbl_seo_keys_lang">(ES)</span></label>
                        <button type="button" onclick="window.generateAI('seo_keywords', 'Lista 10 palabras claves o short tail keywords separadas por comas para el posicionamiento principal de este hotel.')" class="ai-btn-es text-xs text-purple-600 font-medium hover:underline">Sugerir keywords ↑</button>
                    </div>
                    <input type="text" id="seo_keywords_es" class="lang-field lang-es w-full px-4 py-2 border border-gray-300 rounded-lg outline-none max-w-2xl">
                    <div class="lang-field lang-en hidden relative max-w-2xl">
                        <input type="text" id="seo_keywords_en" class="w-full px-4 py-2 border border-blue-300 rounded-lg bg-blue-50 outline-none">
                        <button type="button" onclick="window.translateField('seo_keywords_es', 'seo_keywords_en')" class="absolute top-1 right-2 text-xs bg-blue-200 text-blue-800 px-2 py-1 rounded">Traducir</button>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($isSuperAdmin): ?>
        <!-- Pestaña: GEO -->
        <div id="panel_geo" class="tab-panel hidden">
            <h3 class="text-xl font-bold mb-6 text-purple-900 border-b border-purple-100 pb-2">Localización Geoespacial Analítica</h3>
            <p class="text-sm text-gray-600 mb-4">Estas coordenadas se enviarán a Google mediante el LocalBusiness Schema.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitud G-Maps</label>
                    <input type="text" id="geo_latitud" placeholder="Ej: -13.161099" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitud G-Maps</label>
                    <input type="text" id="geo_longitud" placeholder="Ej: -72.545199" class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL de Google Maps Business</label>
                    <input type="url" id="google_my_business_url" placeholder="https://g.page/..." class="w-full px-4 py-2 border border-gray-300 rounded-lg outline-none">
                </div>
            </div>
        </div>

        <!-- Pestaña: API & IA -->
        <div id="panel_api" class="tab-panel hidden">
            <h3 class="text-xl font-bold mb-6 text-purple-900 border-b border-purple-100 pb-2">API Gemini & Machine Learning</h3>
            <div class="p-5 border border-purple-100 bg-purple-50 rounded-lg mb-6 flex gap-4 shadow-inner">
                <div class="mt-1 text-purple-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 mb-1">Google Gemini 1.5 - API Token</h4>
                    <p class="text-sm text-gray-700 mb-2">La llave es obligatoria para activar los botones interactivos mágicos, análisis de score SEO, recomendación de palabras clave y traductor instantáneo.</p>
                    <input type="password" id="gemini_api_key" placeholder="AIzaSy..." class="w-full px-4 py-2 border border-purple-300 rounded-lg outline-none font-mono text-purple-900 mb-4">
                    
                    <h4 class="font-bold text-gray-900 mb-1 mt-4 border-t border-purple-100 pt-4">Contexto Maestro del Hotel (System Prompt)</h4>
                    <p class="text-sm text-gray-700 mb-2">Instrucciones universales para la IA. Describe aquí la personalidad, el tono y el enfoque comercial para que Gemini sepa cómo redactar (ej: "Eres el copywriter de un hotel boutique en Cusco para aventureros. Usa tono cálido e inspirador.").</p>
                    <textarea id="gemini_system_prompt" rows="3" class="w-full px-4 py-2 border border-purple-300 rounded-lg outline-none focus:ring-1 focus:ring-purple-400 bg-white" placeholder="Escribe el alma de tu hotel aquí..."></textarea>
                </div>
            </div>
        </div>

        <!-- Pestaña: Schema Avanzado -->
        <div id="panel_schema" class="tab-panel hidden">
            <h3 class="text-xl font-bold mb-6 text-purple-900 border-b border-purple-100 pb-2">Inyección Manual de JSON-LD</h3>
            <p class="text-sm text-gray-600 mb-4">Por defecto, el sistema arma el esquema LocalBusiness automáticamente. Si quieres sobreescribirlo, usa esto con cuidado.</p>
            <textarea id="schema_json" rows="12" class="w-full px-4 py-3 border border-gray-300 rounded-lg outline-none font-mono text-sm bg-gray-900 text-green-400" placeholder='{ "@context": "https://schema.org", ... }'></textarea>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
    // UX Logic & Multi-language tabs overlay
    let currentLang = 'es';

    function switchLang(lang) {
        currentLang = lang;
        
        // Mover colores de botones
        const btnEs = document.getElementById('btnLangES');
        const btnEn = document.getElementById('btnLangEN');
        
        if (lang === 'es') {
            btnEs.className = "px-4 py-1 text-sm font-medium rounded-md bg-white shadow-sm transition-all focus:outline-none flex items-center gap-1";
            btnEn.className = "px-4 py-1 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all focus:outline-none flex items-center gap-1";
        } else {
            btnEn.className = "px-4 py-1 text-sm font-medium rounded-md bg-blue-100 shadow-sm transition-all text-blue-800 focus:outline-none flex items-center gap-1";
            btnEs.className = "px-4 py-1 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all focus:outline-none flex items-center gap-1";
        }

        // Intercambiar visibilidad de inputs ocultando los que no pertenecen al idioma
        document.querySelectorAll('.lang-field').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll(`.lang-${lang}`).forEach(el => el.classList.remove('hidden'));

        // Ocultar botones IA en ingles para forzar al usuario a usar el Traductor
        if (lang === 'es') {
            document.querySelectorAll('.ai-btn-es').forEach(el => el.style.display = 'flex');
        } else {
            document.querySelectorAll('.ai-btn-es').forEach(el => el.style.display = 'none');
        }

        // Modificar textos informativos (ES) -> (EN)
        const lbls = document.querySelectorAll('[id^="lbl_"]');
        lbls.forEach(l => l.innerText = `(${lang.toUpperCase()})`);
    }

    // Repeaters Logic & Icon Auto-complete
    const iconList = [
        { c: 'fa-brands fa-facebook', n: 'Facebook' },
        { c: 'fa-brands fa-instagram', n: 'Instagram' },
        { c: 'fa-brands fa-tiktok', n: 'TikTok' },
        { c: 'fa-brands fa-x-twitter', n: 'Twitter / X' },
        { c: 'fa-brands fa-youtube', n: 'YouTube' },
        { c: 'fa-brands fa-whatsapp', n: 'WhatsApp' },
        { c: 'fa-brands fa-tripadvisor', n: 'TripAdvisor' },
        { c: 'fa-brands fa-airbnb', n: 'Airbnb' },
        { c: 'fa-brands fa-spotify', n: 'Spotify' },
        { c: 'fa-brands fa-pinterest', n: 'Pinterest' },
        { c: 'fa-brands fa-vimeo', n: 'Vimeo' },
        { c: 'fa-brands fa-telegram', n: 'Telegram' },
        { c: 'fa-brands fa-github', n: 'GitHub' },
        { c: 'fa-solid fa-bed', n: 'Booking.com o Agoda' },
        { c: 'fa-solid fa-plane', n: 'Expedia o Vuelos' },
        { c: 'fa-solid fa-hotel', n: 'Hostelworld o Hotel' },
        { c: 'fa-solid fa-globe', n: 'Página Web' },
        { c: 'fa-solid fa-map-location-dot', n: 'Ubicación Física' },
        { c: 'fa-solid fa-star', n: 'Reseñas Externas' },
        { c: 'fa-solid fa-envelope', n: 'Correo Directo' }
    ];

    let repeaterState = {
        social_networks: [],
        hotel_platforms: []
    };

    function renderRepeater(type) {
        const container = document.getElementById(type + '_container');
        if (!container) return;
        container.innerHTML = '';
        
        if (repeaterState[type].length === 0) {
            container.innerHTML = `<p class="text-sm text-gray-400 italic py-2">No hay elementos configurados. Haz clic en agregar.</p>`;
            return;
        }
        
        repeaterState[type].forEach((item, index) => {
            // Reemplazar comillas dobles en valores para no quebrar el HTML
            const iIcon = (item.icon || '').replace(/"/g, '&quot;');
            const iName = (item.name || '').replace(/"/g, '&quot;');
            const iUrl  = (item.url || '').replace(/"/g, '&quot;');
            
            container.innerHTML += `
                <div class="flex flex-col md:flex-row gap-3 items-center bg-gray-50 p-3 rounded-lg border border-gray-200 shadow-sm relative group overflow-visible">
                    <div class="w-full md:w-3/12 relative group">
                        <div class="flex items-center border border-gray-300 rounded focus-within:ring-1 focus-within:ring-admin-accent bg-white h-10">
                            <span class="w-8 flex justify-center text-gray-500" id="preview_icon_${type}_${index}">
                                ${iIcon.startsWith('<svg') ? '<svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>' : (iIcon ? `<i class="${iIcon}"></i>` : `<i class="fa-solid fa-magnifying-glass"></i>`)}
                            </span>
                            <input type="text" placeholder="Buscar icono o logo..." 
                                   class="w-full text-sm py-2 pr-2 outline-none" 
                                   oninput="updateRepeater('${type}', ${index}, 'icon', this.value); searchIcons(this, '${type}', ${index})" 
                                   onfocus="searchIcons(this, '${type}', ${index})"
                                   onblur="setTimeout(() => closeIconSearch('${type}', ${index}), 200)"
                                   value="${iIcon}">
                        </div>
                        <div id="icon_dropdown_${type}_${index}" class="absolute z-20 w-full left-0 bg-white border border-gray-200 mt-1 rounded shadow-lg hidden max-h-48 overflow-y-auto"></div>
                    </div>
                    <div class="w-full md:w-3/12">
                        <input type="text" placeholder="Nombre (ej. Booking)" class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-admin-accent outline-none h-10" oninput="updateRepeater('${type}', ${index}, 'name', this.value)" value="${iName}">
                    </div>
                    <div class="w-full md:w-6/12 flex gap-2">
                        <input type="url" placeholder="https://..." class="w-full text-sm px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-admin-accent outline-none h-10" oninput="updateRepeater('${type}', ${index}, 'url', this.value)" value="${iUrl}">
                        <button type="button" onclick="removeRepeater('${type}', ${index})" class="text-red-500 hover:bg-red-50 px-3 h-10 rounded transition-colors" title="Eliminar fila"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                    </div>
                </div>
            `;
        });
    }

    function addRepeater(type) {
        repeaterState[type].push({icon: '', name: '', url: ''});
        renderRepeater(type);
    }

    function removeRepeater(type, index) {
        repeaterState[type].splice(index, 1);
        renderRepeater(type);
    }

    function updateRepeater(type, index, field, value) {
        repeaterState[type][index][field] = value;
    }

    // Icon Dropdown Autocomplete system
    function searchIcons(input, type, index) {
        const val = input.value.toLowerCase();
        const drop = document.getElementById(`icon_dropdown_${type}_${index}`);
        const preview = document.getElementById(`preview_icon_${type}_${index}`);
        
        // Preview on the fly
        if(input.value.trim().startsWith('<svg')) {
            preview.innerHTML = '<svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24"><path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/></svg>';
        } else if (input.value.trim() !== '') {
            preview.innerHTML = `<i class="${input.value}"></i>`;
        } else {
            preview.innerHTML = `<i class="fa-solid fa-magnifying-glass"></i>`;
        }

        const exactSVG = input.value.trim().startsWith('<svg');
        if (exactSVG) {
            drop.classList.add('hidden');
            return;
        }

        const filtered = iconList.filter(i => i.n.toLowerCase().includes(val) || i.c.includes(val));
        
        if (filtered.length === 0) {
            drop.innerHTML = `<div class="px-3 py-2 text-xs text-gray-400 italic">No hay resultados. Escribe clase FontAwesome o pega un código &lt;svg&gt;.</div>`;
        } else {
            drop.innerHTML = filtered.map(i => `
                <div class="px-3 py-2 hover:bg-gray-100 cursor-pointer flex items-center gap-3 text-sm text-gray-700" onclick="selectIcon('${type}', ${index}, '${i.c}')">
                    <span class="w-5 text-center"><i class="${i.c}"></i></span> <span>${i.n}</span>
                </div>
            `).join('');
        }
        drop.classList.remove('hidden');
    }

    window.selectIcon = function(type, index, iconClass) {
        updateRepeater(type, index, 'icon', iconClass);
        renderRepeater(type);
    }

    function closeIconSearch(type, index) {
        const drop = document.getElementById(`icon_dropdown_${type}_${index}`);
        if(drop) drop.classList.add('hidden');
    }

    function changeTab(tabName) {
        // Desactivar todos los botones de tabs
        const tabs = ['general', 'seo', 'geo', 'api', 'schema'];
        
        tabs.forEach(t => {
            const el = document.getElementById('tab_' + t);
            if(el) {
                // Determine base style based on tab group
                let isAdvance = ['geo', 'api', 'schema'].includes(t);
                let baseClass = isAdvance ? "text-gray-600 hover:bg-purple-50" : "text-gray-600 hover:bg-gray-50";
                el.className = `w-full text-left px-4 py-3 ${baseClass} rounded-lg transition-colors border-l-4 border-transparent font-medium`;
            }
            const p = document.getElementById('panel_' + t);
            if(p) p.classList.add('hidden');
        });

        // Activar seleccionado
        const tBtn = document.getElementById('tab_' + tabName);
        if(tBtn) {
            let isAdvance = ['geo', 'api', 'schema'].includes(tabName);
            let activeClass = isAdvance ? "bg-purple-50 text-purple-700 border-purple-500" : "bg-blue-50 text-admin-accent border-admin-accent";
            tBtn.className = `w-full text-left px-4 py-3 ${activeClass} font-medium rounded-lg transition-colors border-l-4 flex items-center gap-2`;
        }
        
        const pSel = document.getElementById('panel_' + tabName);
        if(pSel) pSel.classList.remove('hidden');
    }

    // Google Gemini Integation (Frontend functions)
    window.generateAI = async function(fieldId, explicitPrompt = null) {
        const tgtArea = document.getElementById(fieldId + '_es');
        if (!tgtArea) return;

        let prompt = explicitPrompt;
        if (!prompt) {
            const contextName = document.getElementById('hotel_name_es')?.value || 'hotel';
            prompt = `Escribe un texto atractivo en español acerca de: ${contextName}. El campo a rellenar es ${fieldId.replace('_',' ')}. Sé creativo y persuasivo. Devuelve ÚNICAMENTE el texto generado, sin comillas, sin markdown y sin aclaraciones.`;
        } else {
            prompt += " Devuelve ÚNICAMENTE el texto solicitado, sin comillas, sin formato markdown y sin texto conversacional previo ni posterior.";
        }

        const btn = event.currentTarget;
        const oriContent = btn.innerHTML;
        btn.innerHTML = 'Pensando...';
        btn.classList.add('opacity-75');

        try {
            const res = await apiCall('/hotel/api/ai/generar.php', 'POST', { prompt });
            if (res.exito) {
                // Remove trailing quotes if AI still hallucinates them
                let text = res.data.replace(/^["']|["']$/g, '').trim();
                tgtArea.value = text;
            } else {
                alert(res.error);
            }
        } catch (e) {
            alert('Falló conexión con endpoint AI');
        } finally {
            btn.innerHTML = oriContent;
            btn.classList.remove('opacity-75');
        }
    }

    window.translateField = async function(srcId, dstId) {
        const srcText = document.getElementById(srcId)?.value;
        const tgtArea = document.getElementById(dstId);
        if (!srcText) return alert("El campo original (Español) está vacío.");
        if (!tgtArea) return;

        const prompt = `Traduce el siguiente texto del español al inglés manteniendo el tono comercial y el SEO. \n\nTexto a traducir:\n${srcText}\n\nREGLA ESTRICTA: Devuelve ÚNICAMENTE la traducción, absolutamente nada de texto conversacional como "Aquí tienes", ni comillas envolventes, ni markdown.`;

        const btn = event.currentTarget;
        const oriContent = btn.innerHTML;
        btn.innerHTML = 'Trad...';

        try {
            const res = await apiCall('/hotel/api/ai/generar.php', 'POST', { prompt });
            if (res.exito) {
                tgtArea.value = res.data;
            } else {
                alert(res.error);
            }
        } catch (e) {
            alert('Falló conexión con endpoint AI');
        } finally {
            btn.innerHTML = oriContent;
        }
    }

    // Load Data
    document.addEventListener("DOMContentLoaded", async () => {
        // Inicializar repeaters vacíos por defecto
        renderRepeater('social_networks');
        renderRepeater('hotel_platforms');

        try {
            const req = await apiCall('/hotel/api/ajustes/obtener.php');
            if (req.exito && req.data) {
                mapDataToForm(req.data);
            }
        } catch (e) { console.error('Error fetching settings'); }
    });

    function mapDataToForm(data) {
        Object.keys(data).forEach(key => {
            let val = data[key];
            if (Array.isArray(val) && (key === 'social_networks' || key === 'hotel_platforms')) {
                repeaterState[key] = val;
                renderRepeater(key);
                return;
            }

            if (typeof val === 'object' && val !== null) {
                if (val.es && document.getElementById(key + '_es')) document.getElementById(key + '_es').value = val.es;
                if (val.en && document.getElementById(key + '_en')) document.getElementById(key + '_en').value = val.en;
                if (key === 'schema_json' && document.getElementById('schema_json')) {
                    document.getElementById('schema_json').value = JSON.stringify(val, null, 2);
                }
            } else {
                if (document.getElementById(key)) {
                    if (document.getElementById(key).type === 'checkbox') {
                        document.getElementById(key).checked = (val == 1 || val === '1');
                    } else {
                        document.getElementById(key).value = val;
                    }
                }
            }
        });
        
        // Specific fields that might not be handled by the generic loop or need special treatment
        if (document.getElementById('whatsapp_number')) {
            document.getElementById('whatsapp_number').value = data.whatsapp_number || '';
        }
        if (document.getElementById('whatsapp_enabled')) {
            document.getElementById('whatsapp_enabled').checked = (parseInt(data.whatsapp_enabled) === 1);
        }

        // Pre-load Media Visuals
        ['logo_main', 'logo_secondary', 'logo_favicon', 'logo_og'].forEach(k => {
            if (data[k]) setMediaValue(k, {ruta_original: data[k]}); // Fake object format
        });
    }

    async function saveSettings() {
        // Render empty repeaters initially on load if empty, handle this via init call
        const payload = {};
        const singles = [
            'contact_phone', 'contact_email', 'geo_latitud', 'geo_longitud', 
            'google_my_business_url', 'gemini_api_key', 'gemini_system_prompt', 'whatsapp_number',
            'logo_main', 'logo_secondary', 'logo_favicon', 'logo_og'
        ];
        singles.forEach(s => {
            const el = document.getElementById(s);
            if(el) payload[s] = el.value;
        });

        const chks = ['whatsapp_enabled'];
        chks.forEach(c => {
            const el = document.getElementById(c);
            if(el) payload[c] = el.checked ? 1 : 0;
        });

        // Add Repeaters
        payload['social_networks'] = repeaterState['social_networks'];
        payload['hotel_platforms'] = repeaterState['hotel_platforms'];

        const schema = document.getElementById('schema_json');
        if (schema) {
            try {
                payload['schema_json'] = schema.value.trim() ? JSON.parse(schema.value) : {};
            } catch(e) {
                alert("Error de sintaxis JSON en Schema.org.");
                return;
            }
        }

        const bilingualFields = ['hotel_name', 'hotel_description', 'seo_title', 'seo_description', 'seo_keywords', 'address'];
        bilingualFields.forEach(f => {
            const es = document.getElementById(f + '_es') ? document.getElementById(f + '_es').value : '';
            const en = document.getElementById(f + '_en') ? document.getElementById(f + '_en').value : '';
            payload[f] = { es, en };
        });

        const btn = document.querySelector('button[onclick="saveSettings()"]');
        const origText = btn.innerHTML;
        btn.innerText = "Guardando...";

        try {
            const res = await apiCall('/hotel/api/ajustes/guardar.php', 'POST', payload);
            if (res.exito) {
                alert("¡Configuraciones guardadas!");
            } else {
                alert("Error: " + res.error);
            }
        } catch (e) {
            alert("Ocurrió un error.");
        } finally {
            btn.innerHTML = origText;
        }
    }

    // Funciones del Media Picker en Ajustes
    window.setMediaValue = function(inputId, imgObj) {
        document.getElementById(inputId).value = imgObj.ruta_original;
        const box = document.getElementById('prev_' + inputId);
        if(box) {
            box.querySelector('.prev_empty_txt').classList.add('hidden');
            const img = box.querySelector('.prev_img');
            img.src = imgObj.ruta_original;
            img.classList.remove('hidden');
        }
    }

    window.clearMediaValue = function(inputId) {
        document.getElementById(inputId).value = '';
        const box = document.getElementById('prev_' + inputId);
        if(box) {
            box.querySelector('.prev_empty_txt').classList.remove('hidden');
            const img = box.querySelector('.prev_img');
            img.src = '';
            img.classList.add('hidden');
        }
    }
</script>

<?php require_once __DIR__ . '/includes/media-picker.php'; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
