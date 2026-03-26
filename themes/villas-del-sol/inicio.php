<?php
/**
 * Página de Inicio - Pulido Final Estético
 */
require_once THEME_PATH . '/layout/header.php';

// Obtener 5 habitaciones recomendadas
$stmt = $db->query("SELECT h.*, hi.nombre as nombre_hab, i.ruta_webp 
                    FROM habitaciones h 
                    JOIN habitaciones_idiomas hi ON h.id = hi.habitacion_id AND hi.idioma = '$lang'
                    LEFT JOIN habitacion_imagenes himg ON h.id = himg.habitacion_id AND himg.es_principal = 1
                    LEFT JOIN imagenes i ON himg.imagen_id = i.id
                    WHERE h.activa = 1 LIMIT 5");
$recomendadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$hotelInfoES = "Posada Villa Mayor es una auténtica joya de la arquitectura colonial, situada frente a la histórica Plaza Regocijo, a solo unos pasos de la majestuosa Plaza de Armas del Cusco. Nuestra casona ha sido restaurada con maestría para preservar sus imponentes muros de piedra y balcones tallados que respiran siglos de historia convirtiéndola en un refugio de paz en medio de la energía vibrante de la ciudad imperial.";
$hotelInfoEN = "Posada Villa Mayor is an authentic jewel of colonial architecture, located in front of the historic Plaza Regocijo, just steps away from the majestic Main Square of Cusco. Our mansion has been masterfully restored to preserve its imposing stone walls and carved balconies that breathe centuries of history, making it a sanctuary of peace in the midst of the imperial city's vibrant energy.";
?>

<!-- 1. HERO SECTION -->
<section class="relative h-screen flex items-center justify-center overflow-hidden bg-stone-900">
    <div class="hero-top-gradient"></div>
    <div class="absolute inset-0 z-0">
        <img src="/hotel/public/uploads/webp/portada-85b47.webp" class="w-full h-full object-cover opacity-60 ken-burns" alt="Hero Background">
    </div>
    
    <div class="container mx-auto px-6 relative z-20 text-center text-white">
        <h1 class="text-5xl md:text-8xl font-playfair font-bold mb-6 reveal-up italic tracking-tight">
            <?= $lang === 'es' ? 'El Lujo de lo Auténtico' : 'The Luxury of Authenticity' ?>
        </h1>
        <p class="text-lg md:text-xl font-outfit font-light max-w-2xl mx-auto mb-10 opacity-90 reveal-up leading-relaxed" style="animation-delay: 0.2s">
            <?= Language::get($ajustes, 'hotel_description') ?>
        </p>
        <div class="reveal-up" style="animation-delay: 0.4s">
            <a href="/hotel/habitaciones" class="bg-gold text-white px-12 py-4 rounded-full font-bold uppercase tracking-[0.2em] text-[11px] transition-all hover:bg-white hover:text-gold shadow-2xl">
                <?= $lang === 'es' ? 'Descubrir Habitaciones' : 'Discover Rooms' ?>
            </a>
        </div>
    </div>
</section>

<!-- 2. SECCIÓN ACERCA DE (Fondo Piedra Sutil) -->
<section class="py-32 bg-stone-50 overflow-hidden">
    <div class="container mx-auto px-6">
        <div class="flex flex-col lg:flex-row gap-16 items-center">
            <div class="lg:w-1/2 space-y-8 pr-8">
                <span class="text-gold font-bold text-[11px] uppercase tracking-[0.4em] block">ABOUT THE HOTEL</span>
                <h2 class="text-4xl md:text-5xl font-playfair font-bold text-stone-900 italic leading-tight">
                    <?= $lang === 'es' ? 'Servicios a medida y la experiencia de unas vacaciones únicas' : 'Tailored services and the experience of unique holidays' ?>
                </h2>
                <div class="text-stone-500 leading-loose text-base font-outfit">
                    <p class="mb-6">
                        <?= $lang === 'es' ? 'Posada Villa Mayor le invita a sumergirse en la elegancia de una casona colonial del siglo XVII, donde cada detalle ha sido diseñado para su máximo bienestar.' : 'Posada Villa Mayor invites you to immerse yourself in the elegance of a 17th-century colonial mansion, where every detail has been designed for your maximum well-being.' ?>
                    </p>
                    <p>
                        <?= $lang === 'es' ? 'Nuestra ubicación privilegiada y nuestro compromiso con la excelencia hacen de nosotros el punto de partida ideal para su aventura en la ciudad imperial.' : 'Our privileged location and our commitment to excellence make us the ideal starting point for your adventure in the imperial city.' ?>
                    </p>
                </div>
                <div class="pt-4 border-t border-stone-200">
                    <p class="font-playfair text-2xl italic text-stone-800">Maria... the Owner</p>
                </div>
            </div>
            <div class="lg:w-1/2">
                <div class="img-overlap-container">
                    <img src="/hotel/public/uploads/webp/portada-85b47.webp" class="img-overlap-main" alt="About Main">
                    <img src="/hotel/public/uploads/webp/hab-simple-60c7b.webp" class="img-overlap-sub" alt="About Sub">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. SECCIÓN HABITACIONES (Full Width with Slight Margin) -->
<section class="py-32 bg-white">
    <div class="px-8 md:px-16">
        <div class="text-center mb-16 px-6">
            <span class="text-stone-400 font-bold text-[11px] uppercase tracking-[0.4em] block mb-4">ACCOMMODATIONS</span>
            <h2 class="text-5xl font-playfair font-bold text-stone-900 italic"><?= $lang === 'es' ? 'Nuestra Selección' : 'Our Selection' ?></h2>
        </div>

        <div class="space-y-4">
            <!-- Row 1: 3 Columns -->
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 h-auto md:h-[500px]">
                <?php for($i=0; $i<3; $i++): if(isset($recomendadas[$i])): ?>
                    <div class="<?= $i === 1 ? 'md:col-span-6' : 'md:col-span-3' ?>">
                        <a href="/hotel/habitacion/<?= $recomendadas[$i]['id'] ?>" class="room-card-hostily block h-full">
                            <img src="<?= $recomendadas[$i]['ruta_webp'] ?>" class="w-full h-full object-cover" alt="<?= $recomendadas[$i]['nombre_hab'] ?>">
                            <div class="room-overlay">
                                <span class="text-gold font-bold text-xs mb-1">$<?= number_format($recomendadas[$i]['precio_base'] ?? 100, 0) ?> / Night</span>
                                <h4 class="text-2xl text-white font-playfair font-bold mb-1 italic"><?= $recomendadas[$i]['nombre_hab'] ?></h4>
                                <div class="room-action-btn">
                                    <span class="text-[10px] text-white font-bold uppercase tracking-widest flex items-center gap-2">
                                        <i class="fa-solid fa-circle-chevron-right text-gold"></i> 
                                        <?= $lang === 'es' ? 'VER DETALLES' : 'VIEW DETAILS' ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; endfor; ?>
            </div>

            <!-- Row 2: 2 Columns -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 h-auto md:h-[500px]">
                 <?php for($i=3; $i<5; $i++): if(isset($recomendadas[$i])): ?>
                    <div>
                        <a href="/hotel/habitacion/<?= $recomendadas[$i]['id'] ?>" class="room-card-hostily block h-full">
                            <img src="<?= $recomendadas[$i]['ruta_webp'] ?>" class="w-full h-full object-cover" alt="<?= $recomendadas[$i]['nombre_hab'] ?>">
                            <div class="room-overlay">
                                <span class="text-gold font-bold text-xs mb-1">$<?= number_format($recomendadas[$i]['precio_base'] ?? 200, 0) ?> / Night</span>
                                <h4 class="text-3xl text-white font-playfair font-bold mb-1 italic"><?= $recomendadas[$i]['nombre_hab'] ?></h4>
                                <div class="room-action-btn">
                                    <span class="text-[10px] text-white font-bold uppercase tracking-widest flex items-center gap-2">
                                        <i class="fa-solid fa-circle-chevron-right text-gold"></i> 
                                        <?= $lang === 'es' ? 'VER DETALLES' : 'VIEW DETAILS' ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endif; endfor; ?>
            </div>
        </div>
    </div>
</section>

<!-- 4. SECCIÓN UBICACIÓN (Full Width 2 Col) -->
<section class="flex flex-col lg:flex-row min-h-[700px]">
    <div class="lg:w-1/2 bg-stone-900 text-white p-12 md:p-24 flex flex-col justify-center">
        <span class="text-gold font-bold text-[11px] uppercase tracking-[0.4em] mb-4">DIRECTIONS</span>
        <h3 class="text-4xl md:text-5xl font-playfair font-bold italic mb-8">El Corazón del Cusco Imperial</h3>
        <p class="text-stone-400 text-lg leading-loose mb-12 font-outfit">
            <?= $lang === 'es' ? $hotelInfoES : $hotelInfoEN ?>
        </p>
        <div>
            <a href="/hotel/contacto" class="inline-block bg-gold text-white px-10 py-4 rounded-full font-bold uppercase tracking-widest text-[10px] hover:bg-white hover:text-gold transition-all">
                <?= $lang === 'es' ? 'Ver Ubicación' : 'View Location' ?>
            </a>
        </div>
    </div>
    <div class="lg:w-1/2 h-[500px] lg:h-auto overflow-hidden">
        <img src="/hotel/public/uploads/webp/portada-85b47.webp" class="w-full h-full object-cover transition-all duration-1000 transform hover:scale-110" alt="Cusco View">
    </div>
</section>

<!-- 5. SECCIÓN FACILITIES (Fondo Sutil) -->
<section class="py-32 bg-stone-50">
    <div class="container mx-auto px-6">
        <div class="text-center mb-24">
            <span class="text-stone-400 font-bold text-[11px] uppercase tracking-[0.4em] block mb-4">GUEST SERVICES</span>
            <h2 class="text-4xl md:text-6xl font-playfair font-bold text-stone-900 italic">Comodidades de Clase Mundial</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-16 md:gap-y-24">
            <div class="facility-item space-y-6 text-center reveal-up">
                <i class="fa-solid fa-wifi text-gold"></i>
                <h4 class="font-bold text-sm uppercase tracking-widest text-stone-800">Free High-Speed WiFi</h4>
                <p class="text-stone-400 leading-relaxed px-10">Conexión dedicada de alta velocidad.</p>
            </div>
            <div class="facility-item space-y-6 text-center reveal-up" style="animation-delay: 0.1s">
                <i class="fa-solid fa-mug-hot text-gold"></i>
                <h4 class="font-bold text-sm uppercase tracking-widest text-stone-800">Regional Breakfast</h4>
                <p class="text-stone-400 leading-relaxed px-10">Sabores auténticos de los Andes.</p>
            </div>
            <div class="facility-item space-y-6 text-center reveal-up" style="animation-delay: 0.2s">
                <i class="fa-solid fa-van-shuttle text-gold"></i>
                <h4 class="font-bold text-sm uppercase tracking-widest text-stone-800">Airport Shuttle</h4>
                <p class="text-stone-400 leading-relaxed px-10">Traslados puntuales y seguros.</p>
            </div>
            <div class="facility-item space-y-6 text-center reveal-up" style="animation-delay: 0.3s">
                <i class="fa-solid fa-clock text-gold"></i>
                <h4 class="font-bold text-sm uppercase tracking-widest text-stone-800">24/7 Front Desk</h4>
                <p class="text-stone-400 leading-relaxed px-10">Atención multilingüe las 24 horas.</p>
            </div>
            <div class="facility-item space-y-6 text-center reveal-up" style="animation-delay: 0.4s">
                <i class="fa-solid fa-suitcase text-gold"></i>
                <h4 class="font-bold text-sm uppercase tracking-widest text-stone-800">Luggage Assist</h4>
                <p class="text-stone-400 leading-relaxed px-10">Asistencia con su equipaje técnica.</p>
            </div>
            <div class="facility-item space-y-6 text-center reveal-up" style="animation-delay: 0.5s">
                <i class="fa-solid fa-snowflake text-gold"></i>
                <h4 class="font-bold text-sm uppercase tracking-widest text-stone-800">Cozy Heating</h4>
                <p class="text-stone-400 leading-relaxed px-10">Calidez total en cada estancia.</p>
            </div>
        </div>
    </div>
</section>

<!-- 6. SECCIÓN BLOG (Rediseño con Etiquetas sobre Imagen) -->
<section class="py-32 bg-white">
    <div class="container mx-auto px-6">
        <div class="text-center mb-20">
            <span class="text-stone-400 font-bold text-[11px] uppercase tracking-[0.4em] block mb-4">CITY INSIGHTS</span>
            <h2 class="text-5xl font-playfair font-bold text-stone-900 italic">Historias de la Ciudad</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- Blog Card 1 -->
            <div class="blog-card-refined group">
                <div class="relative overflow-hidden h-72 mb-6">
                    <div class="blog-tag-overlay">Cultura</div>
                    <img src="/hotel/public/uploads/webp/portada-85b47.webp" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Blog 1">
                </div>
                <div class="px-2 pb-4">
                    <span class="text-[10px] text-stone-400 font-bold uppercase tracking-widest block mb-2">20 Marzo, 2026</span>
                    <h4 class="text-xl font-playfair font-bold group-hover:text-gold transition-colors mb-4 line-clamp-2">Secretos de la Catedral del Cusco</h4>
                    <p class="text-stone-500 text-sm leading-loose mb-6 line-clamp-3">Descubre los tesoros ocultos y la historia que guardan los muros de la catedral...</p>
                    <a href="#" class="inline-block border-b-2 border-gold pb-1 text-[10px] font-bold tracking-widest uppercase hover:text-gold transition-all">Leer más</a>
                </div>
            </div>
            <!-- Blog Card 2 -->
             <div class="blog-card-refined group">
                <div class="relative overflow-hidden h-72 mb-6">
                    <div class="blog-tag-overlay">Gastronomía</div>
                    <img src="/hotel/public/uploads/webp/portada-85b47.webp" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Blog 2">
                </div>
                <div class="px-2 pb-4">
                    <span class="text-[10px] text-stone-400 font-bold uppercase tracking-widest block mb-2">18 Marzo, 2026</span>
                    <h4 class="text-xl font-playfair font-bold group-hover:text-gold transition-colors mb-4 line-clamp-2">La Ruta del Café en el Valle Sagrado</h4>
                    <p class="text-stone-500 text-sm leading-loose mb-6 line-clamp-3">Un viaje sensorial por los mejores granos de altura producidos en nuestras tierras...</p>
                    <a href="#" class="inline-block border-b-2 border-gold pb-1 text-[10px] font-bold tracking-widest uppercase hover:text-gold transition-all">Leer más</a>
                </div>
            </div>
            <!-- Blog Card 3 -->
             <div class="blog-card-refined group">
                <div class="relative overflow-hidden h-72 mb-6">
                    <div class="blog-tag-overlay">Aventura</div>
                    <img src="/hotel/public/uploads/webp/portada-85b47.webp" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" alt="Blog 3">
                </div>
                <div class="px-2 pb-4">
                    <span class="text-[10px] text-stone-400 font-bold uppercase tracking-widest block mb-2">15 Marzo, 2026</span>
                    <h4 class="text-xl font-playfair font-bold group-hover:text-gold transition-colors mb-4 line-clamp-2">Caminatas Alternativas a Machu Picchu</h4>
                    <p class="text-stone-500 text-sm leading-loose mb-6 line-clamp-3">Explora senderos menos transitados y asómbrate con paisajes naturales vírgenes...</p>
                    <a href="#" class="inline-block border-b-2 border-gold pb-1 text-[10px] font-bold tracking-widest uppercase hover:text-gold transition-all">Leer más</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once THEME_PATH . '/layout/footer.php'; ?>
