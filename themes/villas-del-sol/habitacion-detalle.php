<?php
/**
 * Página Detalle de Habitación + Reserva - Tema Villas del Sol (Refinado V3.1 - Final Touches)
 */
require_once THEME_PATH . '/layout/header.php';

$slug = $_GET['slug'] ?? '';
$lang = Language::current();

// 1. DATA FETCHING
$stmt = $db->prepare("SELECT h.*, 
                             hi.nombre as nombre_hab, hi.descripcion, 
                             hc.comodidades as json_comodidades, 
                             ha.amenities as json_amenities
                      FROM habitaciones h 
                      JOIN habitaciones_idiomas hi ON h.id = hi.habitacion_id AND hi.idioma = ?
                      LEFT JOIN habitacion_comodidades hc ON h.id = hc.habitacion_id AND hc.idioma = ?
                      LEFT JOIN habitacion_amenities ha ON h.id = ha.habitacion_id AND ha.idioma = ?
                      WHERE h.id = ? OR h.slug = ?");
$stmt->execute([$lang, $lang, $lang, $slug, $slug]);
$h = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$h) {
    echo "<script>window.location.href='/hotel/404.php';</script>";
    exit;
}

// 2. IMÁGENES
$stmt = $db->prepare("SELECT i.* FROM imagenes i JOIN habitacion_imagenes hi ON i.id = hi.imagen_id WHERE hi.habitacion_id = ? ORDER BY hi.es_principal DESC");
$stmt->execute([$h['id']]);
$imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. FAQs
$stmt = $db->prepare("SELECT * FROM habitacion_faqs WHERE habitacion_id = ? AND idioma = ? ORDER BY orden ASC");
$stmt->execute([$h['id'], $lang]);
$faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. ICON MAPPER
function getSmartIcon($icon, $text) {
    $text = strtolower($text);
    $textMap = [
        'escritorio' => 'fa-laptop-code', 'desk' => 'fa-laptop-code',
        'secador' => 'fa-wind', 'hair' => 'fa-wind',
        'armario' => 'fa-box-archive', 'wardrobe' => 'fa-box-archive',
        'percha' => 'fa-shirt', 'caja fuerte' => 'fa-vault',
        'safe' => 'fa-vault', 'cafetera' => 'fa-mug-hot',
        'coffee' => 'fa-mug-hot', 'agua' => 'fa-bottle-water'
    ];
    foreach($textMap as $key => $val) { if(strpos($text, $key) !== false) return $val; }
    if(!empty($icon) && strpos($icon, 'fa-') !== false) return $icon;
    return 'fa-check';
}

$comodidadesData = json_decode($h['json_comodidades'], true) ?: [];
$amenitiesData = json_decode($h['json_amenities'], true) ?: [];

$totalServices = [];
foreach($comodidadesData as $c) {
    $nombre = is_array($c) ? ($c['nombre'] ?? $c['titulo'] ?? '') : $c;
    $icon = is_array($c) ? ($c['icono'] ?? '') : '';
    $totalServices[] = ['titulo' => $nombre, 'ic' => getSmartIcon($icon, $nombre)];
}
foreach($amenitiesData as $a) {
    $nombre = is_array($a) ? ($a['nombre'] ?? $a['titulo'] ?? '') : $a;
    $icon = is_array($a) ? ($a['icono'] ?? '') : '';
    $totalServices[] = ['titulo' => $nombre, 'ic' => getSmartIcon($icon, $nombre)];
}

$vistaText = ($lang === 'es' ? 'Ciudad o Patio' : 'City or Patio');
foreach($totalServices as $service) {
    if (stripos($service['titulo'], 'vista') !== false || stripos($service['titulo'], 'view') !== false) {
        $vistaText = $service['titulo']; break;
    }
}
?>

<!-- SECTION 1: HERO -->
<section class="relative min-h-[60vh] flex items-end pb-20 bg-stone-900 overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="<?= $imagenes[0]['ruta_webp'] ?? '' ?>" class="w-full h-full object-cover opacity-60" alt="Room Hero">
        <div class="absolute inset-0 bg-gradient-to-t from-stone-900 via-stone-900/10 to-transparent"></div>
    </div>
    
    <div class="container mx-auto px-6 relative z-10 text-white">
        <div class="max-w-4xl">
            <span class="text-gold font-bold text-[9px] uppercase tracking-[0.6em] block mb-4 reveal-up">HOSPITALITY CULTURE</span>
            <h1 class="text-5xl md:text-7xl font-playfair font-bold italic reveal-up leading-tight mb-8">
                <?= htmlspecialchars($h['nombre_hab']) ?>
            </h1>
            
            <div class="flex flex-wrap items-center gap-x-12 gap-y-6 reveal-up" style="animation-delay: 0.2s">
                <div class="flex items-center gap-4">
                    <i class="fa-solid fa-users text-gold text-xl opacity-70"></i>
                    <div>
                        <span class="block text-[8px] font-bold uppercase tracking-widest text-stone-500 mb-1">Capacidad</span>
                        <span class="text-sm font-bold"><?= $h['capacidad_huespedes'] ?> <?= $lang === 'es' ? 'Pers.' : 'Guests' ?></span>
                    </div>
                </div>
                <div class="flex items-center gap-4 border-l border-white/10 pl-10">
                    <i class="fa-solid fa-bed text-gold text-xl opacity-70"></i>
                    <div>
                        <span class="block text-[8px] font-bold uppercase tracking-widest text-stone-500 mb-1">Camas</span>
                        <span class="text-sm font-bold"><?= $h['num_camas'] ?> Hab.</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 border-l border-white/10 pl-10">
                    <i class="fa-solid fa-maximize text-gold text-xl opacity-70"></i>
                    <div>
                        <span class="block text-[8px] font-bold uppercase tracking-widest text-stone-500 mb-1">Tamaño</span>
                        <span class="text-sm font-bold">~32 m²</span>
                    </div>
                </div>
                <div class="flex items-center gap-4 border-l border-white/10 pl-10">
                    <i class="fa-solid fa-mountain-sun text-gold text-xl opacity-70"></i>
                    <div>
                        <span class="block text-[8px] font-bold uppercase tracking-widest text-stone-500 mb-1">Vistas</span>
                        <span class="text-sm font-bold"><?= $vistaText ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SECTION 2: 2 COLUMNS -->
<section class="py-24 bg-white">
    <div class="container mx-auto px-6">
        <div class="lg:grid lg:grid-cols-12 lg:gap-20">
            
            <!-- SIDEBAR LEFT -->
            <div class="lg:col-span-4 mb-20 lg:mb-0">
                <div class="sticky top-32 space-y-8">
                    <div class="bg-white p-10 rounded-[3.5rem] shadow-[0_30px_70px_-15px_rgba(0,0,0,0.05)] border border-stone-50">
                        <div class="text-center mb-8 pb-8 border-b border-stone-100">
                            <span class="block text-[9px] font-bold uppercase tracking-[0.4em] text-stone-400 mb-2">Precio sugerido</span>
                            <div class="flex items-baseline justify-center gap-2">
                                <span class="text-5xl font-playfair font-bold text-stone-900 italic">$<?= number_format($h['precio_base'], 0) ?></span>
                                <span class="text-[10px] text-stone-400 uppercase tracking-widest">/ Noche</span>
                            </div>
                        </div>
                        
                        <div class="form-compact-premium">
                            <?= FormBuilder::render('reserva', $lang, [
                                'id' => 'form-room-booking',
                                'pagina_origen' => 'Habitación: ' . $h['nombre_hab'],
                                'tipo_entidad' => 'habitacion', 'entidad_id' => $h['id']
                            ]) ?>
                        </div>
                    </div>

                    <div class="p-8 bg-stone-50 rounded-[2.5rem] border border-stone-100 flex items-center gap-5 group cursor-pointer hover:bg-stone-100 transition-colors">
                        <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-sm text-[#25D366] text-2xl group-hover:scale-110 transition-transform">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div>
                            <p class="text-[8px] font-extrabold uppercase tracking-widest text-stone-400 mb-1">Atención Personalizada</p>
                            <a href="https://wa.me/51984200000" class="text-stone-800 font-bold text-sm hover:underline">WhatsApp Concierge</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MAIN CONTENT RIGHT -->
            <div class="lg:col-span-8 space-y-24">
                
                <!-- 1. Descripción -->
                <article class="space-y-8">
                    <h2 class="text-3xl font-playfair font-bold text-stone-900 border-l-4 border-gold pl-6 leading-none">Sobre la Estancia</h2>
                    <div class="prose prose-stone max-w-none text-stone-500 leading-relaxed text-lg font-light">
                        <?= nl2br(htmlspecialchars($h['descripcion'])) ?>
                    </div>
                </article>

                <!-- 2. Galería "Metro Style" (Influencia Pixpa Revel) -->
                <div class="space-y-12">
                     <h2 class="text-3xl font-playfair font-bold text-stone-900 border-l-4 border-gold pl-6 leading-none">Detalles en Foco</h2>
                     <div class="grid grid-cols-4 grid-rows-2 gap-4 h-[700px]">
                        <!-- Imagen 1: Grande (2x2) -->
                        <div class="col-span-2 row-span-2 rounded-[2.5rem] overflow-hidden group shadow-xl">
                            <img src="<?= $imagenes[0]['ruta_webp'] ?? '' ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Metro 1">
                        </div>
                        <!-- Imagen 2: Horizontal (2x1) -->
                        <div class="col-span-2 row-span-1 rounded-[2.5rem] overflow-hidden group shadow-lg">
                            <img src="<?= $imagenes[1]['ruta_webp'] ?? ($imagenes[0]['ruta_webp'] ?? '') ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Metro 2">
                        </div>
                        <!-- Imagen 3: 1x1 -->
                        <div class="col-span-1 row-span-1 rounded-[2.5rem] overflow-hidden group shadow-md">
                            <img src="<?= $imagenes[2]['ruta_webp'] ?? ($imagenes[0]['ruta_webp'] ?? '') ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Metro 3">
                        </div>
                        <!-- Imagen 4: 1x1 con Overlay -->
                        <div class="col-span-1 row-span-1 rounded-[2.5rem] overflow-hidden relative group shadow-md">
                            <img src="<?= $imagenes[3]['ruta_webp'] ?? ($imagenes[0]['ruta_webp'] ?? '') ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Metro 4">
                            <?php if(count($imagenes) > 4): ?>
                                <div class="absolute inset-0 bg-stone-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all cursor-pointer backdrop-blur-[2px]">
                                    <span class="text-[9px] font-bold uppercase tracking-widest text-white border border-white/20 px-4 py-2 rounded-full">+ <?= count($imagenes)-4 ?> fotos</span>
                                </div>
                            <?php endif; ?>
                        </div>
                     </div>
                </div>

                <!-- 3. Amenities -->
                <div class="space-y-12">
                    <h2 class="text-3xl font-playfair font-bold text-stone-900 border-l-4 border-gold pl-6 leading-none">Servicios & Amenities</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-y-10 gap-x-12">
                        <?php foreach($totalServices as $service): ?>
                        <div class="flex items-start gap-4 group">
                            <div class="mt-1 w-12 h-12 shrink-0 rounded-2xl bg-stone-50 flex items-center justify-center text-stone-400 group-hover:bg-gold/10 group-hover:text-gold transition-all duration-300">
                                <i class="fa-solid <?= htmlspecialchars($service['ic']) ?> text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-stone-800 uppercase text-[10px] tracking-widest mb-1"><?= htmlspecialchars($service['titulo']) ?></h4>
                                <p class="text-[9px] text-stone-400 font-light italic leading-none">Hospitality Standard</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- 4. FAQs -->
                <?php if(!empty($faqs)): ?>
                <div class="space-y-12 pb-20">
                    <h2 class="text-3xl font-playfair font-bold text-stone-900 border-l-4 border-gold pl-6 leading-none">Consultas Frecuentes</h2>
                    <div x-data="{ active: null }" class="space-y-3">
                        <?php foreach($faqs as $i => $faq): ?>
                        <div class="border border-stone-50 rounded-2xl overflow-hidden transition-all" :class="active === <?= $i ?> ? 'bg-white shadow-xl translate-x-1' : 'bg-stone-50/20'">
                            <button @click="active = active === <?= $i ?> ? null : <?= $i ?>" 
                                    class="w-full h-16 px-8 flex justify-between items-center hover:bg-white transition-all text-left">
                                <span class="font-playfair font-bold text-stone-700 tracking-tight"><?= htmlspecialchars($faq['pregunta']) ?></span>
                                <i class="fa-solid fa-plus text-[10px] transition-transform duration-500 text-stone-300" :class="active === <?= $i ?> ? 'rotate-45 text-gold' : ''"></i>
                            </button>
                            <div x-show="active === <?= $i ?>" x-collapse>
                                <div class="px-8 pb-8 pt-2 text-stone-500 font-light text-sm leading-relaxed italic border-t border-stone-100 bg-white">
                                    <?= htmlspecialchars($faq['respuesta']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<div class="mt-24">
    <?php require_once THEME_PATH . '/layout/footer.php'; ?>
</div>

<!-- Dinámica de Fechas (JS) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const arrivalInput = document.querySelector('input[name="fecha_llegada"]');
    const departureInput = document.querySelector('input[name="fecha_salida"]');
    
    if (arrivalInput && departureInput) {
        // 1. Establecer fecha mínima para llegada (HOY)
        const today = new Date().toISOString().split('T')[0];
        arrivalInput.setAttribute('min', today);
        
        // 2. Al cambiar llegada, actualizar mínima de salida
        arrivalInput.addEventListener('change', function() {
            departureInput.setAttribute('min', arrivalInput.value);
            if (departureInput.value && departureInput.value < arrivalInput.value) {
                departureInput.value = arrivalInput.value;
            }
        });
    }
});
</script>

<style>
/* FORM COMPACT - PIXPA STYLE FINESSE */
.form-compact-premium .grid {
    display: flex !important;
    flex-direction: column !important;
    gap: 1.25rem !important;
}
.form-compact-premium label {
    font-size: 9px !important;
    text-transform: uppercase !important;
    font-weight: 800 !important;
    letter-spacing: 0.15em !important;
    margin-bottom: 0.15rem !important;
    color: #44403c !important;
    opacity: 0.5;
}
.form-compact-premium input, .form-compact-premium select, .form-compact-premium textarea {
    background: transparent !important;
    border: none !important;
    border-bottom: 1px solid #f5f5f4 !important;
    padding: 0.35rem 0 !important;
    font-size: 13.5px !important;
    color: #1c1917 !important;
    width: 100% !important;
    outline: none !important;
}
.form-compact-premium input:focus { border-color: #b08d57 !important; }

/* Botón de Enviar más Pequeño y Fined */
.form-compact-premium button[type="submit"] {
    background: #1c1917 !important;
    color: #fff !important;
    border-radius: 4rem !important;
    padding: 1rem 1.5rem !important; /* Más compacto horizontalmente */
    font-weight: 800 !important;
    text-transform: uppercase !important;
    font-size: 11px !important;
    letter-spacing: 0.3em !important;
    margin-top: 2rem !important;
    width: auto !important; /* NO width full */
    min-width: 180px;
    margin-left: auto;
    margin-right: auto;
    display: block;
    cursor: pointer;
    transition: all 0.4s ease;
}
.form-compact-premium button[type="submit"]:hover {
    background: #b08d57 !important;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(176,141,87,0.2) !important;
}

.reveal-up { opacity: 0; transform: translateY(30px); animation: revealUp 1s ease forwards; }
@keyframes revealUp { to { opacity: 1; transform: translateY(0); } }
</style>
